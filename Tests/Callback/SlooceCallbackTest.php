<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Tests\Callback;

use Mautic\SmsBundle\Helper\ContactHelper;
use MauticPlugin\MauticSlooceTransportBundle\Callback\SlooceCallback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SlooceCallbackTest extends TestCase
{
    /**
     * @var ContactHelper|MockObject
     */
    private $contactHelper;

    protected function setUp(): void
    {
        $this->contactHelper = $this->createMock(ContactHelper::class);
    }

    public function testMissingUserThrowsBadRequestException()
    {
        $this->expectException(BadRequestHttpException::class);

        $parameterBag     = $this->createMock(ParameterBag::class);
        $request          = $this->createMock(Request::class);
        $request->request = $parameterBag;

        $parameterBag->method('get')
            ->withConsecutive(['@id'], ['user'])
            ->willReturn('123', '');

        $this->getCallback()->getMessage($request);
    }

    public function testMissingContentThrowsBadRequestException()
    {
        $this->expectException(BadRequestHttpException::class);

        $parameterBag     = $this->createMock(ParameterBag::class);
        $request          = $this->createMock(Request::class);
        $request->request = $parameterBag;

        $parameterBag->method('get')
            ->withConsecutive(['@id'], ['user'], ['content'])
            ->willReturn('123', '321', '');

        $this->getCallback()->getMessage($request);
    }

    public function testMissingIdThrowsBadRequestException()
    {
        $this->expectException(BadRequestHttpException::class);

        $parameterBag     = $this->createMock(ParameterBag::class);
        $request          = $this->createMock(Request::class);
        $request->request = $parameterBag;

        $parameterBag->method('get')
            ->withConsecutive(['@id'])
            ->willReturn('');

        $this->getCallback()->getMessage($request);
    }

    public function testMessageIsReturned()
    {
        $parameterBag = $this->createMock(ParameterBag::class);
        $request      = $this->createMock(Request::class);
        $request->method('get')
            ->willReturn('Hello');
        $request->request = $parameterBag;

        $parameterBag->method('get')
            ->withConsecutive(['@id'], ['user'], ['content'])
            ->willReturn('123', '321', 'Hello');

        $this->assertEquals('Hello', $this->getCallback()->getMessage($request));
    }

    /**
     * @return SlooceCallback
     */
    private function getCallback()
    {
        return new SlooceCallback($this->contactHelper);
    }
}
