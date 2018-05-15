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

namespace MauticPlugin\MauticSlooceTransportBundle\Slooce;


use MauticPlugin\MauticSlooceTransportBundle\Exception\ConnectorException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SloocePluginException;
use MauticPlugin\MauticSlooceTransportBundle\Message\AbstractMessage;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

class Connector
{
    private $apiUrl = "https://samples.cloud.sloocetech.net/slooce_apps/spi";

    private $endpoints = [];

    /** @var string */
    private $slooceDomain;

    /** @var strong */
    private $partnerId;

    public function __construct()
    {
        $this->endpoints = [
            'register'      => 'https://<sloocedomain>/spi/<partnerid>/<user>/<keyword>/messages/start',
            'messageSend'   => 'https://<sloocedomain>/spi/<partnerid>/<user>/<keyword>/messages/mt'
        ];
    }

    public function sendMessage(AbstractMessage $message) {
        $this->postMessage('messageSend', $message);
    }

    public function getPartnerPassword() {
        throw new NoSuchIndexException();
    }

    private function postMessage($endpoint, $message) {
        if (!isset($this->endpoints[$endpoint])) {
            throw new SloocePluginException('Unknown endpoint ' . $message
                                            . ', registered endpoints: ' . join(', ', array_keys($this->endpoints)));
        }

        if (is_null($this->slooceDomain) || is_null($this->partnerId)) {
            throw new ConnectorException('Configuration error.');
        }

        $endpointUrl = str_replace('<sloocedomain>', $this->getSlooceDomain(), $this->endpoints[$endpoint]);
        $endpointUrl = str_replace('<sloocedomain>', $this->getSlooceDomain(), $endpointUrl);


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
     * @return strong
     */
    public function getPartnerId()
    : strong
    {
        return $this->partnerId;
    }

    /**
     * @param strong $partnerId
     *
     * @return Connector
     */
    public function setPartnerId(strong $partnerId)
    : Connector
    {
        $this->partnerId = $partnerId;
        return $this;
    }


}