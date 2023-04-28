<?php include "./lib/headers.php"  ?>
<?php include "./lib/route.php" ?>
<?php include "./controllers/ChocolateController.php"  ?>
<?php include "./controllers/AutenticacionController.php"  ?>
<?php include "./middleware/autenticado.php"  ?>

<?php  


//Chocolates Routes
route("GET", "/chocolates", [$obtenerChocolates]);
route("POST", "/chocolates", [$autenticado,$crearChocolate]);

route("GET", "/chocolates/:id", [$obtenerChocolate]);
route("PUT", "/chocolates/:id", [$autenticado,$actualizandoChocolate]);
route("DELETE", "/chocolates/:id",[$eliminarChocolate]);


//Autenticacion Routes
route("POST", "/registrar", [$registrarUsuario]);
route("POST", "/login", [$loginUsuario]);
route("GET", "/perfil", [$autenticado,$obtenerPerfil]);


//Cajas de chocolates
route("GET", "/cajas-chocolate", [$obtenerCajasChocolate]);
route("POST", "/cajas-chocolate", [$verCajaChocolate]);
route("PUT", "/cajas-chocolate/:id", [$editarCajaChocolate]);
route("DELETE", "/cajas-chocolate/:id", [$eliminarCajaChocolate]);

$path = $_SERVER['REQUEST_URI'] ?? '';
echo "Ruta no encontrada ". $_SERVER['REQUEST_METHOD'] . " " . $path;

?>