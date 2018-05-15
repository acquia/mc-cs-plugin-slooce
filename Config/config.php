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
    'services' => [
        'events' => [
        ],
        'forms' => [
        ],
        'helpers' => [
            'mautic.slooce.message_factory' => [
                'class'     => 'MauticPlugin\MauticSlooceTransportBundle\Message\MessageFactory',
                'alias' => 'slooce_message_factory',
            ],
        ],
        'other' => [
            'mautic.sms.transport.slooce' => [
                'class'        => \MauticPlugin\MauticSlooceTransportBundle\Api\SlooceApi::class,
                'arguments'    => [
                    'mautic.page.model.trackable',
                    'mautic.helper.phone_number',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'alias' => 'Slooce',
                ],
            ],
            'mautic.slooce.api' => [
                'class' => \MauticPlugin\MauticSlooceTransportBundle\Api\SlooceApi::class,
                'arguments'=> [

                ]
            ]
        ],
        'models' => [
        ],
        'integrations' => [
            'mautic.integration.slooce' => [
                'class'     => \MauticPlugin\MauticSlooceTransportBundle\Integration\SlooceIntegration::class,
                'arguments' => [
                    'mautic.lead.model.field',
                ],
            ],
        ],
    ],
    'routes' => [
        'main' => [
        ],
        'public' => [
        ],
        'api' => [
        ],
    ],
    'menu' => [
    ],
    'parameters' => [
    ],
];
