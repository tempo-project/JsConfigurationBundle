<?php

/*
* This file is part of the Tempo-project package http://tempo-project.org/>.
*
* (c) Mlanawo Mbechezi  <mlanawo.mbechezi@ikimea.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Tempo\Bundle\JsConfigurationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

class CompileTemplateCommand extends ContainerAwareCommand
{
    private $finder;
    private $targetPath;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('tempo:js-configuration:compile-template')
            ->setDefinition(array(
                new InputArgument(
                    'target',
                    InputArgument::OPTIONAL,
                    'Override the target directory to dump JS configuration files in.'
                ),
            ))
            ->addOption('watch', null, InputOption::VALUE_NONE, 'Check for changes every second, debug mode only')
            ->setDescription('Compile js');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->targetPath = $input->getArgument('target') ?: $this->getDefaultTargetPath();

        $this->finder = new Finder();
        $this->finder->files()->in($this->targetPath);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = $this->getContainer()->get('filesystem');

        if ($input->getOption('watch')) {
            return $this->watch( $this->targetPath, $output, $this->getContainer()->getParameter('kernel.debug'));
        }

        $this->dumpAsset($this->targetPath, $output);
    }

    public function dumpAsset($targetPath, $output)
    {
        $jsTemplate = 'var JST={};'. "\n";

        foreach ($this->finder as $file) {
            $content  = file_get_contents($file->getRealpath());
            $content = trim(preg_replace("/\r|\n|\r\n/", '', $content));
            $content = str_replace("'", "\'", $content);
            $jsTemplate.= 'JST["'.$file->getRelativePathname().'"] = \''.$content.'\';'. "\n";
        }

        $file = $this->getContainer()->getParameter('tempo_js_configuration.template_compile');
        $file = str_replace('/app/..', '', $file);

        file_put_contents($file, $jsTemplate);

        $output->writeln(sprintf(
            'Dump file in <comment>%s</comment> directory',
            $file
        ));
    }

    public function watch($targetPath, $output, $debug = false)
    {

        if (!$debug) {
            throw new \RuntimeException('The --watch option is only available in debug mode.');
        }

        $error = '';

        $cache = sys_get_temp_dir().'/tempo_template_compile_watch_'.substr(sha1($targetPath), 0, 7);

        if (!file_exists($cache)) {
            $previously = array();
        } else {
            $previously = unserialize(file_get_contents($cache));
            if (!is_array($previously)) {
                $previously = array();
            }
        }

        while (true) {
            try {
                foreach ($this->finder as $file) {
                    if ($this->checkAsset($file, $previously)) {
                        $this->dumpAsset($targetPath, $output);
                    }
                }

                file_put_contents($cache, serialize($previously));
                $error = '';

            } catch(\Exception $e) {
                if ($error != $msg = $e->getMessage()) {
                    $output->writeln('<error>[error]</error> '.$msg);
                    $error = $msg;
                }
            }
        }
    }

    /**
     * @return string Default target path
     */
    private function getDefaultTargetPath()
    {
        return str_replace('/app/..', '', $this->getContainer()->getParameter('tempo_js_configuration.folder_js_compile'));
    }

    public function checkAsset($file, array &$previously)
    {
        $name =  $file->getRelativePathname();
        $mtime =  filemtime($file->getPathName());

        if (isset($previously[$name])) {
            $changed = $previously[$name] != $mtime;
        } else {
            $changed = true;
        }
        $previously[$name] = $mtime;

        return $changed;
    }

}