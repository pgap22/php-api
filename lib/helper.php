<?php  
function startsWith ($string, $startString){
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function obtener_valores_parametros($ruta_patron, $ruta_url) {
    $parametros_patron = array();
    $patron = preg_replace_callback('/:[^\/]+/', function ($coincidencia) use (&$parametros_patron) {
        $parametros_patron[] = str_replace(':', '', $coincidencia[0]);
        return '([^/]+)';
    }, $ruta_patron);

    $coincidencias = array();
    if (preg_match("#^$patron$#", $ruta_url, $coincidencias)) {
        array_shift($coincidencias);
        $parametros = array_combine($parametros_patron, $coincidencias);
        return $parametros;
    }
    return false;
}

function mensaje($mensaje, $code){

    http_response_code($code);

    echo json_encode([
        "message" => $mensaje
    ],JSON_UNESCAPED_UNICODE);

    exit();
}

function guardarImagen($files){
    $archivo = $files['imagen']["tmp_name"];

    $nombreCarpeta = "/imagenes";

    //Crear directorio de imagenes
    $carpetaNueva = "." . $nombreCarpeta;

    //En caso que ya exista omitr
    if(!is_dir($carpetaNueva)){
        //Crear la carpeta
        mkdir($carpetaNueva);
    }

    //Nombra de manera unica el nombre del archivo
    $nombreImagen = md5(uniqid()) . ".jpg";


    //Definir ruta del archivo de la imagen
    $ruta = $carpetaNueva . "/" . $nombreImagen;

    //Obtener la imagen desde los temporales
    move_uploaded_file($archivo, $ruta);

    return $nombreCarpeta . "/" . $nombreImagen;
}

function saveBase64Image($base64Image) {

  $nombreCarpeta = "/imagenes";

  //Crear directorio de imagenes
  $carpetaNueva = "." . $nombreCarpeta;

  //En caso que ya exista omitr
  if(!is_dir($carpetaNueva)){
      //Crear la carpeta
      mkdir($carpetaNueva);
  }

  // Obtener la extensión de la imagen
  $extension = explode('/', mime_content_type($base64Image))[1];

  // Decodificar la imagen en base64
  $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

  //Nombra de manera unica el nombre del archivo
  $nombreImagen = md5(uniqid());

  $rutaImagen = $carpetaNueva . "/" . $nombreImagen;

  // Guardar la imagen en un archivo en el servidor
  if (file_put_contents($rutaImagen.'.'.$extension, $imageData)) {
    return $nombreCarpeta . "/" . $nombreImagen . "." . $extension;
  } else {
      return false;
  }
}

function esImagen($str) {
    // Remove data URI scheme from base64 string
    $str = preg_replace('#^data:image/\w+;base64,#i', '', $str);

    // Decode the base64 string
    $decoded = base64_decode($str, true);

    // Check if the decoded data is an image
    if (!$decoded || !getimagesizefromstring($decoded)) {
        return false;
    }

    return true;
}
