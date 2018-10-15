<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @author      Jan Kozak <galvani78@gmail.com>
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Tests\Message\Validator;

use MauticPlugin\MauticSlooceTransportBundle\Exception\InvalidMessageArgumentsException;
use MauticPlugin\MauticSlooceTransportBundle\Message\MtMessage;
use MauticPlugin\MauticSlooceTransportBundle\Message\Validator\MessageContentValidator;

class MessageContentValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MessageContentValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new MessageContentValidator();
        parent::setUp();
    }

    public function testValidate()
    {
        $message = new MtMessage();
        $message->setContent('fjwenifhwewn,khfe,khnwifewe')->setKeyword('aaaa');

        try {
            $this->validator->validate($message);
            $this->assertTrue(true, 'Validation has passed');
        } catch (\Exception $e) {
            $this->fail('Validation of valid message end with no exception, error:  '.$e->getMessage());
        }

        $message->setContent('k,wuerk,eíwšřčěščěš');

        try {
            $message->setContent(str_repeat('.', MessageContentValidator::MAX_CONTENT_LENGTH + 1));
            $this->fail('Setter should fail');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidMessageArgumentsException::class, $e, get_class($e).' matches InvalidMessageArgumentsException');
        }
    }
}
