<?php
declare(strict_types = 1);

namespace Mikemirten\Bundle\DoctrineCriteriaSerializerBundle\Request;

use Doctrine\Common\Collections\Criteria;
use Mikemirten\Component\DoctrineCriteriaSerializer\CriteriaDeserializer;
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
        $criteria      = $this->createMock(Criteria::class);

        $request->method('getQueryString')
            ->willReturn('filter[test]=1');

        $configuration->method('getName')
            ->willReturn('testCriteria');

        $request->attributes = $this->createMock(ParameterBag::class);

        $request->attributes->expects($this->once())
            ->method('set')
            ->with('testCriteria', $criteria);

        $deserializer->expects($this->once())
            ->method('deserialize')
            ->with('filter[test]=1')
            ->willReturn($criteria);

        $converter = new CriteriaParameterConverter($deserializer);
        $converter->apply($request, $configuration);
    }
}