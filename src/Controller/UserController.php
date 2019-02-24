<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/register",name="app_register")
     */

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        //marca el usuario activo o inactivo
        $user->setIsActive(true);
        $form=$this->createForm(UserType::class,$user);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //encriptamos el password y lo guardamos como campo
            $password=$passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);//si modifica el campo $user, el que irá a la bd
            //para manejo de las entidades
            $entityManager=$this->getDoctrine()->getManager();
            //entidad-orm-bd
            //persistimso la información del formulario
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success','User created'
            );
            return $this->redirectToRoute('app_homepage');

        }
        //renderizar formulario
        return $this->render('user/regform.html.twig',[
            'error'=>$error,
            //'form' es el nombre para construir el formulario en la plantilla
            'form'=>$form->createView()
        ]);

    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @Route("/login",name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils){
        $error=$authUtils->getLastAuthenticationError();//guardaremos el último errore de la autentificación
        //last username
        $lastUsername=$authUtils->getLastUsername();
        return $this->render('user/login.html.twig',[
            'error'=>$error,
            'last_username'=>$lastUsername
        ]);
    }

    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout(){
        $this->redirectToRoute('/');
    }
}
