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
        global $router;
        require '../app/views/frontend/commentList.php';
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