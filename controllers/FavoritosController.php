<?php

$alternarChocolateFav = function ($db, $body, $params) {
    //Obtener el id del chocolate
    $chocolateID = $params['id'] ?? '';
    //Obtener el id del usuario
    $usuarioID = $_REQUEST['user']['id'];

    //Validar si el chocolate existe
    $query = "SELECT * FROM chocolate WHERE id = $chocolateID";
    $chocolateDatos = mysqli_query($db, $query);
    $chocolateDatos = mysqli_fetch_assoc($chocolateDatos);

    if (empty($chocolateDatos)) {
        mensaje("El chocolate no existe", 400);
    }

    //Ver si el chocolate ya esta agregado para eliminarlo
    $query = "SELECT * FROM favoritosusuariochocolates WHERE id_usuario = $usuarioID AND id_chocolate = $chocolateID";
    $esFavorito = mysqli_query($db, $query);
    $esFavorito = mysqli_fetch_assoc($esFavorito);

    if (!empty($esFavorito)) {
        $query = "DELETE FROM favoritosusuariochocolates WHERE id_usuario = $usuarioID AND id_chocolate = $chocolateID";
        mysqli_query($db, $query);

        //Mostrar mensaje de añadido a favoritos
        mensaje("Chocolate eliminado de favoritos !", 200);
    }

    //Si no esta agregado a favoritos agregarlo
    $query = "INSERT INTO favoritosusuariochocolates(id_usuario,id_chocolate) VALUES($usuarioID,$chocolateID)";
    mysqli_query($db,$query);
    
    //Mostrar mensaje de añadido a favoritos
    mensaje("Chocolate añadido a favoritos !", 200);
};

$obtenerChocolatesFav = function ($db, $body) {
    //Obtener el id de usuario
    $usuarioID = $_REQUEST['user']['id'];

    //Obtener todos los favs del usuario
    $query = "SELECT * FROM favoritosusuariochocolates WHERE id_usuario = $usuarioID";
    $chocolateFav = mysqli_query($db, $query);
    $chocolateFav = mysqli_fetch_all($chocolateFav, MYSQLI_ASSOC);

    //Sacar los detalles de los chocolate favs
    $misChocolatesFav = [];
    foreach ($chocolateFav as $chocolate) {
        $chocolateID = $chocolate['id_chocolate'];
        $query = "SELECT * FROM chocolate WHERE id = $chocolateID";

        $chocolateDatos = mysqli_query($db, $query);
        $chocolateDatos = mysqli_fetch_assoc($chocolateDatos);

        $misChocolatesFav[] = $chocolateDatos;
    }

    //Mostrar esos datos en json
    echo json_encode($misChocolatesFav, JSON_UNESCAPED_UNICODE);
};
