<?php  

/**
 * Summary of startsWith
 * @param string $string
 * @param string $startString
 * @return bool
 */
function startsWith ($string, $startString)
{
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


?>