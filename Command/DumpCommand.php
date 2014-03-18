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
use Symfony\Component\Console\Output\Output;


class DumpCommand extends ContainerAwareCommand
{
    private $targetPath;

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->targetPath = $input->getArgument('target') ?:
            realpath(sprintf('%s/../web/js', $this->getContainer()->getParameter('kernel.root_dir')));
    }

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
        if (!is_dir($dir = dirname($this->targetPath))) {
            $output->writeln('<info>[dir+]</info>  ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }

        $output->writeln(sprintf(
            'Installing configuration files in <comment>%s</comment> directory',
            $this->targetPath
        ));

        $this
            ->getContainer()
            ->get('tempo.jsconfiguration.dumper')
            ->dump($this->targetPath);
    }
}