<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 28/04/19
 * Time: 17:50
 */

namespace App\Controller\Api;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UserController
 * @package App\Controller\Api
 * @author <jennigonzalez99asdfghj@gmail.com>
 */
class UserController extends Controller
{

    private function serialize(User $user){
        return array(
            'username'=>$user->getUsername(),
            'email'=>$user->getEmail(),
            'passw'=>$user->getPassw(),
            'role'=>$user->getRole(),
            'lastlogin'=>$user->getLastlogin(),
            'isActive'=>$user->getIsActive()
        );

    }

    public function listUser($username=null){
        if ($username){
            $user=$this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['username'=>$username]);


            return new JsonResponse($this->serialize($user));
        }else{
            $users=$this->getDoctrine()->getRepository(User::class)->findall();
            $data = array('users' => array());
            foreach ($users as $user) {
                $data['users'][] = $this->serialize($user);
            }

            $response = new Response(json_encode($data), 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;


        }
    }
    /**
     * api añadir usuario - método post
     * @Route("/api/user", name="api_user")
     */

    /*public function newUser(Request $request, UserPasswordEncoderInterface $encoder){
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $plainPassword = $request->request->get('plainPassword');

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setRole('ROLE_USER');
        $password=$encoder->encodePassword($user, $plainPassword);
        $user->setPassw($password);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = new Response('User created', 201);
        $response->headers->set('Location', '/api/user/'.$user->getId());
        return $response;



    }*/

}