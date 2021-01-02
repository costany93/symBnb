<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Entrer un super titre pour votre annonce'))
            ->add('slug',TextType::class, $this->getConfiguration('Slug','Adresse web (automatique)', [
                'required' => false
            ]))
            ->add('coverImage',UrlType::class, $this->getConfiguration('Url de l\'image principale', 'Entrer url de votre image'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Donnez une introduction à votre annoce'))
            ->add('contenu', TextareaType::class, $this->getConfiguration('Description détaillé', 'Tapez une description qui donne vraiment envie de venir chez vous'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambre', 'Entrer le nombre de chambre de votre maison'))
            ->add('price',MoneyType::class, $this->getConfiguration('Prix par nuit', 'Entrer le prix par nuit de vos chambres'))
            ->add('images',CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
