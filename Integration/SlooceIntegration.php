<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use MauticPlugin\MauticSlooceTransportBundle\Form\Type\ConfigAuthType;

/**
 * Class SlooceIntegration.
 */
class SlooceIntegration extends BasicIntegration implements IntegrationInterface, BasicInterface, ConfigFormInterface, ConfigFormAuthInterface
{
    use DefaultConfigFormTrait;

    public const NAME = 'Slooce';

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

    public function getAuthConfigFormName(): string
    {
        return ConfigAuthType::class;
    }
}
