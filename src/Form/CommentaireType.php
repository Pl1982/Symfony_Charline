<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', RangeType::class, [
                'label' => 'Note:',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'step' => 1,
                    'value' => 3,
                ],
                'help' => 'Note que vous voulez donner à votre article, entre 0 et 5',
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre:',
                'attr' => [
                    'placeholder' => 'Titre du commentaire',
                ],
                'required' => true,
                'sanitize_html' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:',
                'attr' => [
                    'placeholder' => 'Description du commentaire',
                    'rows' => 5,
                ],
                'required' => true,
                'sanitize_html' => true,
            ])
            ->add('rgpd', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte que mes données soient enregistrées',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
