<?php 

	// crea el objeto con la vista
	$tpl = new Acme("validate");

	// carga la vista
	$tpl->loadTPL();
	//array con las variables a cargar en la vista
	$vars = ["PROJECT_SECTION" => "Validar"];
	// imprime en pantalla la página
	$tpl->printTPL();

 ?>