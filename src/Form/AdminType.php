<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 21/02/19
 * Time: 21:20
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //función para construir un formulario
        //añadimos add por tantos campos que tengamos en la clase User(en entity)
        $builder
            ->add('username',TextType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-username form-control',
                    'placeholder'=>'Username'
                ]
            ])
            ->add('email',EmailType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-email form-control',
                    'placeholder'=>'Email@email'
                ]
            ])
            ->add('roles',choiceType::class,[
                'required'=>'required',
                //si queremos que sea tipo checkbox, en vez de multiple, poner expanded
                'multiple'=>true,
                //campos a mostrar
                'choices'=>[
                    'Usuario'=>'ROLE_USER',
                    'Administrador'=>'ROLE_ADMIN'
                ],
                'attr'=>[
                    'class'=>'form-check  form-control'],
                'label'=>'Escoge un rol'

            ])
            ->add('plainpassword',RepeatedType::class,[ //repeated por que se repetirá para comparar con otro campo de password de que son iguales
                'type'=>PasswordType::class, //aqui indicamos que tipo de campo se va  repetir
                'required'=>'required',
                'first_options'=>[
                    'attr'=>[
                        'class'=>'form-password form-control',
                        'placeholder'=>'Password'
                    ],
                    'label'=>'Contraseña'
                ],
                'second_options'=>[
                    'attr'=>[
                        'class'=>'form-password form-control',
                        'placeholder'=>'Repite password'
                    ],
                    'label'=>'Repite la contraseña'
                ]

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'App\Entity\User']);
    }

}