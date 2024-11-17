<?php 

	// ejecuta el metodo de logout en el objeto User


			session_unset();

			session_destroy();

			header('Location: landing');
	
	// $_SESSION["app-estacion"]->logout();

 ?>