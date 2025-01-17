<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('stock', IntegerType::class, [
                'required' => false, // Le stock doit être spécifié
            ])
            ->add('size', TextType::class, [
                'required' => false, // Taille optionnelle
            ])
            ->add('color', TextType::class, [
                'required' => false, // Couleur optionnelle
            ])
            ->add('brand', TextType::class, [
                'required' => false, // Marque optionnelle
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Utilisation du nom de la catégorie pour l'affichage
                'placeholder' => 'Choisir une catégorie', // Placeholder pour un champ vide
                'required' => true, // Le champ catégorie doit être rempli
            ])
            ->add('images', FileType::class, [
                'label' => 'Images (jusqu\'à 5 fichiers)',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '2M',
                                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                                'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP',
                            ]),
                        ],
                    ]),
                ],
            ]);

            // Ajouter des cases à cocher pour chaque image existante
        $product = $options['data'];  // Récupérer le produit actuel

        if ($product && $product->getImages()) {
            foreach ($product->getImages() as $index => $image) {
                $builder->add('remove_image_' . $index, CheckboxType::class, [
                    'label' => 'Supprimer ' . $image,
                    'required' => false,
                    'mapped' => false,  // Ce champ n'est pas lié à l'entité
                ]);
            }
        }

        $builder
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
