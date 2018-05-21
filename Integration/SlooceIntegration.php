<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Integration;

use Ivory\OrderedForm\Builder\OrderedFormBuilder;
use Mautic\IntegrationBundle\Integration\BasicIntegration;
use Mautic\LeadBundle\Model\FieldModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SlooceIntegration
 *
 * @package MauticPlugin\MauticSlooceTransportBundle\Integration
 */
class SlooceIntegration extends BasicIntegration
{
    /**
     * @var bool
     */
    protected $coreIntegration = true;

    /**
     * @var FieldModel
     */
    protected $fieldModel;

    /**
     * SlooceIntegration constructor.
     *
     * @param FieldModel $fieldModel
     */
    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    public function getIcon()
    {
        return 'app/bundles/SmsBundle/Assets/img/Slooce.png';
    }

    public function getSecretKeys()
    {
        return ['password'];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        return [
            'username' => 'mautic.sms.config.form.sms.slooce.partnerid',
            'password' => 'mautic.sms.config.form.sms.slooce.password',
        ];
    }

    /**
     * @return array
     */
    public function getFormSettings()
    {
        return [
            'requires_callback'      => false,
            'requires_authorization' => false,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'basic';
    }

    /**
     * @param OrderedFormBuilder $builder
     * @param array              $data
     * @param string             $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        //dump($this->fieldModel->getLeadFields());exit;
        if ($formArea === 'features') {
            $builder->add(
                'keyword_field',
                ChoiceType::class,
                [
                    'choices'    => $this->fieldModel->getFieldList(),
                    'label'      => 'mautic.slooce.config.keyword_field',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => true,
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                ]
            );
        } elseif ($formArea == "keys") {
            $builder->add(
                'slooce_domain',
                'text',
                [
                    'label'      => 'mautic.slooce.config.slooce_domain',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => true,
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                ]
            );
        }
    }
}
