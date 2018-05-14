<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\Integration;

use Ivory\OrderedForm\Builder\OrderedFormBuilder;
use Mautic\LeadBundle\Model\FieldModel;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SlooceIntegration extends AbstractIntegration
{
    /**
     * @var bool
     */
    protected $coreIntegration = true;

    /**
     * @var FieldModel
     */
    protected $fieldModel;

    public function __construct(FieldModel $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'Slooce';
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
            'username' => 'mautic.sms.config.form.sms.slooce.username',
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
                    'label'      => 'mautic.sms.config.form.sms.slooce.keyword_field',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => true,
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                ]
            );
            $builder->add('frequency_number',
                          NumberType::class,
                          [
                    'precision'  => 0,
                    'label'      => 'mautic.sms.list.frequency.number',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class' => 'form-control frequency',
                    ],
                ]);
            $builder->add('frequency_time',
                          ChoiceType::class,
                          [
                    'choices' => [
                        'DAY'   => 'day',
                        'WEEK'  => 'week',
                        'MONTH' => 'month',
                    ],
                    'label'      => 'mautic.lead.list.frequency.times',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'multiple'   => false,
                    'attr'       => [
                        'class' => 'form-control frequency',
                    ],
                ]);
        }
    }
}
