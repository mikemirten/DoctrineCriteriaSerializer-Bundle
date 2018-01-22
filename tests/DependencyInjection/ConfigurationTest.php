<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\DependencyInjection;

use Mikemirten\Component\DoctrineCriteriaSerializer\CriteriaDeserializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationTest extends TestCase
{
    /**
     * Integration test of dependency-injection container:
     *
     * 1. It compiles with no errors.
     * 2. It is able to initialize and provide dependencies with no errors.
     * 3. The dependencies are instances of expected types.
     *
     * @dataProvider getConfiguration
     *
     * @param array  $configuration
     * @param string $environment
     */
    public function testConfiguration(string $environment)
    {
        $builder = new ContainerBuilder();
        $builder->setParameter('kernel.cache_dir', '/tmp');
        $builder->setParameter('kernel.environment', $environment);

        $builder->registerExtension(new SerializerExtension());
        $builder->loadFromExtension(SerializerExtension::ALIAS);

        $builder->compile();

        $this->assertInstanceOf(
            CriteriaDeserializer::class,
            $builder->get('mrtn_doctrine_criteria.deserializer')
        );
    }

    /**
     * Get testing configuration
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return [['dev'], ['prod']];
    }
}