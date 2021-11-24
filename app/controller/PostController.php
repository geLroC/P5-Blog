<?php

require_once './app/model/PostManager.php';
require_once './app/model/CommentManager.php';

class PostController{

    public function showPost($postId){
        global $twig;
        $_SESSION['tmp']= [];
        $post = new PostManager();
        $id = implode('', $postId);
        $currentPost = $post->getPost($postId);

        $comments = new CommentManager();
        $comment = $comments->getComments($postId);
        echo $twig->render('post.twig', array_merge(['currentPost'=>$currentPost, 'comments'=>$comment]));
    }

    public function showPostList($page){
        global $twig;

        //Preparing pagination
        $post = new PostManager();
        $postsByPage = 6;
        $nbposts = $post->postCount();
        $totalPages = ceil($nbposts/$postsByPage);
        $page = implode('',$page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
          $page = 1;
          header('Location:'.$_SESSION['routes']['pagenumber'].$page);
        }
        //Pagination is Ready
        $posts = $post->getPaginPosts($page, $postsByPage);
        echo $twig->render('blog.twig', array_merge(['posts'=>$posts, 'page'=>$page, 'postsbypage'=>$postsByPage, 'totalpages'=>$totalPages]));
    }
    
    public function postEdition($postId){

        $post = new PostManager();
        $postErrors = [];
        $postSuccess = [];
        $fileErrors = [];
        $title = $_POST['title'];
        $lede = $_POST['lede'];
        $content = $_POST['content'];
        $ledeLength = strlen($_POST['lede']);
    
        //Check if all fields are completed
        if(!isset($_POST['title']) || empty($_POST['title'])){
            $postErrors[] = "Merci de renseigner le titre de l'article";
        }

        if(!isset($_POST['lede']) || empty($_POST['lede'])){
            $postErrors[] = "Merci de renseigner le chapô de l'article";
        }

        if(!isset($_POST['content']) || empty($_POST['content'])){
            $postErrors[] = "Merci de renseigner le contenu de l'article";
        }
    
        if (isset($_POST['lede']) && $ledeLength > 120){
            $postErrors[] = "Le chapô ne doit pas dépasser 120 caractères";
        }
    
        if(empty($postErrors)){
            if($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE){
                $post->editPostNoPic($title, $lede, $content, $postId);
                $postSuccess = "L'article ".$title." a bien été modifié.";
            }
            else{
                //Set file variables
                $tmpName = $_FILES['image']['tmp_name'];
                $name = $_FILES['image']['name'];
                $size = $_FILES['image']['size'];
                $error = $_FILES['image']['error'];
    
                //Get the file extension
                $setExtension = explode('.', $name);
                $extension = strtolower(end($setExtension));
                $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    
                //Set the file size limit
                $maxSize = 10000000;
    
                //if the file exceed the size limit
                if ($size >= $maxSize || $error == 1)
                {
                    $fileErrors[] = "Le fichier est trop volumineux. Merci d'utiliser une image de moins de 10Mo";
                }
                //if the file has not a correct extension
                if (!in_array($extension, $extensions))
                {
                    $fileErrors[] = "Le format du fichier n'est pas autorisé. Merci d'utiliser une image *.jpg, *.jpeg, *.png, *.gif ou *.bmp";
                }
                //if there is an upload error
                if ($error != 0)
                {
                    $fileErrors[] = "Une erreur est survenue";
                }
                
                if (empty($fileErrors)){
                    $imageName = uniqid('', false);
                    $file = $imageName.".".$extension;
                    move_uploaded_file($tmpName, 'public/images/blog/'.$file);
                    $post->editPost($title, $lede, $content, $file, $postId);
                    $postSuccess = "L'article ".$title." a bien été modifié.";
                }
            }
        }  
        $_SESSION['tmp'] = array_merge(['postSuccess'=>$postSuccess,'postError'=>$postErrors, 'fileError'=>$fileErrors]);
        header('Location:'.$_SESSION['routes']['post'].implode($postId));
    }

    public function newPost(){
        global $twig;
        unset($_SESSION['tmp']);
        echo $twig->render('newpost.twig');
    }

    public function deletePost($postId){
        global $router;
        $post = new PostManager();
        $post->postDelete($postId);
        $link = $router->generate('postlist');
        header('Location:'.$link);

    }

    public function postEdit($postId){
        global $twig;
        unset($_SESSION['tmp']);
        $post = new PostManager();
        $posts = $post->getPost($postId);
        echo $twig->render('editpost.twig', ['post'=>$posts]);
    }

    public function addNewPost(){
        
        $post = new PostManager();
        $postErrors = [];
        $postSuccess = [];

        $title = $_POST['title'];
        $lede = $_POST['lede'];
        $ledeLength = strlen($lede);
        $content = $_POST['content'];
        $postAuthor = $_SESSION['userId'];


        //Check if all fields are completed
        if(!isset($title) || empty($title)){
            $postErrors[] = "Merci de renseigner le titre de l'article";
        }
        if(!isset($lede) || empty($lede)){
            $postErrors[] = "Merci de renseigner le chapô de l'article";
        }
        if(!isset($content) || empty($content)){
            $postErrors[] = "Merci de renseigner le contenu de l'article";
        }
    
        if (isset($lede) && $ledeLength > 120){
            $postErrors[] = "Le chapô ne doit pas dépasser 120 caractères";
        }
        if($_FILES['image']['error'] == 4){
            $postErrors[] = "Merci d'ajouter l'image d'illustration de l'article";
        }
    
        if(isset($postErrors) && empty($postErrors))
        {
            //Set file variables
            $tmpName = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];
            $size = $_FILES['image']['size'];
            $error = $_FILES['image']['error'];

            //Get the file extension
            $setExtension = explode('.', $name);
            $extension = strtolower(end($setExtension));
            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

            //Set the file size limit
            $maxSize = 10000000;

            //if the file exceed the size limit
            if ($size >= $maxSize || $error == 1)
            {
                $fileErrors[] = "Le fichier est trop volumineux. Merci d'utiliser une image de moins de 10Mo";
            }
            //if the file has not a correct extension
            if (!in_array($extension, $extensions))
            {
                $fileErrors[] = "Le format du fichier n'est pas autorisé. Merci d'utiliser une image *.jpg, *.jpeg, *.png, *.gif ou *.bmp";
            }
            //if there is an upload error
            if ($error != 0)
            {
                $fileErrors[] = "Une erreur est survenue";
            }
            
            if (empty($fileErrors)){
                $imageName = uniqid('', false);
                $file = $imageName.".".$extension;
                move_uploaded_file($tmpName, 'public/images/blog/'.$file);
                $post->addPost($title, $lede, $content, $postAuthor, $file);
                $postSuccess = "L'article ".$title." a bien été ajouté.";
            }
        }
        $_SESSION['tmp'] = array_merge(['postSuccess'=>$postSuccess,'postError'=>$postErrors, 'fileError'=>$fileErrors]);
        header('Location:'.$_SESSION['routes']['newpost']);
    }

}