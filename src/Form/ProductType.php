<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Color;
use App\Entity\GenderCategory;
use App\Entity\MainCategory;
use App\Entity\Product;
use App\Entity\Size;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('genderCategory', EntityType::class, [
                'class'        => GenderCategory::class,
                'choice_label' => function ($gender) {
                    return 'gender.' . strtolower($gender->getName()); // ex: gender.men, gender.women
                },
                'choice_translation_domain' => 'messages', 
                'required'     => true,
                'label'        => false,
                'placeholder'  => $this->translator->trans('filter.gender', [], 'messages'),
                'attr'         => [
                    'class' => 'uk-input',
                ]
            ])
            ->add('mainCategory', EntityType::class, [
                'class' => MainCategory::class,
                'choice_label' => 'name',
                'required'     => false,
                'label'        => false,
                'placeholder'  => $this->translator->trans('filter.mainCategory', [], 'messages'),
                'attr'         => ['class' => 'uk-input'],
                'mapped'       => false, // <-- hyper important
            ])

            ->add('name', TextType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder'  => $this->translator->trans('product_form.name', [], 'messages'),
                    'maxlength'   => 20
                ],
                'constraints' => [
                    new Length([
                        'max' => 20,
                        'maxMessage' => 'Le nom du produit ne peut pas dépasser 20 caractères.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class'       => 'uk-input',
                    'placeholder'  => $this->translator->trans('product_form.description', [], 'messages'),
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => false,
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'class'       => 'uk-input',
                    'placeholder'  => $this->translator->trans('product_form.price', [], 'messages'),
                    'step'        => '0.01'
                ]
            ])
            ->add('size', EntityType::class, [
                'class'        => Size::class,
                'choice_label' => 'name',
                'required'     => true,
                'label'        => false,
                'placeholder'  => $this->translator->trans('product_form.size', [], 'messages'),
                'attr'         => [
                    'class' => 'uk-input',
                ]
            ])
            ->add('color', EntityType::class, [
                'class'        => Color::class,
                'choice_label' => 'name',
                'required'     => true,
                'label'        => false,
                'placeholder'  => $this->translator->trans('color.choose', [], 'messages'),
                'attr'         => [
                    'class' => 'uk-input',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC'); // Tri par nom (ordre alphabétique)
                }
            ])
            ->add('brand', EntityType::class, [
                'class'        => Brand::class,
                'choice_label' => 'name',
                'required'     => false,
                'label'        => false,
                'placeholder'  => $this->translator->trans('filter.brand', [], 'messages'),
                'attr'         => [
                    'class' => 'uk-input',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC'); // Tri par nom (ordre alphabétique)
                }
            ])
            ->add('category', EntityType::class, [
                'class'        => Category::class,
                'choice_label' => 'name',
                'required'     => true,
                'label'        => false,
                'placeholder'  => $this->translator->trans('filter.category', [], 'messages'),
                'attr'         => [
                    'class' => 'uk-input',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC'); // Tri par nom (ordre alphabétique)
                }
            ])
            ->add('wear', ChoiceType::class, [
                'required' => true,
                'label'    => "État d'usure",
                'choices'  => [
                    'Neuf avec étiquette' => 'Neuf avec étiquette',
                    'Neuf'                => 'Neuf',
                    'Très bon état'       => 'Très bon état',
                    'Bon état'            => 'Bon état',
                    'Satisfaisant'        => 'Satisfaisant'
                ],
                'choice_value' => function ($choice) {
                    // Retourne la valeur de l'option choisie. Assurez-vous que c'est une chaîne.
                    return is_string($choice) ? $choice : null;
                },
            ])
            ->add('mainImage', FileType::class, [
                'label' => 'Image principale (2MO max)',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'maxSizeMessage' => 'Le fichier est trop gros.',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP',
                    ]),
                ],
            ])
            ->add('images', FileType::class, [
                'label' => 'Images (2MO max) (jusqu\'à 6 fichiers)',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Count([
                        'max' => 6,
                        'maxMessage' => 'Vous ne pouvez pas télécharger plus de 6 fichiers.',
                    ]),
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '20M',
                                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                                'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP',
                            ]),
                        ],
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    "class" => 'uk-button uk-button-default'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
