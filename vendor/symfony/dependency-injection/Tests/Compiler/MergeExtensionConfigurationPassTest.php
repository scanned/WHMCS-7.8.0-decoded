<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Tests\Compiler;

use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
class MergeExtensionConfigurationPassTest extends \PHPUnit_Framework_TestCase
{
    public function testExpressionLanguageProviderForwarding()
    {
        $tmpProviders = array();
        $extension = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\Extension\\ExtensionInterface')->getMock();
        $extension->expects($this->any())->method('getXsdValidationBasePath')->will($this->returnValue(false));
        $extension->expects($this->any())->method('getNamespace')->will($this->returnValue('http://example.org/schema/dic/foo'));
        $extension->expects($this->any())->method('getAlias')->will($this->returnValue('foo'));
        $extension->expects($this->once())->method('load')->will($this->returnCallback(function (array $config, ContainerBuilder $container) use(&$tmpProviders) {
            $tmpProviders = $container->getExpressionLanguageProviders();
        }));
        $provider = $this->getMockBuilder('Symfony\\Component\\ExpressionLanguage\\ExpressionFunctionProviderInterface')->getMock();
        $container = new ContainerBuilder(new ParameterBag());
        $container->registerExtension($extension);
        $container->prependExtensionConfig('foo', array('bar' => true));
        $container->addExpressionLanguageProvider($provider);
        $pass = new MergeExtensionConfigurationPass();
        $pass->process($container);
        $this->assertEquals(array($provider), $tmpProviders);
    }
}

?>