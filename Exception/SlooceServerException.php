<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Exception;

/**
 * Class SlooceServerException.
 */
class SlooceServerException extends SloocePluginException
{
    /**
     * @var string|null
     */
    private $payload;

    /**
     * SlooceServerException constructor.
     */
    public function __construct(string $xmlResponse, int $httpCode, string $payload = null)
    {
        $this->payload = $payload;

        $message = sprintf('%s (%d)', $xmlResponse, $httpCode);

        parent::__construct($message, $httpCode);
    }

    /**
     * @return string|null
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
