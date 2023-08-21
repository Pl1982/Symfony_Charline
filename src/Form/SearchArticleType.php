<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\User;
use App\Search\SearchArticle;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre :',
                'attr' => [
                    'placeholder' => 'Chercher par titre',
                ],
                'required' => false,
            ])
            ->add('tags', EntityType::class, [ // pour avoir une lsite de choix sur les catégories
                'label' => 'Catégories :',
                'class' => Categorie::class,
                'choice_label' => 'title',
                'query_builder' => function(EntityRepository $er): QueryBuilder { // $er pour Entity Repository
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.articles', 'a')
                        ->andWhere('c.enable = true') 
                        ->orderBy('c.title', 'ASC');
                }, 
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'label' => 'Auteurs :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'query_builder' => function(EntityRepository $er): QueryBuilder { // $er pour Entity Repository
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.articles', 'a')
                        ->andWhere('a.enable = true')
                        ->orderBy('u.lastName', 'ASC');
                }, 
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchArticle::class,
            'method' => 'GET',  // pour optimiser le partage de lien
            'csrf_protection' => false, // pour ne pas afficher le token dans l'url -> fait par défaut par symfony lors de la soumission du formulaire
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';  // enlève le prefixe des inputs du formualire (name=)
    }
}
