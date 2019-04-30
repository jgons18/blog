<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 25/04/19
 * Time: 16:16
 */

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use FOS\CKEditorBundle\Config\CKEditorConfiguration;
use App\Form\Type\TagsInputType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'Comentarios',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder' => 'Añadir comentario'
                ]
            ])
            ->add('publicar', SubmitType::class,
                ['label'=>'Publicar',
                    'attr'=>[
                        'class'=>'form-submit btn btn-success'
                    ]])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'App\Entity\Comment']);

    }
}