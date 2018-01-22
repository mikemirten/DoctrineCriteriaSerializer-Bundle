<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request;

use Doctrine\Common\Collections\Criteria;
use Mikemirten\Component\DoctrineCriteriaSerializer\CriteriaDeserializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * CriteriaParameterConverter constructor.
     *
     * @param CriteriaDeserializer $deserializer
     */
    public function __construct(CriteriaDeserializer $deserializer)
    {
        $this->deserializer = $deserializer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $criteria = $this->createCriteria($request);

        $request->attributes->set($configuration->getName(), $criteria);
        return true;
    }

    /**
     * Create criteria by request
     *
     * @param  Request $request
     * @return Criteria
     * @throws BadRequestHttpException
     */
    protected function createCriteria(Request $request): Criteria
    {
        $query = $request->getQueryString();

        if ($query === null) {
            return Criteria::create();
        }

        $deserializer = new KatharsisQueryDeserializer();

        try {
            return $deserializer->deserialize($query);
        }
        catch (InvalidQueryException $exception) {
            throw new BadRequestHttpException('Query deserialization error: ' . $exception->getMessage(), $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Criteria::class;
    }
}