<?php

namespace App\Controller;

use App\Form\PostType;
use App\Form\PostEditType;
use App\Form\CommentEditType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\Comment;

class PostController extends AbstractController
{

    /**
     * Función para listar los posts
     * @Route("/post", name="post")
     */
    public function listPosts()
    {
        $user=$this->getUser();
        $posts=$this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('post/post.html.twig',[
            'user'=>$user,
            'posts'=>$posts]);
    }
    /**
     * Función para listar mis posts
     * @Route("/misposts/{id}", name="mis_posts")
     */
    public function misPosts($id){
        $posts=$this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$id));
        return $this->render('post/mis_posts.html.twig',[
            'posts'=>$posts,
            'user'=>$id]);

    }

    /**
     * Función para ver cada post ->Botón Leer más
     * @Route("/post/view/{id}", name="view_post")
     */
    public function viewPost(Request $request,$id){
        /*$user=$this->getUser();
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $comments=$this->getDoctrine()->getRepository(Comment::class)->findBy(array('post'=>$id));
        //$authorcomment=$this->
        return $this->render('post/view_post.html.twig',[
            'user'=>$user,
            'post'=>$post[0],
            'comments'=>$comments]);*/
        $user=$this->getUser();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $comentarios = $this->getDoctrine()->getRepository(Comment::class)->findBy(array('post'=>$post));
        $comentario = new Comment();
        $form = $this->createForm(CommentType::class, $comentario);

        $form->handleRequest($request);
        $error = $form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            $comentario->setUser($this->getUser());
            $comentario->setPost($post);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comentario);
            $entityManager->flush();
        }

        return $this->render('post/view_post.html.twig', [
            'user'=>$user,
            'post' => $post,
            'form'=>$form->createView(),
            'comments' => $comentarios

        ]);
    }
    /**
     * Función para eliminar un comentario
     * @Route("/post/view/{id}/deletecomment", name="del_comment")
     */
    public function delComment(Request $request,$id){
        //buscamos por el id del comentario que hemos seleccionado para eliminar
        //$post=$this->getDoctrine()->getRepository(Post::class)->find($id);
        $comentario=$this->getDoctrine()->getRepository(Comment::class)->find($id);

        $comentarioaborrar=$comentario;

        $entityManager=$this->getDoctrine()->getManager();
        //comando en cuestión que borra el post
        $entityManager->remove($comentarioaborrar);
        $entityManager->flush();

        $this->addFlash('success', 'Comentario eliminado correctmanete');
        //una vez eliminado,volvemos a la página de los post, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute('post');
    }
    /**
     * Función para editar comentario
     * @Route("/post/edit/{id}/editcomment", name="edit_comment")
     */
    public function editComment($id,Request $request){
        $commentario=$this->getDoctrine()->getRepository(Comment::class)->findBy(array('id'=>$id));
        $comentarioaeditar=$commentario[0];

        $form=$this->createForm(CommentEditType::class,$comentarioaeditar);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){

            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Comentario modificado correctamente');
            return $this->redirectToRoute('post');
        }

        return $this->render('post/edit_comment.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Función para añadir nuevos posts
     * @Route("/post/new", name="new_post")
     */
    public function addPost(Request $request)
    {
        //crear nuevo objeto Post
        $post= new Post();
        //$post->setTitle('write a post title');
        //recojo datos del usuario loggeado
        $user=$this->getUser();
        //indico que ese usuario es el que crea el post
        $post->setUser($user);
        //por lo tanto el autor tendrá como valor el nombre de usuario correspondiente  al user
        $author=$user->getUsername();
        $post->setAuthor($author);
        //obtenemos la fecha de creación,es decir la actual
        $createdAt=$post->getCreateAt();

        //creamos el formulario
        $form=$this->createForm(PostType::class,$post);
        //handle the request
        $form->handleRequest($request);
        $error=$form->getErrors();

        //$post->setAuthor($this->getUser());
        if($form->isSubmitted() && $form->isValid()){//si el formulario es enviado y valiado...
            //data capture
            $post=$form->getData();
        /*$builder
            ->add('title')
            ->add('content')
            ->add('createAt')
            ->add('publishedAt')
            ->add('modifiedAt')
            ->add('author')
            ->add('user')
            ->add('tags')
        ;*/
            //indicamos que si lo publicamos, cogerá esa fecha
            if($post->getPublishedAt()){
                $post->setPublishedAt($createdAt);
            }

            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($post);
            //flush to DB
            $entityManager->flush();
            $this->addFlash('success','Post creado correctamente');
            return $this->redirectToRoute('post');
        }
        //render the form
        return $this->render('post/new_post.html.twig', [
            'error'=>$error,
            'form' => $form->createView()]);

    }

    /**
     * Función para editar post
     * @Route("/post/edit/{id}", name="edit_post")
     */
    public function editPost($id,Request $request){
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postaeditar=$post[0];

        $user=$this->getUser();
        if(!(in_array("ROLE_USER",$user->getRoles())) && $user->getId() != $post->getUser()->getId()){
            $this->addFlash('warning', 'No puede editar un post ajeno');
            return $this->redirectToRoute('app_homepage');
        }


        $form=$this->createForm(PostEditType::class,$postaeditar);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //recojo la fecha actual y se la añado al campo de modificación
            $fechactual= new \DateTime();
            $postaeditar->setModifiedAt($fechactual);

            //actualizo la fecha de publicación
            if($postaeditar->getPublishedAt() == null){
                $postaeditar->setPublishedAt($fechactual);
            }

            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Post modificado correctamente');
            return $this->redirectToRoute('post');
        }

        return $this->render('post/edit_post.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Función para eliminar post
     * @Route("/post/delete/{id}", name="del_post")
     */
    public function deletePost($id, Request $request)
    {
        //buscamos por el id del post que hemos seleccionado para eliminar
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $postaborrar=$post[0];

        $entityManager=$this->getDoctrine()->getManager();
        //comando en cuestión que borra el post
        $entityManager->remove($postaborrar);
        $entityManager->flush();

        $this->addFlash('success', 'Post eliminado correctmanete');
        //una vez eliminado,volvemos a la página de los post, para comprobar que se ha borrado correctamente
        return $this->redirectToRoute('post');

    }


    /*
     * @Route ("/tag/search", name="tag_search")
     * */
    public function searchTag(Request $request){
        $form=$this->createFormBuilder(null)
            ->add('query',TextType::class)
            ->add('search',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Capturo el tag
            $tag_recogido=$form->getData();
            $query=$tag_recogido['query'];
            // Busco los tags
            $tag=$this->getDoctrine()->getRepository(Tag::class)->findOneByTag($query);
            if(is_null($tag)){
                $this->addFlash('warning', 'Tag no encontrado');
                return $this->redirectToRoute('app_homepage');
            }
            // Obtengo los posts con ese tag
            $post_tag=$tag->getPosts();
            // Me quedo con los posts publicados cque tengan ese tag
            $posts=[];
            foreach ($post_tag as $post) {
                // Si el post tiene fecha de publicación, lo añado
                if(!is_null($post->getpublishedAt())){
                    array_push($posts,$post);
                }
            }
            if(empty($posts)){
                $this->addFlash('warning', 'No hay posts publicados con ese tag');
                return $this->redirectToRoute('app_homepage');
            }
            // Obtengo el id del usuario logeado
            $user_id=0;
            if(!is_null($this->getUser())){
                $user=$this->getUser();
                $user_id=$user->getId();
            }
            return $this->render('home/home.html.twig',['posts'=>$posts,'userId'=>$user_id]);
        }
        // render the form
        return $this->render('post/search_tag.html.twig',[
            'form'=>$form->createView()
        ]);
    }

}
