<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Message;

use MauticPlugin\MauticSlooceTransportBundle\Exception\MessageException;

/**
 * Class MessageFactory.
 */
class MessageFactory
{
    /**
     * @param string $type      type of message to create, currently only **MTMessage**
     * @param null   $messageId
     *
     * @throws MessageException
     */
    public function create($type = 'MTMessage', $messageId = null): AbstractMessage
    {
        switch ($type) {
            case 'MTMessage':
                return new MtMessage($messageId ?: uniqid('mautic'));
        }

        throw new MessageException('Unknown message type requested. '.$type);
    }
}
