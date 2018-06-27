<?php

declare(strict_types=1);
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

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
     * @return AbstractMessage
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
