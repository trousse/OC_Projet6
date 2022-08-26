<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


/**
 * Class TrickType
 *
 * @package App\Form
 */
class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                "label"=> "Trick Name",
                'attr' => ['class' => 'input'],
            ])
            ->add('description', TextareaType::class,[
                "label" => "Description",
                'attr' => ['class' => 'input'],
            ])
            ->add('groupeId',EntityType::class,[
                'label' => "Groupe",
                'choice_label' => "name",
                'class' => TrickGroup::class,
                'attr' => ['class' => 'input'],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save button'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
