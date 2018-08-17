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
use Mautic\LeadBundle\Model\FieldModel;
use MauticPlugin\IntegrationsBundle\Integration\BasicIntegration;
use MauticPlugin\IntegrationsBundle\Integration\DispatcherIntegration;
use MauticPlugin\IntegrationsBundle\Integration\EncryptionIntegration;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\DispatcherInterface;
use MauticPlugin\IntegrationsBundle\Integration\Interfaces\EncryptionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;

/**
 * Class SlooceIntegration.
 */
class SlooceIntegration extends BasicIntegration implements BasicInterface, EncryptionInterface, DispatcherInterface
{
    use EncryptionIntegration, DispatcherIntegration;

    /**
     * @var FieldModel
     */
    protected $fieldModel;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Slooce';
    }

    /**
     * SlooceIntegration constructor.
     *
     * @param FieldModel $fieldModel
     */
    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'plugins/MauticSlooceTransportBundle/Assets/img/slooce.png';
    }

    /**
     * {@inheritdoc}
     */
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
    public function appendToForm(FormBuilder $builder, array $data, string $formArea)
    {
        if ($formArea === 'keys') {
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
