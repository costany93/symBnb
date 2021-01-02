<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformers\FrenchToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BookingType extends ApplicationType
{
    
    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class, $this->getConfiguration('La date d\'arrivé','la date à l\'aquelle vous compter arriver'))
            ->add('endDate',TextType::class, $this->getConfiguration('La date de départ','la date à l\'aquelle vous compter quittez les lieux'))
            ->add('comment', TextareaType::class, $this->getConfiguration(false, 'Si vous avez un commentaire n\'hésitez pas à le mettre',['required'=> false]))
            ;
        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => [
                "Default", "front"
            ]
        ]);
    }
}
