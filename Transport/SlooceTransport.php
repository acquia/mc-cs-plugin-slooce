<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Transport;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\IntegrationsBundle\Exception\PluginNotConfiguredException;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidRecipientException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\MessageException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SloocePluginException;
use MauticPlugin\MauticSlooceTransportBundle\Exception\SlooceServerException;
use MauticPlugin\MauticSlooceTransportBundle\Integration\SlooceIntegration;
use MauticPlugin\MauticSlooceTransportBundle\Message\MessageFactory;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;
use MauticPlugin\MauticSlooceTransportBundle\Message\Validator\MessageContentValidator;
use MauticPlugin\MauticSlooceTransportBundle\Slooce\Connector;
use Monolog\Logger;

/**
 * Class SlooceTransport is the transport service for mautic.
 */
class SlooceTransport implements TransportInterface
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
     * @var IntegrationsHelper
     */
    private $integrationsHelper;

    /**
     * @var bool
     */
    private $connectorConfigured = false;

    /**
     * SlooceTransport constructor.
     */
    public function __construct(
        IntegrationsHelper $integrationsHelper,
        Logger $logger,
        Connector $connector,
        MessageFactory $messageFactory,
        DoNotContact $doNotContactService
    ) {
        $this->logger              = $logger;
        $this->connector           = $connector;
        $this->messageFactory      = $messageFactory;
        $this->doNotContactService = $doNotContactService;
        $this->integrationsHelper  = $integrationsHelper;
    }

    /**
     * @param string $content
     *
     * @return bool|PluginNotConfiguredException|mixed|string
     *
     * @throws MessageException
     * @throws SloocePluginException
     * @throws \Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException
     */
    public function sendSms(Lead $contact, $content)
    {
        $number = $contact->getLeadPhoneNumber();
        if (empty($number)) {
            return false;
        }

        $util = PhoneNumberUtil::getInstance();

        if (is_null($this->connector)) {
            throw new SloocePluginException('There is no connector available');
        }

        if (!$this->connectorConfigured && !$this->configureConnector()) {
            return new PluginNotConfiguredException();
        }

        /** @var MtMessage $message */
        $message = $this->messageFactory->create();

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
        } catch (NumberParseException $exception) {
            $this->logger->addInfo('Invalid number format', ['error' => $exception->getMessage()]);

            return 'mautic.slooce.failed.invalid_phone_number';
        } catch (InvalidRecipientException $exception) {    // There is something with the user, probably opt-out
            $this->logger->addInfo(
                'Invalid recipient',
                ['error' => $exception->getMessage(), 'number' => $number, 'keyword' => $message->getKeyword(), 'payload' => $exception->getPayload()]
            );

            $this->unsubscribeInvalidUser($contact, $exception);

            return 'mautic.slooce.failed.rejected_recipient';
        } catch (MessageException $exception) {  // Message contains invalid characters or is too long
            $this->logger->addError(
                'Invalid message.',
                ['error' => $exception->getMessage(), 'number' => $number, 'keyword' => $message->getKeyword()]
            );

            return 'mautic.slooce.failed.invalid_message_format';
        } catch (SlooceServerException $exception) {
            $this->logger->addError(
                'Server response error.',
                ['error' => $exception->getMessage(), 'number' => $number, 'keyword' => $message->getKeyword(), 'payload' => $exception->getPayload()]
            );

            return $exception->getMessage();
        } catch (SloocePluginException $exception) {
            $this->logger->addError(
                'Slooce plugin unhandled exception',
                ['error' => $exception->getMessage(), 'number' => $number, 'keyword' => $message->getKeyword()]
            );

            throw $exception;
        }

        return true;
    }

    /**
     * Add user to DNC.
     */
    private function unsubscribeInvalidUser(Lead $contact, \Exception $exception)
    {
        $this->logger->addWarning(
            'Invalid user added to DNC list. '.$exception->getMessage(),
            ['exception' => $exception]
        );

        $this->doNotContactService->addDncForContact(
            $contact->getId(),
            'sms',
            \Mautic\LeadBundle\Entity\DoNotContact::UNSUBSCRIBED,
            $exception->getMessage(),
            true
        );
    }

    /**
     * @return bool
     *
     * @throws \Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException
     */
    private function configureConnector()
    {
        $integration              = $this->integrationsHelper->getIntegration(SlooceIntegration::NAME);
        $integrationConfiguration = $integration->getIntegrationConfiguration();

        if ($integrationConfiguration->getIsPublished()) {
            $keys = $integrationConfiguration->getApiKeys();

            if (isset($keys['username']) && isset($keys['password']) && isset($keys['slooce_domain'])) {
                $this->connector
                    ->setSlooceDomain($keys['slooce_domain'])
                    ->setPartnerId($keys['username'])
                    ->setPassword($keys['password']);

                $this->keywordField = isset($keys['keyword_field']) ? $keys['keyword_field'] : null;

                $this->connectorConfigured = true;

                return true;
            }
        }

        return false;
    }
}
