<?php include "./route.php" ?>
<?php include "./controllers/ChocolateController.php"  ?>
<?php include "./controllers/AutenticacionController.php"  ?>
<?php include "./middleware/autenticado.php"  ?>

<?php  
//Permiten el uso de los Metodos HTTP para que funcione la api en el frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 1000');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");


//Chocolates Routes
route("GET", "/chocolates", [$obtenerChocolates]);
route("POST", "/chocolates", [$crearChocolate]);

route("GET", "/chocolates/:id", [$obtenerChocolate]);
route("PUT", "/chocolates/:id", [$actualizandoChocolate]);
route("DELETE", "/chocolates/:id",[$eliminarChocolate]);


//Autenticacion Routes
route("POST", "/registrar", [$registrarUsuario]);
route("POST", "/login", [$loginUsuario]);
route("GET", "/perfil", [$autenticado,$obtenerPerfil]);




echo "Ruta no encontrada ". $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['PATH_INFO'];

?>