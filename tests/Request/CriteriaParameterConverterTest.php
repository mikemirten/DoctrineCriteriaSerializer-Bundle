<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request;

use Doctrine\Common\Collections\Criteria;
use Mikemirten\Component\DoctrineCriteriaSerializer\CriteriaDeserializer;
use Mikemirten\Component\DoctrineCriteriaSerializer\DeserializationContext;
use Mikemirten\Component\DoctrineCriteriaSerializer\Exception\InvalidQueryException;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class CriteriaParameterConverterTest extends TestCase
{
    public function testApplyEmpty()
    {
        $request       = $this->createMock(Request::class);
        $configuration = $this->createMock(ParamConverter::class);
        $deserializer  = $this->createMock(CriteriaDeserializer::class);

        $request->method('getQueryString')
            ->willReturn(null);

        $configuration->method('getName')
            ->willReturn('testCriteria');

        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes->expects($this->once())
            ->method('set')
            ->with('testCriteria', $this->isInstanceOf(Criteria::class));

        $deserializer->expects($this->never())
            ->method('deserialize');

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->apply($request, $configuration);
    }

    public function testApply()
    {
        $request       = $this->createMock(Request::class);
        $configuration = $this->createMock(ParamConverter::class);
        $deserializer  = $this->createMock(CriteriaDeserializer::class);

        $request->method('getQueryString')
            ->willReturn('filter[test]=1');

        $configuration->method('getName')
            ->willReturn('testCriteria');

        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes->expects($this->once())
            ->method('set')
            ->with(
                'testCriteria',
                $this->isInstanceOf(Criteria::class)
            );

        $deserializer->expects($this->once())
            ->method('deserialize')
            ->with(
                'filter[test]=1',
                $this->isInstanceOf(Criteria::class)
            );

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testInvalidQueryExceptionHandling()
    {
        $request       = $this->createMock(Request::class);
        $configuration = $this->createMock(ParamConverter::class);
        $deserializer  = $this->createMock(CriteriaDeserializer::class);

        $request->method('getQueryString')
            ->willReturn('filter[]=1');

        $deserializer->method('deserialize')
            ->willThrowException(new InvalidQueryException('Invalid query'));

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->apply($request, $configuration);
    }

    public function testSupports()
    {
        $deserializer  = $this->createMock(CriteriaDeserializer::class);
        $configuration = $this->createMock(ParamConverter::class);

        $configuration->method('getClass')
            ->willReturn(Criteria::class);

        $converter = new CriteriaParameterConverter($deserializer);

        $this->assertTrue($converter->supports($configuration));
    }

    public function testSupportsNegative()
    {
        $deserializer  = $this->createMock(CriteriaDeserializer::class);
        $configuration = $this->createMock(ParamConverter::class);

        $configuration->method('getClass')
            ->willReturn('ArrayObject');

        $converter = new CriteriaParameterConverter($deserializer);

        $this->assertFalse($converter->supports($configuration));
    }

    /**
     * @depends testApply
     */
    public function testApplyWithContext()
    {
        $request       = $this->createMock(Request::class);
        $configuration = $this->createMock(ParamConverter::class);
        $deserializer  = $this->createMock(CriteriaDeserializer::class);
        $context       = $this->createMock(DeserializationContext::class);

        $request->method('getQueryString')
            ->willReturn('filter[test]=1');

        $request->attributes = $this->createMock(ParameterBag::class);

        $deserializer->expects($this->once())
            ->method('deserialize')
            ->with(
                'filter[test]=1',
                $this->isInstanceOf(Criteria::class),
                $context
            );

        $configuration->method('getOptions')
            ->willReturn(['context' => 'testContext']);

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->registerContext('testContext', $context);
        $converter->apply($request, $configuration);
    }

    /**
     * @depends testApply
     * @expectedException \LogicException
     */
    public function testApplyWithUnknownContext()
    {
        $request       = $this->createMock(Request::class);
        $configuration = $this->createMock(ParamConverter::class);
        $deserializer  = $this->createMock(CriteriaDeserializer::class);

        $request->method('getQueryString')
            ->willReturn('filter[test]=1');

        $request->attributes = $this->createMock(ParameterBag::class);

        $deserializer->expects($this->never())
            ->method('deserialize');

        $configuration->method('getOptions')
            ->willReturn(['context' => 'testContext']);

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->apply($request, $configuration);
    }
}