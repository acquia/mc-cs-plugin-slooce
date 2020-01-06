<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

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
