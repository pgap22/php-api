<?php

$obtenerCajasChocolates = function ($db, $body) {
    //Obtener cajas
    $query = "SELECT * FROM caja";

    $cajasChocolates = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);

    //Obtener chocolates de las cajas
    $misCajasChocolates = [];

    foreach ($cajasChocolates as $caja) {
        $miCaja = $caja;

        //Obtener chocolate por caja
        $cajaId = $caja['id'];

        $query = "SELECT * FROM cajachocolates WHERE id_caja = $cajaId";

        $chocolatesIDs = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);

        //Obtener los datos de todos los chocolates de la caja
        foreach ($chocolatesIDs as $chocolate) {

            //Obtener chocolate por caja
            $chocolateID = $chocolate['id_chocolate'];

            $query = "SELECT * FROM chocolate WHERE id = $chocolateID";

            $chocolateDatos = mysqli_fetch_assoc(mysqli_query($db, $query));

            $miCaja['chocolates'][] = [
                ...$chocolateDatos,
                "cantidad" => $chocolate['cantidad'],
            ];
        }

        $misCajasChocolates[] = $miCaja;
    };



    echo json_encode($misCajasChocolates, JSON_UNESCAPED_UNICODE);
};

$verCajaChocolate = function ($db, $body, $params) {
    $idCaja = $params['id'];

    $query = "SELECT * FROM caja WHERE id = $idCaja";

    $caja = mysqli_fetch_assoc(mysqli_query($db, $query));

    if (empty($caja)) {
        mensaje("No se ha encotrado esa caja", 404);
    }

    //Obtener chocolates de la caja
    $query = "SELECT * FROM cajachocolates WHERE id_caja = $idCaja";

    $chocolatesIDs = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);

    $misChocolates = [];

    //Obtener los datos de todos los chocolates de la caja
    foreach ($chocolatesIDs as $chocolate) {

        //Obtener chocolate por caja
        $chocolateID = $chocolate['id_chocolate'];

        $query = "SELECT * FROM chocolate WHERE id = $chocolateID";

        $chocolateDatos = mysqli_fetch_assoc(mysqli_query($db, $query));

        $misChocolates[] = [
            ...$chocolateDatos,
            "cantidad" => $chocolate['cantidad']
        ];
    }

    $caja['chocolates'] = $misChocolates;

    echo json_encode($caja, JSON_UNESCAPED_UNICODE);
};

$crearCajaChocolates = function ($db, $body) {
    $usuarioID = $_REQUEST['user']['id'];
    $nombreCaja = $body['nombre'] ?? '';
    $chocolatesCaja = $body['chocolates'] ?? '';

    //Validar si existen los datos
    if (empty($nombreCaja)) {
        mensaje("Agrega un nombre a la caja de chocolates", 400);
    }

    if (empty($chocolatesCaja)) {
        mensaje("Debes agregar al menos un chocolate", 400);
    }

    //Obtener datos de los chocolates por el precio
    $misChocolates = [];

    foreach ($chocolatesCaja as $chocolate) {
        $chocolateID = $chocolate['id'];

        $query = "SELECT * FROM chocolate WHERE id = $chocolateID";

        $chocolateObtenido = mysqli_fetch_assoc(mysqli_query($db, $query));


        if (!empty($chocolateObtenido) & intval($chocolate['cantidad'] !== 0)) {
            $misChocolates[] = [
                ...$chocolateObtenido,
                "cantidad" => $chocolate['cantidad'],
            ];
        }
    }

    //Si en la obtencion de los chocolates no hay nada
    if (empty($misChocolates)) {
        mensaje("Añade un chocolate valido !", 400);
    }

    //Sacar el precio de la caja
    $precioCaja = 0;

    foreach ($misChocolates as $chocolate) {
        $precioChocolate = floatval($chocolate['precio']);
        $cantidadChocolate = intval($chocolate['cantidad']);

        $precioCaja += floatval($precioChocolate*$cantidadChocolate);
    }

    exit();
    //Crear la caja
    $queryCrearCaja = "INSERT INTO caja(nombre,precio,id_usuario) VALUES('$nombreCaja', $precioCaja, $usuarioID)";

    //Añadir a la base de datos
    mysqli_query($db, $queryCrearCaja);

    //Obtener la caja creada
    $idCajaCreada = mysqli_insert_id($db);

    $queryObtenerCaja = "SELECT * FROM caja WHERE id = $idCajaCreada";

    $cajaCreada = mysqli_fetch_assoc(mysqli_query($db, $queryObtenerCaja));

    //Añadir los chocolates a la caja
    foreach ($misChocolates as $chocolate) {
        $idChocolate = $chocolate['id'];

        $cantidad = $chocolate['cantidad'];

        $query = "INSERT INTO cajachocolates(id_caja,id_chocolate,cantidad) VALUES($idCajaCreada,$idChocolate,$cantidad)";

        mysqli_query($db, $query);
    }

    echo json_encode([...$cajaCreada, "chocolates" => $misChocolates], JSON_UNESCAPED_UNICODE);
};

$actualizarCajaChocolate = function ($db, $body, $params) {
    
    //Obtener la caja de chocolates
    $cajaSeleccionadaID = $params['id'];

    $query = "SELECT * FROM caja WHERE id = $cajaSeleccionadaID";

    $cajaSeleccionada = mysqli_query($db, $query);

    $cajaSeleccionada = mysqli_fetch_assoc($cajaSeleccionada);


    //Obtener los datos del json
    $nombreCaja = $body['nombre'] ?? '';
    $chocolatesData = $body['chocolates'] ?? '';


    //Validar los datos
    #Si la caja para actualizar no existe
    if (empty($cajaSeleccionada)) {
        mensaje("La caja de chocolates no existe", 400);
    }

    #Si los datos del json estan vacios
    if (empty($nombreCaja)) {
        mensaje("El nombre es obligatorio", 400);
    }

    if (empty($chocolatesData)) {
        mensaje("Los chocolates son obligatorios", 400);
    }

    //Sacar el precio de los nuevos chocolates
    #Array que nos permite guardar los datos del chocolate
    $misChocolates = [];

    #Obtener los datos por cada chocolate
    foreach ($chocolatesData as $chocolate) {
        $chocolateID = $chocolate['id'];
        $query = "SELECT * FROM chocolate WHERE id = $chocolateID";
        $chocolateDatos = mysqli_query($db, $query);
        $chocolateDatos = mysqli_fetch_assoc($chocolateDatos);

        #Añadir el campo de cantidad si existe el chocolate en la bd
        #Si la cantidad es 0, omitir tambien
        if (!empty($chocolateDatos) & intval($chocolate['cantidad']) !== 0) {
            $chocolateDatos['cantidad'] = $chocolate['cantidad'];

            $misChocolates[] = $chocolateDatos;
        }
    }

    #Si no hay chocolates en la obtencio de datos
    if(empty($misChocolates)){
        mensaje("Añade un chocolate valido", 400);
    }

    #Por cada chocolate sumar su precio
    $precio = 0;
    foreach($misChocolates as $chocolate){
        $precioChocolate = floatval($chocolate['precio']);
        $cantidadChocolate = intval($chocolate['cantidad']);
        
        //Un mas 
        $precio += floatval($precioChocolate*$cantidadChocolate);
    }    

    //Actualizar la caja en la base de datos
    $query = "
    UPDATE caja
    
    SET nombre = '$nombreCaja',
    precio = $precio

    WHERE id = $cajaSeleccionadaID
    ";

    mysqli_query($db,$query);
    
    //Borrar los chocolates antiguos
    $query = "DELETE FROM cajachocolates WHERE id_caja = $cajaSeleccionadaID";
    mysqli_query($db,$query);


    //Insertar los nuevos chocolates
    foreach($misChocolates as $chocolate){
        $chocolateID = $chocolate['id'];
        $cantidad = $chocolate['cantidad'];

        $query = "INSERT INTO cajachocolates(id_caja,id_chocolate,cantidad) VALUES($cajaSeleccionadaID,$chocolateID,$cantidad)";

        mysqli_query($db,$query);
    }

    //Mostrar la caja actualizada
    $query = "SELECT * FROM caja WHERE id = $cajaSeleccionadaID";
    
    $cajaSeleccionada = mysqli_query($db, $query);
    $cajaSeleccionada = mysqli_fetch_assoc($cajaSeleccionada);

    $cajaSeleccionada['chocolates'] = $misChocolates;

    echo json_encode($cajaSeleccionada, JSON_UNESCAPED_UNICODE);
};

$eliminarCajaChocolate = function ($db, $body, $params) {
    $id = $params['id'];

    $chocolates = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM caja WHERE id = $id"));

    if (!empty($chocolates)) {
        mysqli_query($db, "DELETE FROM caja WHERE id = $id");
        mensaje("La caja de chocolates ha sido eliminada", 200);
    } else {
        mensaje("La caja de chocolates no ha sido encontrada", 404);
    }
};

$obtenerMisCajasChocolates = function($db,$body){
    $usuarioID = $_REQUEST['user']['id'];

    //Obtener cajas
    $query = "SELECT * FROM caja WHERE id_usuario = $usuarioID";

    $cajasChocolates = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);

    //Obtener chocolates de las cajas
    $misCajasChocolates = [];

    foreach ($cajasChocolates as $caja) {
        $miCaja = $caja;

        //Obtener chocolate por caja
        $cajaId = $caja['id'];

        $query = "SELECT * FROM cajachocolates WHERE id_caja = $cajaId";

        $chocolatesIDs = mysqli_fetch_all(mysqli_query($db, $query), MYSQLI_ASSOC);

        //Obtener los datos de todos los chocolates de la caja
        foreach ($chocolatesIDs as $chocolate) {

            //Obtener chocolate por caja
            $chocolateID = $chocolate['id_chocolate'];

            $query = "SELECT * FROM chocolate WHERE id = $chocolateID";

            $chocolateDatos = mysqli_fetch_assoc(mysqli_query($db, $query));

            $miCaja['chocolates'][] = [
                ...$chocolateDatos,
                "cantidad" => $chocolate['cantidad'],
            ];
        }

        $misCajasChocolates[] = $miCaja;
    };

    echo json_encode($misCajasChocolates, JSON_UNESCAPED_UNICODE);
};