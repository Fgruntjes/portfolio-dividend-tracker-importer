<?php

namespace App\Form;

use App\Broker\ExportConverter\ExportConverterRegistry;
use App\Entity\ExportExportUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ExportUploadForm extends AbstractType
{
    private ExportConverterRegistry $brokerRegistry;

    public function __construct(ExportConverterRegistry $brokerRegistry)
    {
        $this->brokerRegistry = $brokerRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'text/csv',
                        'text/xml',
                        'application/csv',
                        'application/xml',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid XML or CSV file',
                ])
            ],
        ]);
        $builder->add('broker', ChoiceType::class, [
            'required' => true,
            'choices' => $this->brokerRegistry->getChoices(),
        ]);
        $builder->add('upload', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ExportExportUpload::class);
    }
}
