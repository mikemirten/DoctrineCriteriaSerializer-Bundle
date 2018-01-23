<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request;

use Doctrine\Common\Collections\Criteria;
use Mikemirten\Component\DoctrineCriteriaSerializer\Context\DummyDeserializationContext;
use Mikemirten\Component\DoctrineCriteriaSerializer\CriteriaDeserializer;
use Mikemirten\Component\DoctrineCriteriaSerializer\DeserializationContext as Context;
use Mikemirten\Component\DoctrineCriteriaSerializer\Exception\InvalidQueryException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Criteria parameter converter
 * Converts request query into a Doctrine Collection's criteria.
 *
 * @package Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request
 */
class CriteriaParameterConverter implements ParamConverterInterface
{
    /**
     * @var CriteriaDeserializer
     */
    protected $deserializer;

    /**
     * @var Context[]
     */
    protected $contexts = [];

    /**
     * CriteriaParameterConverter constructor.
     *
     * @param CriteriaDeserializer $deserializer
     */
    public function __construct(CriteriaDeserializer $deserializer)
    {
        $this->deserializer = $deserializer;
    }

    /**
     * Register deserialization context
     *
     * @param string  $name
     * @param Context $context
     */
    public function registerContext(string $name, Context $context): void
    {
        $this->contexts[$name] = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $query = $request->getQueryString();

        if ($query === null || trim($query) === '') {
            $request->attributes->set(
                $configuration->getName(),
                Criteria::create()
            );

            return true;
        }

        $context  = $this->getContext($configuration);
        $criteria = $this->createCriteria($query, $context);

        $request->attributes->set($configuration->getName(), $criteria);
        return true;
    }

    /**
     * Create criteria by request
     *
     * @param  string  $query
     * @param  Context $context
     * @return Criteria
     * @throws BadRequestHttpException
     */
    protected function createCriteria(string $query, Context $context): Criteria
    {
        $criteria = Criteria::create();

        try {
            $this->deserializer->deserialize($query, $criteria, $context);
        }
        catch (InvalidQueryException $exception) {
            throw new BadRequestHttpException('Query deserialization error: ' . $exception->getMessage(), $exception);
        }

        return $criteria;
    }

    /**
     * Get deserialization context
     *
     * @param  ParamConverter $configuration
     * @return Context
     */
    protected function getContext(ParamConverter $configuration): Context
    {
        $options = $configuration->getOptions();

        if (! isset($options['context'])) {
            return new DummyDeserializationContext();
        }

        $name = trim($options['context']);

        if (! isset($this->contexts[$name])) {
            throw new \LogicException(sprintf('Deserialization context named by "%s" not found.', $name));
        }

        return $this->contexts[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Criteria::class;
    }
}