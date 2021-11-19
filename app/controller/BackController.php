<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './vendor/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/src/Exception.php';
require './vendor/phpmailer/src/SMTP.php';

	class BackController{
		
		public function mailer(){
			global $twig;
			$data = require __DIR__.'/../config/dbconfig.php';
			$mail = new PHPMailer(true);
			$mailSuccess = [];
			$mailErrors = [];
		
			try {
				//Server settings
				$mail->SMTPDebug = 0;						//Verbose debug output -- Activate = SMTP::DEBUG_SERVER
				$mail->isSMTP();							//Send using SMTP
				$mail->Host       = $data['smtp'];			//Set the SMTP server to send through
				$mail->Port       = $data['port'];			//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
				$mail->SMTPAuth   = true;					//Enable SMTP authentication
				
				if ($mail->SMTPAuth){
					$mail->Username   = $data['username'];	//SMTP username
					$mail->Password   = $data['password'];	//SMTP password
					$mail->SMTPSecure = 'ssl';				//Enable implicit TLS encryption
				}
			
				//Recipients
				$mail->setFrom($_POST['email'], $_POST['name']);
				$mail->addAddress($data['username']);
			
				//Content
				$mail->isHTML(true);						//Set email format to HTML
				$mail->Body = $_POST['message'];
			
				$mail->send();
				$mailSuccess = 'Message envoyé avec succès';
			} 
			catch (Exception $e) {
			$mailErrors = "Le message n'a pas pu être envoyé.<br/>Mailer Error: <strong>{$mail->ErrorInfo}</strong>";
			}

			$_SESSION['tmp'] = array_merge(['mailsuccess' => $mailSuccess, 'mailerrors'=>$mailErrors]);
			header ('Location:'.$_SESSION['routes']['home'].'#contact');
		}
	}