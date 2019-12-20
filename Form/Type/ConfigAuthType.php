<?php

declare(strict_types=1);

/*
 * @copyright   2018 Mautic Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://www.mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticSlooceTransportBundle\Form\Type;

use Mautic\LeadBundle\Field\FieldList;
use MauticPlugin\IntegrationsBundle\Form\Type\Auth\BasicAuthKeysTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigAuthType extends AbstractType
{
    use BasicAuthKeysTrait;

    /**
     * @var FieldList
     */
    private $fieldList;

    /**
     * ConfigAuthType constructor.
     *
     * @param FieldList $fieldList
     */
    public function __construct(FieldList $fieldList)
    {
        $this->fieldList = $fieldList;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'choices'    => $this->fieldList->getFieldList(),
                'label'      => 'mautic.slooce.config.keyword_field',
                'label_attr' => ['class' => 'control-label'],
                'required'   => true,
                'attr'       => [
                    'class' => 'form-control',
                ],
                'constraints' => [$this->getNotBlankConstraint()],
                'placeholder' => '',
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }
}
