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


use MauticPlugin\MauticSlooceTransportBundle\Message\AbstractMessage;

class SlooceServerException extends \Exception
{
    public function __construct(string $xmlResponse, int $httpCode, AbstractMessage $payload)
    {
        $message = sprintf("Slooce API Exception: %d - %s, message: %s ", $httpCode, $xmlResponse, print_r($payload->getSanitizedArray(), true));

        parent::__construct($message, $httpCode);
    }
}