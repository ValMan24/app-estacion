<?php 

	// crea el objeto con la vista
	$tpl = new Acme("panel");
	if(isset($_SESSION['app-estacion']['user'])){
var_dump($_SESSION['app-estacion']['user']->is_admin);
}$usuario = new User();
$usuario->obtenerDataLocation();

	// carga la vista
	$tpl->loadTPL();

	//array con las variables a cargar en la vista
	$vars = ["PROJECT_SECTION" => "Panel"];

	//carga el array con las vaiables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en la vista en la página
	$tpl->printTPL();

 ?>