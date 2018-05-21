<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Transport;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\CoreBundle\Helper\PhoneNumberHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Api\AbstractSmsApi;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidRecipientException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SloocePluginException;
use MauticPlugin\MauticSlooceTransportBundle\Message\MessageFactory;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;
use MauticPlugin\MauticSlooceTransportBundle\Message\Validator\MessageContentValidator;
use MauticPlugin\MauticSlooceTransportBundle\Slooce\Connector;
use Monolog\Logger;

class SlooceTransport extends AbstractSmsApi
{
    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var string
     */
    private $keywordField;

    /**
     * SlooceApi constructor.
     *
     * @param TrackableModel    $pageTrackableModel
     * @param PhoneNumberHelper $phoneNumberHelper
     * @param IntegrationHelper $integrationHelper
     * @param Logger            $logger
     * @param Connector         $connector
     * @param MessageFactory    $messageFactory
     */
    public function __construct(
        TrackableModel $pageTrackableModel,
        PhoneNumberHelper $phoneNumberHelper,
        IntegrationHelper $integrationHelper,
        Logger $logger,
        Connector $connector,
        MessageFactory $messageFactory)
    {
        $this->logger         = $logger;
        $this->connector      = $connector;
        $this->messageFactory = $messageFactory;

        $integration = $integrationHelper->getIntegrationObject('Slooce');

        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            $keys = $integration->getDecryptedApiKeys($integration->getIntegrationSettings());

            $settings = $integration->getIntegrationSettings()->getFeatureSettings();

            if (isset($keys['username']) && isset($keys['password']) && isset($keys['slooce_domain'])) {
                $this->connector
                    ->setSlooceDomain($keys['slooce_domain'])
                    ->setPartnerId($keys['username'])
                    ->setPassword($keys['password'])
                ;
                $this->keywordField = isset($settings['keyword_field']) ? $settings['keyword_field'] : null;
            }
        }

        parent::__construct($pageTrackableModel);
    }

    /**
     * @param $number
     *
     * @return string
     *
     * @throws NumberParseException
     */
    protected function sanitizeNumber($number)
    {
        $util   = PhoneNumberUtil::getInstance();
        $parsed = $util->parse($number, 'US');

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * @param Lead $contact
     * @param $content
     *
     * @return bool|mixed|string
     * @throws SloocePluginException
     * @throws \MauticPlugin\MauticSlooceTransportBundle\Exception\MessageException
     */
    public function sendSms(Lead $contact, $content)
    {
        $number = $contact->getMobile();
        if (empty($leadPhoneNumber)) {
            $number = $contact->getPhone();
        }

        if (empty($number)) {
            return false;
        }

        if (is_null($this->connector)) {
            throw new SloocePluginException('There is no connector available');
        }

        /** @var MtMessage $message */
        $message = $this->messageFactory->create();

        // @todo add replacements to contect as in the documentation
        $message
            ->setContent($content)
            ->setKeyword($contact->getFieldValue($this->keywordField))
            ->setUserId($number)
            ;

        try {
            MessageContentValidator::validate($message);
            $this->connector->sendMessage($message);
        } catch (NumberParseException $e) {
            $this->logger->addWarning(
                $e->getMessage(),
                ['exception' => $e]
            );

            return $e->getMessage();
        } catch (InvalidRecipientException $exception) {
            return $exception->getMessage();
        } catch (InvalidMessageArgumentsException $exception) {
            throw $exception;
        }

        return true;
    }
}
