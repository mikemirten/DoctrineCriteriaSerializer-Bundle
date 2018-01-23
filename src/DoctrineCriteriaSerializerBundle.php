<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle;

use Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection\Compiler\ContextCompilerPass;
use Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection\SerializerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineCriteriaSerializerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SerializerExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContextCompilerPass());
    }
}