<?php

/*
 * @copyright   2018 Mautic Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://www.mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Tests\Callback;


use Mautic\SmsBundle\Helper\ContactHelper;
use MauticPlugin\MauticSlooceTransportBundle\Callback\SlooceCallback;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SlooceCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContactHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contactHelper;


    protected function setUp()
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