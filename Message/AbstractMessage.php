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

namespace MauticPlugin\MauticSlooceTransportBundle\Message;

/**
 * Class Message
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Slooce
 *
 *
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
     * @var string
     */
    private $apiEncoding     = 'ISO-8859-1';

    /**
     * @var string
     */
    private $messageId       = null;

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
     * @return null
     */
    public function getPartnerPassword()
    : string

    {
        return $this->partnerPassword;
    }

    /**
     * @param null $partnerPassword
     *
     * @return AbstractMessage
     */
    public function setPartnerPassword($partnerPassword)
    : AbstractMessage
    {
        $this->partnerPassword = $partnerPassword;
        return $this;
    }

    abstract public function getSerializable();

    public function getXML() {
        $xml = new \DOMDocument('1.0', $this->apiEncoding);
        $messageElement = $xml->createElement('message');
        $messageElement->setAttribute('id', $this->messageId);

        $serializable = $this->getSerializable();
        foreach ($serializable as $elementName=>$elementValue) {
            $elementValue = mb_convert_encoding($elementValue, 'UTF-8', $this->apiEncoding);
            $xmlElement = $xml->createElement($elementName, $elementValue);
            $messageElement->appendChild($xmlElement);
        }

        var_dump($xml->saveXML());
    }

    /**
     * @return string
     */
    public function getMessageId()
    : string
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     *
     * @return AbstractMessage
     */
    public function setMessageId(string $messageId)
    : AbstractMessage
    {
        $this->messageId = $messageId;
        return $this;
    }


}