<?php

/*
* This file is part of the Tempo-project package http://tempo.ikimea.com/>.
*
* (c) Mlanawo Mbechezi  <mlanawo.mbechezi@ikimea.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


namespace Tempo\Bundle\JsConfigurationBundle\Tests\Dumper;

use Symfony\Component\Filesystem\Filesystem;
use Tempo\Bundle\JsConfigurationBundle\Dumper\ConfigurationDumper;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;


class ConfigurationDumperTest extends \PHPUnit_Framework_TestCase
{
    protected $dumper;

    public function setUp()
    {

        $env = 'test_env';
        $debug = true;
        $kernel = new KernelForTest($env, $debug);

        $this->dumper = new ConfigurationDumper($kernel, new Filesystem, array());
    }

    public function testResolveConfig()
    {
        $base = array('foo' => array('bar' => array('foobar' => 'magicbox')));
        $str  = 'foo.bar.foobar';

        $this->assertEquals($this->dumper->resolveConfig($str, $base), 'magicbox');
    }

    public function testResolveConfig2()
    {
        $base = array('foo' => array('bar' => array('foobar' => 'magicbox')));
        $str  = 'foo.bar';

        $this->assertEquals($this->dumper->resolveConfig($str, $base), array('foobar' => 'magicbox'));
    }
}