<?php

	require_once 'AuthController.php';
	require_once 'app/model/PostManager.php';
	require_once 'UserController.php';
	require_once 'CommentController.php';
	require_once 'PostController.php';
	
	class FrontController{
			
		public function index(){
			global $router, $twig;
			$post = new PostManager();
			$postList = $post->getPosts();
			echo $twig->render('home.twig', ['posts' => $postList]);
			unset($_SESSION['tmp']);
		}
	}