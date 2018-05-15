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


use Mautic\PluginBundle\Entity\Integration;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticSlooceTransportBundle\Exception\MessageException;
use MauticPlugin\MauticSlooceTransportBundle\Integration\SlooceIntegration;

/**
 * Class MessageFactory
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Slooce
 */
class MessageFactory
{
    /**
     * @var SlooceIntegration
     */
    private $pluginIntegration;

    /** @var array */
    private $encoders;

    /**
     * MessageFactory constructor.
     *
     * @param Integration $pluginIntegration
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->pluginIntegration = $integrationHelper->getIntegrationObject('Slooce');
    }

    /**
     * @param string $type
     *
     * @return AbstractMessage
     * @throws MessageException
     */
    public function create($type = 'MTMessage')
    : AbstractMessage
    {

        switch ($type) {
            case 'MTMessage':
                return new MtMessage(uniqid('mautic'));
        }


        throw new MessageException('Unknown message type requested. ' . $type);
    }
}