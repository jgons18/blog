<?php

namespace App\Controller;

#use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\AdminType;


/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_admin_users")
     */
    public function listUsers()
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig',[
            'users'=>$users]);
    }

    /**
     * @Route("/admin/user/view/{id}", name="app_admin_users_view")
     */
    public function viewUser($id){
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        return $this->render('admin/usersview.html.twig',['user'=>$user[0]]);;
    }

    /**
     * @Route("/admin/user/new", name="app_admin_user_new")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user=new User();
        $form=$this->createForm(AdminType::class,$user);
        $form->handleRequest($request);
        $error=$form->getErrors();
        if($form->isSubmitted() && $form->isValid()){
            //
            $user=$form->getData();
            //
            // encrypt the plainpassword
            $password=$passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            // handle the entities
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario insertado correctamente');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/usersnew.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="app_admin_users_edit")
     */
    public function editUser($id,Request $request,UserPasswordEncoderInterface $passwordEncoder){
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $useraeditar=$user[0];
        $form=$this->createForm(AdminType::class,$useraeditar);
        $form->handleRequest($request);
        $error=$form->getErrors();
        if($form->isSubmitted() && $form->isValid()){
            // encriptación de la password
            $password=$passwordEncoder->encodePassword($useraeditar,$useraeditar->getPlainPassword());
            $useraeditar->setPassword($password);
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Usuario modificado correctamente');
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/usersedit.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }




    /**
     * @Route("/admin/user/delete/{id}", name="app_admin_users_delete")
     */
    public function deleteUser($id, Request $request)
    {
        //buscamos por el id del usuario que hemos seleccionado para eliminar
        $user=$this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $useraborrar=$user[0];
        $entityManager=$this->getDoctrine()->getManager();
        //comando en cuestión que borra el usuario
        $entityManager->remove($useraborrar);
        $entityManager->flush();
        $this->addFlash('success', 'User deleted correctly');
        //una vez esto volvemos al panel de administración para poder observar que se ha borrado correctamente
        return $this->redirectToRoute('app_admin_users');

    }
}
