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

namespace MauticPlugin\MauticSlooceTransportBundle\Exception;

/**
 * Class SlooceServerException.
 */
class SlooceServerException extends SloocePluginException
{
    /**
     * @var null|string
     */
    private $payload;

    /**
     * SlooceServerException constructor.
     *
     * @param string      $xmlResponse
     * @param int         $httpCode
     * @param null|string $payload
     */
    public function __construct(string $xmlResponse, int $httpCode, string $payload = null)
    {
        $message = sprintf('%s (%d)', $xmlResponse, $httpCode);

        parent::__construct($message, $httpCode, $payload);
    }

    /**
     * @return string|null
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
