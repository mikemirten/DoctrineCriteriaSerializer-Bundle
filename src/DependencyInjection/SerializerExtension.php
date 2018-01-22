<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SerializerExtension extends Extension
{
    const ALIAS = 'mrtn_doctrine_criteria_serializer';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return self::ALIAS;
    }
}