<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle;

use Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request\SerializerExtension;
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

    }
}