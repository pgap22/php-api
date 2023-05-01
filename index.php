<?php include "./lib/headers.php"  ?>
<?php include "./lib/route.php" ?>

<?php include "./controllers/ChocolateController.php"  ?>
<?php include "./controllers/CajasChocolateController.php"  ?>
<?php include "./controllers/AutenticacionController.php"  ?>
<?php include "./controllers/FeedbackController.php"  ?>
<?php include "./controllers/FavoritosController.php"  ?>
<?php include "./controllers/CompraController.php"  ?>


<?php include "./middleware/autenticado.php"  ?>
<?php include "./middleware/admin.php"  ?>

<?php
date_default_timezone_set('Etc/GMT+6');

//Chocolates Routes
route("GET", "/chocolates", [$obtenerChocolates]);
route("POST", "/chocolates", [$autenticado, $admin, $crearChocolate]);

route("GET", "/chocolates/:id", [$obtenerChocolate]);
route("PUT", "/chocolates/:id", [$autenticado, $admin, $actualizandoChocolate]);
route("DELETE", "/chocolates/:id", [$autenticado, $admin, $eliminarChocolate]);


//Autenticacion Routes
route("POST", "/registrar", [$registrarUsuario]);
route("POST", "/login", [$loginUsuario]);
route("GET", "/perfil", [$autenticado, $obtenerPerfil]);


//Cajas de chocolates
route("GET", "/cajas-chocolate", [$obtenerCajasChocolates]);
route("GET", "/cajas-chocolate/miscajas", [$autenticado,$obtenerMisCajasChocolates]);
route("POST", "/cajas-chocolate", [$autenticado,$crearCajaChocolates]);

route("GET", "/cajas-chocolate/:id", [$verCajaChocolate]);
route("PUT", "/cajas-chocolate/:id", [$autenticado, $actualizarCajaChocolate]);
route("DELETE", "/cajas-chocolate/:id", [$autenticado, $eliminarChocolate]);

//Feedback
route("GET", "/feedback", [$autenticado, $admin, $obtenerFeedback]);
route("POST", "/feedback", [$autenticado, $crearFeedback]);

//Favoritos
route("GET", "/favoritos", [$autenticado,$obtenerChocolatesFav]);
route("GET", "/favoritos/:id", [$autenticado,$alternarChocolateFav]);

//Comprar
route("GET", "/comprar", [$autenticado,$obtenerCompras]);
route("POST", "/comprar", [$autenticado,$realizarComprar]);
route("POST", "/comprar/consultar", [$consultarPrecioTotal]);

$path = $_SERVER['REQUEST_URI'] ?? '';
echo "Ruta no encontrada " . $_SERVER['REQUEST_METHOD'] . " " . $path;

?>