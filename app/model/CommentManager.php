<?php

require_once './app/class/Dbconnect.php';

class CommentManager{

    public function getComments($postId){    
        $comments = DbConnect::connect()->prepare('SELECT c.commentId, DATE_FORMAT(c.commentDate, \'%d/%m/%Y à %Hh%i\') AS commentDateFr, c.commentContent, c.pendingStatus, c.userId, c.postId, u.userId, u.userName AS userName
        FROM comments c
        INNER JOIN user u
        ON c.userId = u.userId
        WHERE postId = ? 
        ORDER BY commentDateFr ASC');
        $id = implode($postId);
	    $comments->execute([$id]);
        return $comments;
    }
    
    public function getCommentList(){
        $comment = DbConnect::connect()->query('SELECT c.commentId, c.userId AS userId, c.commentContent, c.pendingStatus, c.postId, DATE_FORMAT(commentDate, \'%d/%m/%Y à %Hh%i\') AS commentDateFr, p.postId, p.postTitle, p.urlImg 
        FROM comments c 
        INNER JOIN post p 
        ON c.postId = p.postId 
        ORDER BY commentDate DESC');
        return $comment;
    }
    
    public function postComment($commentContent, $commentAuthor, $postId){
        $postId = implode('', $postId);
        $req = DbConnect::connect()->prepare('INSERT 
        INTO comments(commentContent, userId, postId) 
        VALUES (:commentContent, :userid, :postid)');
        $req->execute(array('commentContent'=>$commentContent, 'userid'=>$commentAuthor, 'postid'=>$postId));
    }
    
    public function commentDelete($commentId){
        $commentid = implode('', $commentId);
        $req = DbConnect::connect()->prepare('DELETE FROM comments WHERE commentId = ?');
        $req->execute([$commentid]);
    }
    
    public function setValidComment($commentId){
        $commentId = implode('',$commentId);
        $req = DbConnect::connect()->prepare('UPDATE comments SET pendingStatus = 0 WHERE commentId = ?');
        $res = $req->execute([$commentId]);
    }
    
    public function pendingCommentsCount(){
        $req = DbConnect::connect()->query('SELECT COUNT(*) FROM comments WHERE pendingStatus = 1');
        $res = $req->fetchColumn();
        return $res;
    
    }

    public function commentCount(){
        $req = DbConnect::connect()->query('SELECT COUNT(*) FROM comments');
        $res = $req->fetchColumn();
        return $res;
    }

    public function getPaginCommentList($page, $commentsByPage){
        $nbcomments = self::commentCount();
        $start = ($page-1)*$commentsByPage;
        $comment = DbConnect::connect()->query('SELECT c.commentId, c.userId AS userId, c.commentContent, c.pendingStatus, c.postId, DATE_FORMAT(commentDate, \'%d/%m/%Y à %Hh%i\') AS commentDateFr, p.postId, p.postTitle, p.urlImg 
        FROM comments c 
        INNER JOIN post p 
        ON c.postId = p.postId 
        ORDER BY commentDate DESC
        LIMIT '.$start.','.$commentsByPage);
        return $comment;
    }
}