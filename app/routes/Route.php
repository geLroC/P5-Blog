<?php

require 'vendor/autoload.php';

 class Route{

    public static function getRoutes():array{
        return
        [
            //HOME
            ['GET', '/', 'FrontController#index', 'home'],
            ['GET', '/home', 'FrontController#index', 'homepage'],

            //AUTHENTICATION
            ['GET', '/authentication', 'AuthController#authentication', 'authentication'],
            ['POST', '/authentication/login', 'AuthController#userLogin', 'login'],
            ['POST', '/authentication/register', 'AuthController#userRegister', 'register'],
            ['GET', '/authentication/disconnect', 'AuthController#disconnect', 'disconnect'],

            //USER MANAGMENT
            ['GET', '/user/list/page/[:page]', 'UserController#userList', 'userlist'],
            ['GET', '/user/list/delete-user/[:userId]', 'UserController#deleteUser', 'deleteUser'],
            ['POST', '/user/admin/set/[:userId]', 'UserController#setUserAdmin','setUserAdmin'],
            ['POST', '/user/admin/unset/[:userId]', 'UserController#unsetUserAdmin','unsetUserAdmin'],
            ['POST', '/user/activate/[:userId]', 'UserController#setUserActive', 'activateUser'],
            ['POST', '/user/deactivate/[:userId]', 'UserController#setUserInactive', 'deactivateUser'],
            ['GET', '/user/myaccount', 'UserController#myAccount', 'account'],
            ['POST', '/user/myaccount/editpassword/[:userId]', 'UserController#editPassword', 'passedit'],

            //BLOG
            ['GET', '/blog', 'PostController#showPostList', 'postlist'],
            ['GET', '/blog/page/[:page]', 'PostController#showPostList', 'pagenumber'],
            ['GET', '/blog/[**:params]', 'PostController#showPost', 'post'],

            //POST MANAGMENT
            ['GET', '/post/new', 'PostController#newPost', 'newpost'],
            ['POST', '/post/add', 'PostController#addNewPost', 'addpost'],
            ['GET', '/post/delete/[:postId]', 'PostController#deletePost', 'deletepost'],
            ['GET', '/post/edit/[:postId]', 'PostController#postEdit', 'editpost'],
            ['POST', '/blog/[:postId]', 'PostController#postEdition', 'postEdition'],

            //COMMENT MANAGMENT
            ['GET', '/comments/list', 'CommentController#commentList', 'commentlist'],
            ['POST', '/post/newcomment/[:postId]', 'CommentController#newComment', 'newcomment'],
            ['GET', '/comments/delete/[:commentId]', 'CommentController#deleteComment', 'deletecomment'],
            ['GET', '/post/validcomment/[:commentId]', 'CommentController#ValidateComment', 'validcomment'],
            ['GET', '/comments/list/page/[:page]', 'CommentController#commentList', 'commentnumber'], 

            //MAILER
            ['POST', '/mail', 'BackController#mailer', 'mail'],
        ];
    }
}