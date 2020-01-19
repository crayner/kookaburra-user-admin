<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 5/10/2019
 * Time: 18:14
 */
namespace Kookaburra\UserAdmin\DependencyInjection;

use Kookaburra\UserAdmin\Manager\PersonNameManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class KookaburraUserAdminExtension
 * @package Kookaburra\SystemAdmin\DependencyInjection
 */
class KookaburraUserAdminExtension extends Extension
{
    /**
     * load
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new YamlFileLoader(
            $container,
            $locator
        );
        $loader->load('services.yaml');

        if ($container->has(PersonNameManager::class))
        {
            $container
                ->getDefinition(PersonNameManager::class)
                ->addMethodCall('setFormats', [isset($config['name_formats']) ? $config['name_formats'] : $this->getNameFormatsDefault()])
            ;
        }
    }

    /**
     * getNameFormatsDefault
     * @return array
     */
    private function getNameFormatsDefault(): array
    {
        return Yaml::parse("
staff:
    first:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, first'
            normal: 'first surname'
    preferred:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, first'
            normal: 'first surname'
    formal: 'title first surname'
parent:
    first:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, first'
            normal: 'first surname'
    preferred:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, preferred'
            normal: 'preferred surname'
    formal: 'title first surname'
other:
    first:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, first'
            normal: 'first surname'
    preferred:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, preferred'
            normal: 'preferred surname'
    formal: 'title first surname'
student:
    first:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, first'
            normal: 'first surname'
    preferred:
        short:
            reversed: 'surname, initial'
            normal: 'initial surname'
        long:
            reversed: 'surname, preferred'
            normal: 'preferred surname'
    formal: 'first surname'
");
    }
}