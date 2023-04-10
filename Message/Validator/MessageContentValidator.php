<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Message\Validator;

use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;

/**
 * Class MessageContentValidator.
 *
 * It may be worth creating a service but right now I choose singleton
 */
class MessageContentValidator
{
    public const MAX_CONTENT_LENGTH = 160;
    public const VALID_CHARACTERS   = 'A-Za-z0-9\ @$_\/.,\'"():;\-=+*&%#!\\?<>';

    /**
     * @throws InvalidMessageArgumentsException
     */
    public static function validate(MtMessage $message): MtMessage
    {
        if (is_null($message->getKeyword())) {
            throw new InvalidMessageArgumentsException('Message has no keyword set.');
        }

        $content = $message->encodeStringToProviderEncoding($message->getContent());

        if (mb_strlen($content) > self::MAX_CONTENT_LENGTH) {
            throw new InvalidMessageArgumentsException('Message content is too long. Maximum is '.self::MAX_CONTENT_LENGTH.' characters');
        }

        return $message;
    }
}
