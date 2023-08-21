<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'article.title.label',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'article.title.placeholder',
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'article.metaTitle.label',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'article.metaTitle.placeholder',
                ],
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'article.metaDesc.label',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'article.metaDesc.placeholder',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'article.content.label',
                'required' => true,
                'sanitize_html' => true,
                'attr' => [
                    'placeholder' => 'article.content.placeholder',
                    'rows' => 5,
                ],
            ])
            ->add('enable', CheckboxType::class, [
                'label' => 'article.enable.label',
                'required' => false,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ArticleImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories:',
                'class' => Categorie::class,
                'choice_label' => 'title',
                'expanded' => false,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.enable = :enable')
                        ->setParameter('enable', true)
                        ->orderBy('c.title', 'ASC');
                },
                'autocomplete' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'translation_domain' => 'form',
        ]);
    }
}
