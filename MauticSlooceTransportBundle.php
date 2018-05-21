<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\SmsBundle\DependencyInjection\Compiler\SmsTransportPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MauticSlooceTransportBundle
 *
 * @package MauticPlugin\MauticSlooceTransportBundle
 */
class MauticSlooceTransportBundle extends PluginBundleBase
{
    public function build(ContainerBuilder $container)
    {

    }
}
