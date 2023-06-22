<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Slooce;

use MauticPlugin\MauticSlooceTransportBundle\Exception\ConnectorException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidRecipientException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SloocePluginException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SlooceServerException;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;

class Connector
{
    /** @var string */
    private $apiDomain = '<sloocedomain>';

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

    public function __construct()
    {
        $this->slooceDomain = '';

        $this->endpoints = [
            'register'    => '<partnerid>/<user>/<keyword>/messages/start',
            'messageSend' => '<partnerid>/<user>/<keyword>/messages/mt',
        ];
    }

    /**
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
     * @param string $endpoint
     *
     * @return array
     *
     * @throws ConnectorException
     * @throws InvalidMessageArgumentsException
     * @throws InvalidRecipientException
     * @throws SlooceServerException
     */
    private function postMessage($endpoint, MtMessage $message)
    {
        if (!isset($this->endpoints[$endpoint])) {
            throw new ConnectorException('Unknown endpoint '.$endpoint.', registered endpoints: '.join(', ', array_keys($this->endpoints)));
        }

        if (is_null($this->slooceDomain) || is_null($this->partnerId)) {
            throw new ConnectorException('Configuration error.');
        }

        $apiDomain = str_replace('<sloocedomain>', $this->getSlooceDomain(), $this->apiDomain);

        $endpointURI = str_replace('<partnerid>', $this->getPartnerId(), $this->endpoints[$endpoint]);
        $endpointURI = str_replace('<user>', $message->getUserId(), $endpointURI);
        $endpointURI = str_replace('<keyword>', $message->getKeyword(), $endpointURI);
        $apiURL      = rtrim($apiDomain, '/').'/'.$endpointURI;

        $payload = $message->getXML();

        $ch = curl_init();

        $headers = ['Accept: application/xml', 'Content-Type: application/xml'];

        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);

        $response = $this->handleResponse($ch, $data, (string) $payload);

        return $response;
    }

    /**
     * @param \CurlHandle|false|resource $curlHandler
     * @param string|false               $data
     *
     * @throws InvalidRecipientException
     * @throws SlooceServerException
     */
    private function handleResponse($curlHandler, $data, string $payload): array
    {
        $httpcode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        $xmlResponse = $data ? simplexml_load_string($data) : false;

        if (false === $xmlResponse || curl_errno($curlHandler)) {  //  This might be redundancy
            throw new SlooceServerException('curl exception :'.curl_error($curlHandler), $httpcode, $payload);
        }

        switch ($httpcode) {
            case 202:
                break;
            case 403:
                throw new InvalidRecipientException((string) $xmlResponse, $httpcode, $payload);
            default:
                throw new SlooceServerException((string) $xmlResponse, $httpcode, $payload);
                break;
        }

        $array_data = json_decode(json_encode($xmlResponse), true);

        return $array_data;
    }

    public function getSlooceDomain(): string
    {
        return $this->slooceDomain;
    }

    public function setSlooceDomain(string $slooceDomain): Connector
    {
        $this->slooceDomain = $slooceDomain;

        return $this;
    }

    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    public function setPartnerId(string $partnerId): Connector
    {
        $this->partnerId = (string) $partnerId;

        return $this;
    }

    public function setPassword(string $password): Connector
    {
        $this->password = $password;

        return $this;
    }

    public function getShortCodeField(): string
    {
        return $this->shortCodeField;
    }

    public function setShortCodeField(string $shortCodeField): Connector
    {
        $this->shortCodeField = $shortCodeField;

        return $this;
    }
}
