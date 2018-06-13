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

namespace MauticPlugin\MauticSlooceTransportBundle\Slooce;

use MauticPlugin\MauticSlooceTransportBundle\Exception\ConnectorException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidRecipientException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SloocePluginException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SlooceServerException;
use MauticPlugin\MauticSlooceTransportBundle\Message\AbstractMessage;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;

/**
 * Class Connector
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Slooce
 */
class Connector
{
    /** @var string */
    private $apiDomain = "https://<sloocedomain>.sloocetech.net/slooce_apps";

    /** @var array */
    private $endpoints = [];

    /** @var string */
    private $slooceDomain;

    /** @var string */
    private $partnerId;

    /** @var string */
    private $password;

    /** @var string */
    private $shortCodeField;

    /**
     * Connector constructor.
     */
    public function __construct()
    {
        $this->slooceDomain = "samples.cloud";

        $this->endpoints = [
            'register'    => 'spi/<partnerid>/<user>/<keyword>/messages/start',
            'messageSend' => 'spi/<partnerid>/<user>/<keyword>/messages/mt',
        ];
    }

    /**
     * @param MtMessage $message
     *
     * @throws ConnectorException
     * @throws InvalidRecipientException
     * @throws SloocePluginException
     * @throws SlooceServerException
     */
    public function sendMtMessage(MtMessage $message)
    {
        $message->setPartnerPassword($this->password);

        $this->postMessage('messageSend', $message);
    }


    /**
     * @param $endpoint
     * @param MtMessage $message
     *
     * @return array
     * @throws ConnectorException
     * @throws InvalidRecipientException
     * @throws SloocePluginException
     * @throws SlooceServerException
     */
    private function postMessage($endpoint, MtMessage $message)
    {
        if (!isset($this->endpoints[$endpoint])) {
            throw new ConnectorException('Unknown endpoint ' . $endpoint
                . ', registered endpoints: ' . join(', ', array_keys($this->endpoints)));
        }

        if (is_null($this->slooceDomain) || is_null($this->partnerId)) {
            throw new ConnectorException('Configuration error.');
        }

        $apiDomain = str_replace('<sloocedomain>', $this->getSlooceDomain(), $this->apiDomain);

        $endpointURI = str_replace('<partnerid>', $this->getPartnerId(), $this->endpoints[$endpoint]);
        $endpointURI = str_replace('<user>', '1' . $message->getUserId(), $endpointURI);
        $endpointURI = str_replace('<keyword>', $message->getKeyword(), $endpointURI);
        $apiURL = $apiDomain . "/" . $endpointURI;

        $payload = $message->getXML();

        $ch = curl_init();

        $headers = ["Accept: application/xml", "Content-Type: application/xml"];

        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);

        $response = $this->handleResponse($ch, $data, $message);

        return $response;
    }

    /**
     * @param $curlHandler
     * @param $data
     * @param AbstractMessage $message
     *
     * @throws InvalidRecipientException
     * @throws SlooceServerException
     *
     * @return array
     */
    private function handleResponse($curlHandler, $data, AbstractMessage $message)
    : array
    {
        $httpcode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        $xmlResponse = $data ? simplexml_load_string($data) : false;

        if ($xmlResponse === false || false === $data || curl_errno($curlHandler)) {  //  This might be redundancy
            throw new SlooceServerException('curl exception :' . curl_error($curlHandler), $httpcode, $message);
        }

        switch ($httpcode) {
            case 202:
                break;
            case 403:
                throw new InvalidRecipientException((string)$xmlResponse, $httpcode);
            case 400:
            case 500:
                throw new SlooceServerException((string)$xmlResponse, $httpcode, $message);
                break;
        }

        $array_data = json_decode(json_encode($xmlResponse), true);

        return $array_data;
    }

    /**
     * @return string
     */
    public function getSlooceDomain()
    : string
    {
        return $this->slooceDomain;
    }

    /**
     * @param string $slooceDomain
     *
     * @return Connector
     */
    public function setSlooceDomain(string $slooceDomain)
    : Connector
    {
        $this->slooceDomain = $slooceDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerId()
    : string
    {
        return $this->partnerId;
    }

    /**
     * @param string $partnerId
     *
     * @return Connector
     */
    public function setPartnerId(string $partnerId)
    : Connector
    {
        $this->partnerId = (string)$partnerId;
        return $this;
    }

    /**
     * @param string $password
     *
     * @return Connector
     */
    public function setPassword(string $password)
    : Connector
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortCodeField()
    : string
    {
        return $this->shortCodeField;
    }

    /**
     * @param string $shortCodeField
     *
     * @return Connector
     */
    public function setShortCodeField(string $shortCodeField)
    : Connector
    {
        $this->shortCodeField = $shortCodeField;
        return $this;
    }


}
