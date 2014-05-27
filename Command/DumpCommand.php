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

class DumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('tempo:js-configuration:dump')
            ->setDefinition(array(
                new InputArgument(
                    'target',
                    InputArgument::OPTIONAL,
                    'Override the target directory to dump JS configuration files in.'
                ),
            ))
            ->setDescription('Dumps all JS configuration files to the filesystem');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = $this->getContainer()->get('filesystem');
        $targetPath = $input->getArgument('target') ? : $this->getDefaultTargetPath();

        if (!$fs->exists($targetPath)) {
            $output->writeln(sprintf(
                '<info>[dir+]</info> %s',
                $targetPath
            ));
            $fs->mkdir($targetPath);
        }

        $output->writeln(sprintf(
            'Installing configuration files in <comment>%s</comment> directory',
            $targetPath
        ));

        $this
            ->getContainer()
            ->get('tempo.jsconfiguration.dumper')
            ->dump($targetPath);
    }

    /**
     * @return string   Default target path
     */
    private function getDefaultTargetPath()
    {
        return str_replace(
            'app',
            'web/js',
            $this->getContainer()->getParameter('kernel.root_dir')
        );
    }
}
