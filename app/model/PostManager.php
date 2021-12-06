<?php
require_once 'app/class/Dbconnect.php';

class PostManager{

    public function getPosts(){
        $postsList = DbConnect::connect()->query('SELECT postId, postTitle, postLede, urlImg 
        FROM post 
        ORDER BY postId DESC
        LIMIT 3');
        return $postsList;
    }

    public function getPaginPosts($page, $postsByPage){
        $nbpost = self::postCount();
        $start = ($page-1)*$postsByPage;
        $postsList = DbConnect::connect()->query('SELECT postId, postTitle, postLede, urlImg 
        FROM post 
        ORDER BY postId DESC 
        LIMIT '.$start.','.$postsByPage);
        return $postsList;
    }

    public function getPost($postId){
	    $req = DbConnect::connect()->prepare('SELECT p.postId, p.postTitle AS postTitle, p.postLede AS postLede, p.postContent AS postContent, p.urlImg AS urlImg, p.userId, DATE_FORMAT(p.postCreationDate, \'%d/%m/%Y à %Hh%i\') AS postCreationDateFr, DATE_FORMAT(postUpdateDate, \'%d/%m/%Y à %Hh%i\') AS postUpdateDateFr, u.userId, u.userName AS userName
	    FROM post p 
	    INNER JOIN user u 
	    ON p.userId = u.userId
	    WHERE postId = ?');
        if (is_array($postId)){
            $postId = implode($postId);
        }
	    $req->execute([$postId]);
	    $post=$req->fetch();

	    return $post;
    }

    public function postDelete($postId){
        $id = implode($postId);
        //DELETE IMG FROM DIR
        $deleteImg = DbConnect::connect()->prepare('SELECT urlImg FROM post where postId = ?');
        $deleteImg->execute([$id]);
        $img = $deleteImg->fetchColumn();
        unlink('public/images/blog/'.$img);
        //DELETE ASSOCIATED COMMENTS 
        $deleteComments = DbConnect::connect()->prepare('DELETE FROM comments WHERE postId = ?');
        $deleteComments->execute([$id]);
        //DELETE POST
        $deletePost = DbConnect::connect()->prepare('DELETE FROM post WHERE postId = ?');
        $deletePost->execute([$id]);
    }

    public function addPost($title, $lede, $content, $postAuthor, $file){
        $req = DbConnect::connect()->prepare('INSERT INTO post(postTitle, postLede, postContent, userId, urlImg) 
        VALUES (:postTitle, :postLede, :postContent, :userid, :urlImg)');
        $req->execute(array('postTitle'=>$title, 'postLede'=>$lede, 'postContent'=>$content, 'userid'=>$postAuthor, 'urlImg'=>$file));
    }

    public function editPost($title, $lede, $content, $file, $postId){
        $id = implode('',$postId);
        //DELETE OLD IMG FROM DIR
        $deleteImg = DbConnect::connect()->prepare('SELECT urlImg 
        FROM post 
        WHERE postId = ?');
        $deleteImg->execute([$id]);
        $img = $deleteImg->fetchColumn();
        unlink('public/images/blog/'.$img);
        $req = DbConnect::connect()->prepare('UPDATE post 
        SET postTitle = :postTitle, postLede = :postLede, postContent = :postContent, urlImg = :urlImg 
        WHERE postId = :postId');
        $req->execute(array('postTitle'=>$title, 'postLede'=>$lede, 'postContent'=>$content, 'urlImg'=>$file, 'postId' => $id));
    }

    public function editPostNoPic($title, $lede, $content, $postId){
        $postId = implode('',$postId);
        $req = DbConnect::connect()->prepare('UPDATE post 
        SET postTitle = :postTitle, postLede = :postLede, postContent = :postContent 
        WHERE postId = :postid');
        $req->execute(array('postTitle'=>$title, 'postLede'=>$lede, 'postContent'=>$content, 'postid' => $postId));
    }

    public function postCount(){
        $postCount = DbConnect::connect()->query('SELECT postId 
        FROM post');
        $res = $postCount->rowCount();
        return $res;
    }
}
