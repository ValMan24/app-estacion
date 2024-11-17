<?php 


		// crea el objeto con la vista
	$tpl = new Acme("map");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "MAPA"];

	// reemplaza las variables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();


 ?>