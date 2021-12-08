<?php

require_once './app/model/PostManager.php';
require_once './app/model/CommentManager.php';

class PostController{

    public function showPost($params){
        global $twig;
        $_SESSION['tmp']= [];
        //EXPLODE URI PARAMS
        $params = implode('/', $params);
        $params = explode('/', $params);
        $postId = $params[0];

        //GET POST
        $post = new PostManager();
        $currentPost = $post->getPost($postId);
        //PAGIN COMMENTS
        $comments = new CommentManager();
        $commentsByPage = 6;
        $nbcomments = $comments->getComments($postId)->rowCount();
        $totalPages = ceil($nbcomments/$commentsByPage);
        // IF POST HAS > 6 COMMENTS -- USE PAGINATION
        if($nbcomments > 6){
            $page = $params[2];
            if(!isset($page) || $page > $totalPages || $page <= 0){
                $page = 1;
                header('Location:'.$_SESSION['routes']['post'].$postId.'/page/'.$page);
            }
            //PAGINATION READY - FETCHING COMMENT LIST
            $comment = $comments->getPaginCommentList($page, $commentsByPage);
            //FETCHING COMMENT AUTHOR
            foreach($comment as $key=>$comments){
                $user = new UserManager();
                $userid = $comments['userId'];
                $username = $user->getUsername($userid);
                $comments['userName'] = $username;
                $comment[$key] = $comments;
            }
            //ALL SET -- RENDERING VIEW
            echo $twig->render('post.twig', array_merge(['currentPost'=>$currentPost,'comments'=>$comment, 'page'=>$page, 'commentsbypage'=>$commentsByPage, 'totalpages'=>$totalPages, 'nbcomments'=>$nbcomments]));
        }
        //IF POST HAS < 6 COMMENTS -- PAGINATION NOT NEEDED
        else{
            $comments = new CommentManager();
            $comment = $comments->getComments($postId);
            echo $twig->render('post.twig', array_merge(['currentPost'=>$currentPost, 'comments'=>$comment, 'nbcomments'=>$nbcomments]));
        }
    }

    public function showPostList($page){
        global $twig;

        //PREPARING PAGINATION
        $post = new PostManager();
        $postsByPage = 6;
        $nbposts = $post->postCount();
        $totalPages = ceil($nbposts/$postsByPage);
        $page = implode($page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
            $page = 1;
            header('Location:'.$_SESSION['routes']['pagenumber'].$page);
        }
        //PAGINATION IS READY
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
    
        //CHECKING IF ALL FIELDS ARE COMPLETED
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
                //SETTING FILE VARIABLES
                $tmpName = $_FILES['image']['tmp_name'];
                $name = $_FILES['image']['name'];
                $size = $_FILES['image']['size'];
                $error = $_FILES['image']['error'];
    
                //GETTING FILE EXTENSION
                $setExtension = explode('.', $name);
                $extension = strtolower(end($setExtension));
                $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    
                //SETTING SIZE LIMIT
                $maxSize = 10000000;
    
                //CHECKING SIZE
                if ($size >= $maxSize || $error == 1)
                {
                    $fileErrors[] = "Le fichier est trop volumineux. Merci d'utiliser une image de moins de 10Mo";
                }
                //CHECKING FILE TYPE
                if (!in_array($extension, $extensions))
                {
                    $fileErrors[] = "Le format du fichier n'est pas autorisé. Merci d'utiliser une image *.jpg, *.jpeg, *.png, *.gif ou *.bmp";
                }
                //CHECKING UPLOAD
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


        //CHECKING IF ALL FIELDS ARE COMPLETED
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
            //SETTING FILE VARIABLES
            $tmpName = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];
            $size = $_FILES['image']['size'];
            $error = $_FILES['image']['error'];

            //GETTING FILE EXTENSION
            $setExtension = explode('.', $name);
            $extension = strtolower(end($setExtension));
            $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

            //SETTING SIZE LIMIT
            $maxSize = 10000000;

            //CHECKING SIZE
            if ($size >= $maxSize || $error == 1)
            {
                $fileErrors[] = "Le fichier est trop volumineux. Merci d'utiliser une image de moins de 10Mo";
            }
            //CHECKING FILE TYPE
            if (!in_array($extension, $extensions))
            {
                $fileErrors[] = "Le format du fichier n'est pas autorisé. Merci d'utiliser une image *.jpg, *.jpeg, *.png, *.gif ou *.bmp";
            }
            //CHECKING UPLOAD
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