<?php

namespace App\Type;

use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableau', FileType::class, [
                'label' => 'Tableau à importer (.xlsx)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'mimeTypesMessage' => 'Merci de déposer un document .xlsx',
                    ])
                ],
            ])
            ->add('promotion', EntityType::class, [
                'class' => Promotion::class,
                'choice_value' => 'id',
                'choice_label' => "nomPromotion",
            ])
        ;
    }
}
