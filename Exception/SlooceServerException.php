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

/**
 * Class SlooceServerException.
 */
class SlooceServerException extends \Exception
{
    /**
     * SlooceServerException constructor.
     *
     * @param string $xmlResponse
     * @param int    $httpCode
     */
    public function __construct(string $xmlResponse, int $httpCode)
    {
        $message = sprintf('%s (%d)', $xmlResponse, $httpCode);

        parent::__construct($message, $httpCode);
    }
}
