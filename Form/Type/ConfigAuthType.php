<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSlooceTransportBundle\Form\Type;

use Mautic\IntegrationsBundle\Form\Type\Auth\BasicAuthKeysTrait;
use Mautic\LeadBundle\Model\FieldModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigAuthType extends AbstractType
{
    use BasicAuthKeysTrait;

    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * ConfigAuthType constructor.
     */
    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

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
                'choices'    => $this->fieldModel->getFieldList(),
                'label'      => 'mautic.slooce.config.keyword_field',
                'label_attr' => ['class' => 'control-label'],
                'required'   => true,
                'attr'       => [
                    'class' => 'form-control',
                ],
                'constraints' => [$this->getNotBlankConstraint()],
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }
}
