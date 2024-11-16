<?php

	include_once 'env.php';


	include 'lib/mp-mailer/Mailer/src/PHPMailer.php';
	include 'lib/mp-mailer/Mailer/src/SMTP.php';
	include 'lib/mp-mailer/Mailer/src/Exception.php';


	// incluimos a User para poder hacer uso de la variable cargada en session
	include_once 'models/User.php';

	// incluimos la libreria que genera los pdf
	require ('lib/fpdf/fpdf.php');

	// Inicia la sesión
	session_start();

	// motor de plantillas
	include 'lib/Acme/Acme.php';

	// pasar variables a las plantillas
	$vars = [];

	// por defecto se va a landing
	$controlador = "landing";

	// si pidieron una seccion lo llevamos a ella
	if(strlen($_GET['slug'])!=0){
		$controlador = $_GET['slug'];	
	}

	// averiguamos si existe el controlador
	if(!is_file('controllers/'.$controlador.'Controller.php')){
		$controlador = "error404";
	}	

	//=== firewall

	// // Listas de acceso dependiendo del estado del usuario
	 $controlador_login = ["details"];//si esta en el vector puede entrar
	 $controlador_anonimo = ["login","register","validate","blocked","recovery","reset"];

	// sesion iniciada
	if(isset($_SESSION['app-estacion'])){

		// $controlador_default = "productList";
		// if ($_SESSION['app-estacion']['user']->is_admin) {
		// 	$controlador_anonimo = ["landing", "login", "register" ];
		// 	$controlador_default = "panel";
		// }

		// recorre la lista de secciones no permitidas
		foreach ($controlador_anonimo as $key => $value) {
			// si esta solicitando una sección no permitida
			if($controlador==$value){
				$controlador = "panel";
				break;
			}
		}

	}else{ // sesión no iniciada

			// recorre la lista de secciones no permitidas
			foreach ($controlador_login as $key => $value) {
			// si esta solicitando una sección no permitida
			if($controlador==$value){
				$controlador = "login";
				break;
			}
		}

	}

	// === fin firewall

	include 'controllers/'.$controlador.'Controller.php';

 ?>
