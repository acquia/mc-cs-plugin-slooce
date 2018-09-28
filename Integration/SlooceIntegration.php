<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Integration;

use MauticPlugin\IntegrationsBundle\Integration\BasicIntegration;
use MauticPlugin\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use MauticPlugin\MauticSlooceTransportBundle\Form\Type\ConfigAuthType;

/**
 * Class SlooceIntegration.
 */
class SlooceIntegration extends BasicIntegration implements IntegrationInterface, BasicInterface, ConfigFormInterface, ConfigFormAuthInterface
{
    use DefaultConfigFormTrait;

    const NAME = 'Slooce';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'plugins/MauticSlooceTransportBundle/Assets/img/slooce.png';
    }

    /**
     * @return string
     */
    public function getAuthConfigFormName(): string
    {
        return ConfigAuthType::class;
    }
}
