<?php 


		// crea el objeto con la vista
	$tpl = new Acme("administrator");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "ADMINISTRADOR"];

	// reemplaza las variables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();


 ?>