<?php

require_once './app/model/PostManager.php';
require_once './app/model/CommentManager.php';

class CommentController{

    public function newComment($postId){
        unset($_SESSION['tmp']);
        $commentErrors = [];
        $commentSuccess = [];
        $commentAuthor = $_SESSION['userId'];
        $commentContent = $_POST['comment'];
        $comment = new CommentManager();
    
        //Check if all fields are completed
        if(empty($_POST['comment'])){
            $commentErrors = "Votre commentaire est vide, impossible de l'enregistrer.";
        }
        else{		
            $comment->postComment($commentContent, $commentAuthor, $postId);
            $commentSuccess = "Votre commentaire a bien été soumis à validation par un administrateur.";
        }
        $_SESSION['tmp'] = array_merge(['commentError' =>$commentErrors],['commentSuccess'=>$commentSuccess]);
        $link = $_SESSION['routes']['post'].implode($postId);
        header('Location:'.$link.'#comments');
        
    }
    
    public function commentList($page){
        global $router,$twig;
        $comments = new CommentManager();
        $commentList = $comments->getCommentList();
        // Comptage du nombre de commentaires
        $countPending = $comments->pendingCommentsCount();
        // Récupération de l'auteur du commentaire
        $commentAuthor = $commentList->fetch();
        $user = new UserManager();
        $userid = $commentAuthor['userId'];
        $username = $user->getUsername($userid);
        
        //Preparing pagination
        $commentsByPage = 10;
        $countValid = $comments->commentCount();
        $totalPages = ceil($countValid/$commentsByPage);
        $page = implode('',$page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
            $page = 1;
        }
        //Pagination is Ready
        $validCommentList = $comments->getPaginCommentList($page, $commentsByPage);


        echo $twig->render('commentlist.twig',array_merge(['countValid'=>$countValid],['countPending'=>$countPending], ['commentList'=>$commentList], ['userid'=>$userid],['username'=>$username],['validCommentList'=>$validCommentList],['page'=>$page], ['commentsbypage'=>$commentsByPage], ['totalpages'=>$totalPages]));

        
        //// Récupération de la liste des commentaires
        //$comments = new CommentManager();
        //$commentList = $comments->getCommentList();
        //$commentList = $commentList->fetch();
        //// Comptage du nombre de commentaires
        //$count = $comments->pendingCommentsCount();
        //
        //// Récupération de l'auteur du commentaire
        //$user = new UserManager();
        //$userid = $commentList['userId'];
        //$username = $user->getUsername($userid);
        //
        ////Preparing pagination
        //$validComments = new CommentManager();
        //$commentsByPage = 10;
        //$nbcomments = $validComments->commentCount();
        //$totalPages = ceil($nbcomments/$commentsByPage);
        //$page = implode('',$page);
        //if(!isset($page) || $page > $totalPages || $page <= 0){
        //    $page = 1;
        //    $link = $router->generate('commentnumber').$page;
        //    header('Location:'.$link);
        //}
        ////Pagination is Ready
        //$validCommentList = $validComments->getPaginCommentList($page, $commentsByPage);
        //

    }
    
    public function deleteComment($commentId){
        global $router;
        $comment = new CommentManager();
        $comment->commentDelete($commentId);
        
        $link = $router->generate('commentlist');
        header('Location:'.$link);
    }
    
    public function validateComment($commentId){
        global $router;
        $comment = new CommentManager();
        $comment->setValidComment($commentId);
        
        $link = $router->generate('commentlist');
        header('Location:'.$link);
    }
    
    public function countPending(){
        pendingCommentsCount();
    }
    
}