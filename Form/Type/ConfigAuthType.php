<?php

/*
 * @copyright   2018 Mautic Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://www.mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Form\Type;


use Mautic\LeadBundle\Model\FieldModel;
use MauticPlugin\IntegrationsBundle\Form\Type\Auth\BasicAuthKeysTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigAuthType extends AbstractType
{
    use BasicAuthKeysTrait;

    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * ConfigAuthType constructor.
     *
     * @param FieldModel $fieldModel
     */
    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addKeyFields(
            $builder,
            'mautic.sms.config.form.sms.slooce.partnerid',
            'mautic.sms.config.form.sms.slooce.password'
        );

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
                'constraints' => [$this->getNotBlankConstraint()],
                'empty_value' => '',
            ]
        );

        $builder->add(
            'slooce_domain',
            TextType::class,
            [
                'label'      => 'mautic.slooce.config.slooce_domain',
                'label_attr' => ['class' => 'control-label'],
                'required'   => true,
                'attr'       => [
                    'class' => 'form-control',
                ],
                'constraints' => [$this->getNotBlankConstraint()],
            ]
        );
    }
}