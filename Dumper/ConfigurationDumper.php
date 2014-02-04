<?php

/*
* This file is part of the Tempo-project package http://tempo.ikimea.com/>.
*
* (c) Mlanawo Mbechezi  <mlanawo.mbechezi@ikimea.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


namespace Tempo\Bundle\JsConfigurationBundle\Dumper;

use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;


class ConfigurationDumper
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Default constructor.
     * @param KernelInterface   $kernel            The kernel.
     * @param FileSystem        $filesystem        The file system.
     * @param array             $configToExpose Some parameter names to expose.
     */
    public function __construct(KernelInterface $kernel, Filesystem $filesystem, array $configToExpose = array())
    {
        $this->kernel     = $kernel;
        $this->filesystem = $filesystem;
        $this->configToExpose = $configToExpose;
    }

    /**
     * @param $targetPath
     * @return int
     */
    public function dump($targetPath)
    {
        $dataConfig = array();
        $configuration = $this->getConfiguration();

        $returnConfig = $this->configToExpose;
        foreach($this->configToExpose as $alias) {
            $dataConfig[$alias]  = $this->resolveConfig($alias, $configuration);
        }
        $callback = 'Tempo.Configuration.setData';

        $dataConfig = $this->kernel->getContainer()->get('tempo.jsconfiguration.serializer')->serialize( $dataConfig, 'json' );

        $content = sprintf("%s(%s);", $callback, $dataConfig);

        return file_put_contents($targetPath . '/tempo_configuration.js', $content);
    }

    public function resolveConfig($haystack , $needle)
    {
        foreach (explode('.', $haystack) as $e) {
            if(isset($needle[$e])) {
                $needle = $needle[$e];
            }
        }

        return $needle;
    }

    public function getConfiguration()
    {
        $configurations = '';
        $containerBuilder = $this->getContainerBuilder();
        $bundles = $this->kernel->getContainer()->get('kernel')->getBundles();
        $dumper = new YamlReferenceDumper();

        foreach ($bundles as $bundle) {
            $extension = $bundle->getContainerExtension();

            if($extension) {

                $configuration = $extension->getConfiguration(array(), $containerBuilder);
                if(is_object($configuration)) {
                    $configurations.= $dumper->dump($configuration, $extension->getNamespace());
                }
            }
            $extension = null;
        }

        return Yaml::parse($configurations);
    }

    protected function getContainerBuilder()
    {
        if (!is_file($cachedFile = $this->kernel->getContainer()->getParameter('debug.container.dump'))) {
            throw new \LogicException(sprintf('Debug information about the container could not be found. Please clear the cache and try again.'));
        }

        $container = new ContainerBuilder();

        $loader = new XmlFileLoader($container, new FileLocator());
        $loader->load($cachedFile);

        return $container;
    }
}