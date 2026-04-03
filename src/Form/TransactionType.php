<?php

namespace App\Form;

use App\Entity\TransactionWallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('nomTransaction')
    ->add('type')
    ->add('montant')
    ->add('dateTransaction')
    ->add('category', EntityType::class, [
        'class' => Category::class,
        'choice_label' => 'nom',
    ])
    ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransactionWallet::class,
        ]);
    }
}