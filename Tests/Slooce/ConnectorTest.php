<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Tests\Slooce;

use MauticPlugin\MauticSlooceTransportBundle\Slooce\Connector;

class ConnectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Connector
     */
    private $connector;

    protected function setUp(): void
    {
        $this->connector = new Connector();
        $this->connector
            ->setPassword('borg')
            ->setPartnerId('number7')
            ->setSlooceDomain('universe')
            ->setShortCodeField('scfi')
            ;

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testGetShortCodeField()
    {
        $this->assertEquals('scfi', $this->connector->getShortCodeField());
    }
}
