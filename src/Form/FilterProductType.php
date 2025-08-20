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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterProductType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('genderCategory', ChoiceType::class, [
            'choices' => [
                $this->translator->trans('gender.femme', [], 'messages') => 'femme',
                $this->translator->trans('gender.homme', [], 'messages') => 'homme',
                $this->translator->trans('gender.enfant', [], 'messages') => 'enfant',
                $this->translator->trans('gender.mixte', [], 'messages') => 'mixte',
            ],
            'required'    => false,
            'label'       => false,
            'placeholder' => $this->translator->trans('filter.gender', [], 'messages'),
            'attr'        => ['class' => 'uk-input'],
        ]);

        $builder->add('mainCategory', EntityType::class, [
            'class' => MainCategory::class,
            'choice_label' => function (MainCategory $mc) {
                return 'mainCategory.' . $mc->getSlug(); // slug correspond exactement aux clés YAML
            },
            'choice_value'              => 'slug',                    // valeur envoyée au submit pour filtrer
            'choice_translation_domain' => 'messages',                // Symfony traduit le label automatiquement
            'placeholder'               => 'filter.mainCategory',
            'required'                  => false,
            'label'                     => false,
            'attr'                      => ['class' => 'uk-input'],
        ]);

        // Catégorie de produit
        $builder->add('category', EntityType::class, [
            'class' => Category::class,
            'label' => false,

            'choice_label' => function (Category $category) {
                // transforme le nom en une clé clean genre "category.chaussures_de_sport"
                return 'category.' . strtolower(
                    preg_replace('/\s+/', '_', trim($category->getName()))
                );
            },

            'choice_translation_domain' => 'messages',
            'required' => false,
            'placeholder' => $this->translator->trans('filter.category', [], 'messages'),
            'attr'        => ['class' => 'uk-input'],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
            }
        ]);

        // Taille
        $builder->add('size', EntityType::class, [
            'class' => Size::class,
            'label' => false,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => 'size.choose', // <-- clé de traduction
            'choice_translation_domain' => 'messages',
            'attr' => [
                'class' => 'uk-input',
            ],
        ]);

        $builder->add('brand', EntityType::class, [
            'class' => Brand::class,
            'label' => false,
            'choice_label' => 'name', // pas de traduction, on prend le nom tel quel
            'required' => false,
            'placeholder' => $this->translator->trans('filter.brand', [], 'messages'),
            'attr' => [
                'class' => 'uk-input',
            ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('b')
                    ->orderBy('b.name', 'ASC'); // Tri par nom
            }
        ]);

        $builder->add('color', EntityType::class, [
            'class' => Color::class,
            'label' => false,

            // On génère une clé propre pour chaque couleur
            'choice_label' => function ($color) {
                return 'color.' . strtolower(str_replace([' ', 'é', 'è', 'à', 'ù', 'ç'], ['_', 'e', 'e', 'a', 'u', 'c'], $color->getName()));
            },

            'choice_translation_domain' => 'messages', // traduit toujours la clé
            'required' => false,
            'placeholder' => $this->translator->trans('color.choose', [], 'messages'),
            'attr' => ['class' => 'uk-input'],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC');
            }
        ]);

        // Submit button
        $builder->add('filter', SubmitType::class, [
            'label' => $this->translator->trans('filter.action', [], 'messages'),
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
