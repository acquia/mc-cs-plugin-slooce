<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Message;

use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;

/**
 * Class Message.
 *
 * @example
 * <message id="abcdef123">
 * <partnerpassword>jTUWufdis</partnerpassword>
 * <content>Correct! Mount Everest is the tallest peak in the world.</content>
 * </message>
 */
abstract class AbstractMessage
{
    /**
     * Part of the message that should contain the password.
     */
    public const PASSWORD_ELEMENT = 'partnerpassword';

    /**
     * @var string
     */
    private $apiEncoding = 'ISO-8859-1';

    /**
     * @var string
     */
    private $messageId = null;

    /**
     * @var string
     */
    private $partnerPassword = null;

    /**
     * Message constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->messageId = $id;
    }

    /**
     * @param null $partnerPassword
     */
    public function setPartnerPassword($partnerPassword): AbstractMessage
    {
        $this->partnerPassword = $partnerPassword;

        return $this;
    }

    abstract public function getSerializable(): array;

    /**
     * @throws InvalidMessageArgumentsException
     */
    public function getXML(): string
    {
        if (is_null($this->getMessageId())) {
            $this->generateMessageId();
        }

        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        $xml            = new \DOMDocument('1.0', $this->apiEncoding);
        $messageElement = $xml->createElement('message');
        $messageElement->setAttribute('id', $this->messageId);

        $serializable = $this->getSerializable();

        if (!is_null($this->partnerPassword) && !array_key_exists(self::PASSWORD_ELEMENT, $serializable)) {
            $passwordElement = $xml->createElement(self::PASSWORD_ELEMENT, $this->partnerPassword);
            $messageElement->appendChild($passwordElement);
        } else {
            throw new InvalidMessageArgumentsException('No password set');
        }

        foreach ($serializable as $elementName => $elementValue) {
            $elementValue = $this->encodeStringToProviderEncoding($elementValue);
            $xmlElement   = $xml->createElement($elementName, $elementValue);
            $messageElement->appendChild($xmlElement);
        }

        $xml->appendChild($messageElement);

        return $xml->saveXML();
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): AbstractMessage
    {
        $this->messageId = $messageId;

        return $this;
    }

    protected function generateMessageId(): AbstractMessage
    {
        $this->setMessageId('slooce-'.date('Ymd-Hims').'-'.substr(sha1(microtime()), 0, 5));

        return $this;
    }

    public function getSanitizedArray(): array
    {
        $serializable = $this->getSerializable();
        if (is_null($serializable)) {
            return [];
        }

        $output = [];
        foreach ($serializable as $key => $value) {
            if (self::PASSWORD_ELEMENT == $key || $value == $this->partnerPassword) {
                continue;
            }
            $output[] = sprintf("%s='%s'", $key, mb_convert_encoding($value, 'UTF-8', $this->apiEncoding));
        }

        return $output;
    }

    public function encodeStringToProviderEncoding(string $message): string
    {
        return mb_convert_encoding($message, 'UTF-8', $this->apiEncoding);
    }
}
