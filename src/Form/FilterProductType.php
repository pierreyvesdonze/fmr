<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Size;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\GenderCategory;
use App\Entity\MainCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('genderCategory', EntityType::class, [
            'class'        => GenderCategory::class,
            'choice_label' => function ($genderCategory) {
                return ucfirst($genderCategory->getName());
            },
            'required'     => false,
            'label'        => false,
            'placeholder'  => "Pour qui ?",
            'attr'         => [
                'class' => 'uk-input',
            ]
        ]);

        // Catégorie principale (ex: vetements, chaussures, accessoires)
        $builder->add('mainCategory', EntityType::class, [
            'class' => MainCategory::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une catégorie principale',
            'attr'         => [
                'class' => 'uk-input',
            ]
        ]);

        // Catégorie de produit
        $builder->add('category', EntityType::class, [
            'class' => Category::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une catégorie',
            'attr'         => [
                'class' => 'uk-input',
            ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC'); // Tri par nom (ordre alphabétique)
            }
        ]);

        // Taille
        $builder->add('size', EntityType::class, [
            'class' => Size::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une taille',
            'attr'         => [
                'class' => 'uk-input',
            ]
        ]);

        // Marque
        $builder->add('brand', EntityType::class, [
            'class' => Brand::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une marque',
            'attr'         => [
                'class' => 'uk-input',
            ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC'); // Tri par nom (ordre alphabétique)
            }
        ]);

        // Couleur
        $builder->add('color', EntityType::class, [
            'class' => Color::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'Choisir une couleur',
            'attr'         => [
                'class' => 'uk-input',
            ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC'); // Tri par nom (ordre alphabétique)
            }
        ]);

        // Submit button
        $builder->add('filter', SubmitType::class, [
            'label' => 'Filtrer',
            'attr' => [
                "class" => 'uk-button uk-button-default'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'genderCategories' => [],
            'categories'       => [],
            'sizes'            => [],
            'brands'           => [],
            'colors'           => [],
            'mainCategories'   => [],
        ]);
    }
}
