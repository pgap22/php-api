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

    if(empty($caja)){
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
    $nombreCaja = $body['nombre'];
    $chocolatesCaja = $body['chocolates'];

    //Validar si existen los datos
    if (empty($nombreCaja)) {
        mensaje("Debes agregar al menos un chocolate", 400);
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
        $precioCaja += floatval($chocolate['precio'] * $chocolate['cantidad']);
    }

    //Crear la caja
    $queryCrearCaja = "INSERT INTO caja(nombre,precio) VALUES('$nombreCaja', $precioCaja)";

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

    echo json_encode(
        [
            ...$cajaCreada,
            "chocolates" => $misChocolates
        ],
        JSON_UNESCAPED_UNICODE
    );
};

$actualizarCajaChocolate = function ($db, $body, $params) {

};

$eliminarChocolate = function ($db, $body,$params) {
    $id = $params['id'];

    $chocolates = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM caja WHERE id = $id"));

    if (!empty($chocolates)) {
        mysqli_query($db, "DELETE FROM caja WHERE id = $id");
        mensaje("La caja de chocolates ha sido eliminada", 200);
    } else {
        mensaje("La caja de chocolates no ha sido encontrada", 404);
    }
};
