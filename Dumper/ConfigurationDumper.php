<?php

/*
* This file is part of the Tempo-project package http://tempo-project.org/>.
*
* (c) Mlanawo Mbechezi  <mlanawo.mbechezi@ikimea.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


namespace Tempo\Bundle\JsConfigurationBundle\Dumper;

use Symfony\Component\Serializer\Serializer;

class ConfigurationDumper
{
    protected $serializer;
    protected $parameterBag;
    protected $configToExpose;

    /**
     * Default constructor.
     *
     * @param Serializer $serializer
     * @param array $parameterBag
     * @param array $configToExpose
     */
    public function __construct(Serializer $serializer, array $parameterBag, array $configToExpose = array())
    {
        $this->serializer     = $serializer;
        $this->parameterBag   = $parameterBag;
        $this->configToExpose = $configToExpose;
    }

    /**
     * @param $targetPath
     * @return string\boolean
     */
    public function dump($targetPath)
    {
        $dataConfig = array();

        foreach($this->configToExpose as $alias) {
            if(isset($this->parameterBag[$alias])) {
                $dataConfig[$alias] = $this->parameterBag[$alias];
            }
        }
        $callback = 'Tempo.Configuration.setData';

        $dataConfig = $this->serializer->serialize( $dataConfig, 'json' );

        $content = sprintf("%s(%s);", $callback, $dataConfig);

        return file_put_contents($targetPath . '/tempo_configuration.js', $content);
    }
}