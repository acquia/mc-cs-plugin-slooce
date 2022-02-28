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
     * @var string|null
     */
    private $payload;

    /**
     * SlooceServerException constructor.
     */
    public function __construct(string $xmlResponse, int $httpCode, string $payload = null)
    {
        $this->payload = $payload;

        $message = sprintf('%s (%d)', $xmlResponse, $httpCode);

        parent::__construct($message, $httpCode);
    }

    /**
     * @return string|null
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
