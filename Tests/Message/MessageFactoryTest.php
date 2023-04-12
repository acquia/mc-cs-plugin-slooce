<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Tests\Message;

use MauticPlugin\MauticSlooceTransportBundle\Message\MessageFactory;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;

class MessageFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $factory = new MessageFactory();
        $this->assertInstanceOf(MtMessage::class, $message = $factory->create('MTMessage', 'ohlala'));
        $this->assertEquals('ohlala', $message->getMessageId());
    }
}
