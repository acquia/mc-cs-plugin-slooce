<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Slooce',
    'description' => 'Enables integrations Slooce MT Transport',
    'version'     => '1.0',
    'author'      => 'Mautic',
    'services'    => [
        'events'       => [
        ],
        'forms'        => [
            'mautic.slooce.form.config_auth' => [
                'class' => \MauticPlugin\MauticSlooceTransportBundle\Form\Type\ConfigAuthType::class,
                'arguments' => [
                    'mautic.lead.model.field',
                ],
            ],
        ],
        'helpers'      => [
            'mautic.slooce.message_factory' => [
                'class' => 'MauticPlugin\MauticSlooceTransportBundle\Message\MessageFactory',
                'alias' => 'slooce_message_factory',
            ],
        ],
        'other'        => [
            'mautic.sms.transport.slooce' => [
                'class'        => \MauticPlugin\MauticSlooceTransportBundle\Transport\SlooceTransport::class,
                'arguments'    => [
                    'mautic.integrations.helper',
                    'monolog.logger.mautic',
                    'mautic.slooce.connector',
                    'mautic.slooce.message_factory',
                    'mautic.lead.model.dnc',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'Slooce',
                ],
            ],
            'mautic.sms.slooce.callback' => [
                'class' => \MauticPlugin\MauticSlooceTransportBundle\Callback\SlooceCallback::class,
                'arguments' => [
                    'mautic.sms.helper.contact',
                ],
                'tag' => 'mautic.sms_callback_handler',
            ],
            'mautic.slooce.connector'     => [
                'class'     => \MauticPlugin\MauticSlooceTransportBundle\Slooce\Connector::class,
                'arguments' => [
                    'mautic.helper.phone_number',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
            ],
        ],
        'models'       => [
        ],
        'integrations' => [
            'mautic.integration.slooce' => [
                'class'     => \MauticPlugin\MauticSlooceTransportBundle\Integration\SlooceIntegration::class,
                'arguments' => [
                ],
                'tags'      => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    'mautic.config_integration',
                    'mautic.auth_integration',
                ],
            ],
        ],
    ],
    'routes'      => [
        'main'   => [
        ],
        'public' => [
        ],
        'api'    => [
        ],
    ],
    'menu'        => [
        'main' => [
            'items' => [
                'mautic.sms.smses' => [
                    'route'    => 'mautic_sms_index',
                    'access'   => ['sms:smses:viewown', 'sms:smses:viewother'],
                    'parent'   => 'mautic.core.channels',
                    'checks'   => [
                        'integration' => [
                            'Slooce' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'priority' => 70,
                ],
            ],
        ],
    ],
    'parameters'  => [
    ],
];
