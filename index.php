<?php include "./lib/headers.php"  ?>
<?php include "./lib/route.php" ?>

<?php include "./controllers/ChocolateController.php"  ?>
<?php include "./controllers/CajasChocolateController.php"  ?>
<?php include "./controllers/AutenticacionController.php"  ?>
<?php include "./controllers/FeedbackController.php"  ?>

<?php include "./middleware/autenticado.php"  ?>
<?php include "./middleware/admin.php"  ?>

<?php  


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


//Cajas de chocolates
route("GET", "/cajas-chocolate", [$obtenerCajasChocolates]);
route("POST", "/cajas-chocolate", [$crearCajaChocolates]);

route("GET", "/cajas-chocolate/:id", [$verCajaChocolate]);
route("PUT", "/cajas-chocolate/:id", [$actualizarCajaChocolate]);
route("DELETE", "/cajas-chocolate/:id", [$eliminarChocolate]);

//Feedback
route("GET", "/feedback", [$autenticado,$admin,$obtenerFeedback]);
route("POST", "/feedback", [$autenticado, $crearFeedback]);


$path = $_SERVER['REQUEST_URI'] ?? '';
echo "Ruta no encontrada ". $_SERVER['REQUEST_METHOD'] . " " . $path;

?>