<?php

require_once './app/model/PostManager.php';
require_once './app/model/CommentManager.php';

class CommentController{

    public function newComment($postId){
        global $router;
        unset($_SESSION['tmp']);
        $commentErrors = [];
        $commentSuccess = [];
        $commentAuthor = $_SESSION['userId'];
        $commentContent = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
        $comment = new CommentManager();
    
        //CHECKING IF ALL FIELDS ARE COMPLETED
        if(empty($commentContent)){
            $commentErrors = "Votre commentaire est vide, impossible de l'enregistrer.";
        }
        else{		
            $comment->postComment($commentContent, $commentAuthor, $postId);
            $commentSuccess = "Votre commentaire a bien été soumis à validation par un administrateur.";
        }
        $_SESSION['tmp'] = array_merge(['commentError' =>$commentErrors],['commentSuccess'=>$commentSuccess]);
        header('Location:'.$router->generate('post').implode($postId).'#comments');
        
    }
    
    public function commentList($page){
        global $router,$twig;
        $comments = new CommentManager();
        $commentList = $comments->getCommentList();
        //GETTING PENDING COMMENTS AUTHOR
        foreach($commentList as $key=>$comment){
            $user = new UserManager();
            $userid = $comment['userId'];
            $username = $user->getUsername($userid);
            $comment['userName'] = $username;
            $commentList[$key] = $comment;
        }
        //COUNTING PENDING COMMENTS
        $countPending = $comments->pendingCommentsCount();
        
        //PREPARING PAGINATION
        $commentsByPage = 10;
        $countValid = $comments->validCommentsCount();
        $totalPages = ceil($countValid/$commentsByPage);
        $page = implode('',$page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
            $page = 1;
            header('Location:'.$router->generate('commentnumber').$page);
        }
        //PAGINATION READY
        $validCommentList = $comments->getPaginCommentList($page, $commentsByPage);
        //GETTING VALID COMMENTS AUTHOR
        foreach($validCommentList as $key=>$validComment){
            $user = new UserManager();
            $userid = $validComment['userId'];
            $username = $user->getUsername($userid);
            $validComment['userName'] = $username;
            $validCommentList[$key] = $validComment;
        }
        echo $twig->render('commentlist.twig',array_merge(['countValid'=>$countValid],['countPending'=>$countPending], ['commentList'=>$commentList],['validCommentList'=>$validCommentList],['page'=>$page], ['commentsbypage'=>$commentsByPage], ['totalpages'=>$totalPages]));
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
        
}