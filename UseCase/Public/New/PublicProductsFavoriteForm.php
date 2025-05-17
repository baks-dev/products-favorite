<?php

declare(strict_types=1);

namespace BaksDev\Products\Favorite\UseCase\Public\New;

use BaksDev\Products\Favorite\Entity\ProductsFavoriteInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublicProductsFavoriteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('invariable', HiddenType::class, ['required' => false]);

        /* Сохранить ******************************************************/
        $builder->add(
            'products_favorite',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductsFavoriteInterface::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
            'csrf_protection' => false,
        ]);
    }
}