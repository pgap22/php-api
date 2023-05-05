<?php

$obtenerCompras = function ($db, $body) {
    $usuarioID = $_REQUEST['user']['id'];
    
    //Obtener todas las compras
    $query   = "SELECT * FROM compra WHERE id_usuario = $usuarioID";
    $compras = mysqli_query($db,$query);
    $compras = mysqli_fetch_all($compras,MYSQLI_ASSOC);
    //Obtener los detalles de los chocolates y cajas

    $misCompras = [];

    foreach($compras as $compra){
        $idCompra = $compra['id'];

        #Obtener los detalles de los chocolates
        $query = "SELECT 
        comprachocolates.cantidad,
        chocolate.*
        from comprachocolates
        inner join chocolate
        on comprachocolates.id_chocolate = chocolate.id
        where id_compra = $idCompra
        ";

        $chocolates = mysqli_query($db,$query);
        $chocolates = mysqli_fetch_all($chocolates, MYSQLI_ASSOC);
        
        $query = "SELECT 
        compracaja.cantidad,
        caja.*
        from compracaja
        inner join caja
        on compracaja.id_caja = caja.id
        where id_compra = $idCompra
        ";

        $cajas = mysqli_query($db,$query);
        $cajas = mysqli_fetch_all($cajas, MYSQLI_ASSOC);
        
        $compra['chocolates'] = $chocolates;
        $compra['cajas'] = $cajas;

        $misCompras[] = $compra;
    }
    

    echo json_encode($misCompras, JSON_UNESCAPED_UNICODE);
};

$realizarComprar = function ($db, $body) {
    $usuarioCompra = $_REQUEST['user']['id'];
    $chocolates = $body['chocolates'] ?? '';
    $cajasChocolates = $body['cajas'] ?? '';

    //Verificar si no estan vacios
    if (empty($chocolates) && empty($cajasChocolates)) {
        mensaje("Agrega al menos chocolates o cajas !", 400);
    }
 

    //Obtener todos los detalles de la compra
    $compra      = obtenerPrecioTotal($chocolates, $cajasChocolates, $db);
    $chocolates  = $compra['chocolates'];
    $cajas       = $compra['cajas'];
    $precioTotal = $compra['precioTotal'];
    $fecha       = date("Y-m-d H:i:s");

    //Agregar la compra a la base de datos

    #Query para añadir la compra
    $query = "INSERT INTO compra(id_usuario,fecha_compra,precio) VALUES($usuarioCompra, '$fecha', $precioTotal)";
    mysqli_query($db, $query);

    //Obtener el id de compra
    $idCompra = mysqli_insert_id($db);

    //Añadir registro de los chocolates comprados
    foreach ($chocolates as $chocolate) {
        $chocolateID = $chocolate['id'];
        $cantidad    = intval($chocolate['cantidad']);

        $query = "INSERT INTO comprachocolates(id_compra,id_chocolate,cantidad) VALUES($idCompra, $chocolateID,$cantidad)";

        mysqli_query($db, $query);
    }

    //Añadir registro de las cajas compradas
    foreach ($cajas as $caja) {
        $cajaID = $caja['id'];
        $cantidad    = intval($caja['cantidad']);
        $query = "INSERT INTO compracaja(id_compra,id_caja,cantidad) VALUES($idCompra, $cajaID, $cantidad)";
        mysqli_query($db, $query);
    }


    $compra['id'] = $idCompra;
    $compra['fecha_compra'] = $fecha;

    echo json_encode($compra);
};

$consultarPrecioTotal = function ($db, $body) {
    $chocolates = $body['chocolates'] ?? '';
    $cajasChocolates = $body['cajas'] ?? '';

    //Verificar si no estan vacios
    if (empty($chocolates) && empty($cajasChocolates)) {
        mensaje("Agrega al menos chocolates o cajas !", 400);
    }
 

    //Obtener el precio total de todos los chocolates
    $precioTotal = obtenerPrecioTotal($chocolates, $cajasChocolates, $db);
    $precioTotal = $precioTotal['precioTotal'];

    echo json_encode(["precioTotal" => $precioTotal]);
};


function obtenerPrecioTotal($chocolates, $cajasChocolates, $db)
{
    $chocolatesDatos = [];
    $cajasDatos = [];

    #Empezar el precio desde 0
    $precioChocolate = 0;

    #Porcada chocolate obtener el precio de la BD
    foreach ($chocolates as $chocolate) {
        $chocolateID = $chocolate['id'];
        $cantidad = intval($chocolate['cantidad'] ?? '');

        #Query para obtener los datos del chocolate
        $query = "SELECT * from chocolate WHERE id=$chocolateID";

        #Obtener el precio del chocolate que esta en la bd
        $precioDB = mysqli_query($db, $query);
        $precioDB = mysqli_fetch_assoc($precioDB);

        $chocolatesDatos[] = [
            ...$precioDB,
            "cantidad" => $cantidad
        ];

        #Solo sumar al total si existe el chocolate en la BD
        if (!empty($precioDB) & $cantidad !== 0) {
            #Convertir de string a numero decimal (float)
            $precioDB = floatval($precioDB['precio']);

            #Sumarlo al total de precioChocolate
            $precioChocolate += $precioDB * $cantidad;
        }
    }

    if (empty($chocolatesDatos) & !empty($chocolates)) {
        mensaje("Debes agregar almenos un chocolate valido", 400);
    }

    //Obtener el precio total de todas las cajas

    $precioCajas = 0;

    #Porcada caja obtener el precio de la BD
    foreach ($cajasChocolates as $caja) {
        $cajaID = $caja['id'];
        $cantidad = intval($caja['cantidad'] ?? '');

        #Query solo para obtener el precio
        $query = "SELECT * from caja WHERE id=$cajaID";

        #Obtener el precio de la caja que esta en la bd
        $precioDB = mysqli_query($db, $query);
        $precioDB = mysqli_fetch_assoc($precioDB);

        $cajasDatos[] = [
            ...$precioDB,
            "cantidad" => $cantidad
        ];

        #Solo sumar al total si existe la caja en la BD
        if (!empty($precioDB) & $cantidad !== 0) {

            #Convertir de string a numero decimal (float)
            $precioDB = floatval($precioDB['precio']);

            #Sumarlo al total de precioCajas
            $precioCajas += $precioDB * $cantidad;
        }
    }

    if (empty($cajasDatos) & !empty($cajasChocolates)) {
        mensaje("Debes agregar almenos una caja valida", 400);
    }

    //Sumar el precio
    $precioTotal = $precioCajas + $precioChocolate;

    return [
        "precioTotal" => $precioTotal,
        "chocolates" => $chocolatesDatos,
        "cajas" => $cajasDatos,
    ];
}
