<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                // 'attr' => ['class' => 'form-control'],
                'label' => 'Nom de la tâche',
                // 'label_attr' => ['class' => "form-label mt-2"],
            ],)
            ->add('description', TextareaType ::class, [
                // 'attr' => ['class' => 'form-control'],
                'label' => 'Description',
                // 'label_attr' => ['class' => "form-label mt-2"],
            ])
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'label' => 'Catégorie',
                // 'label_attr' => ['class' => "form-label mt-2"],
            ])
            ->add('priority', ChoiceType::class,[
                'choices'  => [
                    "Haute" => "Haute",
                    "Normale" => "Normale",
                    "Basse" => "Basse"
                ],
                'label' => 'Priorité'
            ])
            ->add('deadline', DateType ::class, [
                'widget' => 'single_text',
                'label' => 'Date limite',
                // 'label_attr' => ['class' => "form-label mt-2 "],
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Créer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
