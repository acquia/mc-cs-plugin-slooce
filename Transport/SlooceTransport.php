<?php
declare(strict_types=1);

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
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\SmsBundle\Api\AbstractSmsApi;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidRecipientException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\MessageException;
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
     * @var DoNotContact
     */
    private $doNotContactService;

    /**
     * SlooceTransport constructor.
     *
     * @param TrackableModel $pageTrackableModel
     * @param PhoneNumberHelper $phoneNumberHelper
     * @param IntegrationHelper $integrationHelper
     * @param Logger $logger
     * @param Connector $connector
     * @param MessageFactory $messageFactory
     * @param DoNotContact $doNotContactService
     */
    public function __construct(
        TrackableModel $pageTrackableModel,
        PhoneNumberHelper $phoneNumberHelper,
        IntegrationHelper $integrationHelper,
        Logger $logger,
        Connector $connector,
        MessageFactory $messageFactory,
        DoNotContact $doNotContactService)
    {
        $this->logger = $logger;
        $this->connector = $connector;
        $this->messageFactory = $messageFactory;
        $this->doNotContactService = $doNotContactService;

        $integration = $integrationHelper->getIntegrationObject('Slooce');

        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            $keys = $integration->getDecryptedApiKeys($integration->getIntegrationSettings());

            if (isset($keys['username']) && isset($keys['password']) && isset($keys['slooce_domain'])) {
                $this->connector
                    ->setSlooceDomain($keys['slooce_domain'])
                    ->setPartnerId($keys['username'])
                    ->setPassword($keys['password']);

                $this->keywordField = isset($keys['keyword_field']) ? $keys['keyword_field'] : null;
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
        $util = PhoneNumberUtil::getInstance();
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
     * @throws \MauticPlugin\MauticSlooceTransportBundle\Exception\SlooceServerException
     */
    public function sendSms(Lead $contact, $content)
    {
        $number = $contact->getMobile();
        if (empty($number)) {
            $number = $contact->getPhone();
        }


        if (empty($number)) {
            return false;
        }

        $util = PhoneNumberUtil::getInstance();

        if (is_null($this->connector)) {
            throw new SloocePluginException('There is no connector available');
        }

        /** @var MtMessage $message */
        $message = $this->messageFactory->create();

        var_dump($this->keywordField);
        var_dump($contact->getFieldValue($this->keywordField));

        $message
            ->setContent($content)
            ->setKeyword($contact->getFieldValue($this->keywordField));

        try {
            $parsed = $util->parse($number, 'US');
            $number = $util->format($parsed, PhoneNumberFormat::E164);
            $number = substr($number, 1);
            $message->setUserId($number);

            MessageContentValidator::validate($message);
            $this->connector->sendMtMessage($message);
        } catch (NumberParseException $e) {
            return $e->getMessage();
        } catch (InvalidRecipientException $exception) {    // There is something with the user, probably opt-out
            $this->unsubscribeInvalidUser($contact, $exception);
            return $exception->getMessage();
        } catch (MessageException $exception) {  // Message containes invalid characters or is too long
            $this->logger->addError('Invalid message.', ['error' => $exception->getMessage()]);
            return $exception->getMessage();
        } catch (SloocePluginException $exception) {
            $this->logger->addError('Slooce plugin unhandled exception', ['error' => $exception->getMessage()]);
            throw $exception;
        }

        return true;
    }

    /**
     * Add user to DNC
     *
     * @param Lead $contact
     * @param \Exception $exception
     */
    private function unsubscribeInvalidUser(Lead $contact, \Exception $exception)
    {
        $this->logger->addWarning(
            "Invalid user added to DNC list. " . $exception->getMessage(),
            ['exception' => $exception]
        );

        $this->doNotContactService->addDncForContact(
            $contact->getId(),
            'sms',  //  no idea
            \Mautic\LeadBundle\Entity\DoNotContact::BOUNCED,
            $exception->getMessage(),
            true
        );
    }
}
