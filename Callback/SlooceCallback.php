<?php

namespace MauticPlugin\MauticSlooceTransportBundle\Callback;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\SmsBundle\Callback\CallbackInterface;
use Mautic\SmsBundle\Helper\ContactHelper;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SlooceCallback implements CallbackInterface
{
    /**
     * @var ContactHelper
     */
    private $contactHelper;

    /**
     * SlooceCallback constructor.
     */
    public function __construct(ContactHelper $contactHelper)
    {
        $this->contactHelper = $contactHelper;
    }

    /**
     * @return string
     */
    public function getTransportName()
    {
        return 'slooce';
    }

    /**
     * @return string
     */
    public function getMessage(Request $request)
    {
        $this->validateRequest($request->request);

        return $request->get('content');
    }

    /**
     * @return ArrayCollection
     *
     * @throws \Mautic\SmsBundle\Exception\NumberNotFoundException
     */
    public function getContacts(Request $request)
    {
        $this->validateRequest($request->request);

        $user = $request->get('user');

        return $this->contactHelper->findContactsByNumber($user);
    }

    private function validateRequest(ParameterBag $request)
    {
        if (
            !$request->get('@id', false)
            || !$request->get('user', false)
            || !$request->get('content', false)
        ) {
            throw new BadRequestHttpException();
        }
    }
}
