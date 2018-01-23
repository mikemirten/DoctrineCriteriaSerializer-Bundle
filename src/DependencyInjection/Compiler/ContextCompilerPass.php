<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Deserialization context compiler pass
 *
 * @package Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection\Compiler
 */
class ContextCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('mrtn_doctrine_criteria.parameter_converter');
        $extensions = $container->findTaggedServiceIds('mrtn_doctrine_criteria.deserialization_context');

        foreach ($extensions as $id => $tags)
        {
            foreach ($tags as $nm => $tag)
            {
                if (! isset($tag['alias'])) {
                    throw new \LogicException(sprintf(
                        'Tag #%s of service "%s" does not contains required attribute "alias".',
                        $nm,
                        $id
                    ));
                }

                $definition->addMethodCall('registerContext', [
                    $tag['alias'],
                    new Reference($id)
                ]);
            }
        }
    }
}