<?php
// MIME types archivos permitidos
$MIME_PERMITIDOS = [
    'application/pdf' => 'pdf',
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
];

// Tamaño máximo: 5MB en bytes
define('TAMANIO_MAXIMO', 5 * 1024 * 1024);

/**
 * Valida un archivo subido de forma segura.
 * @param string $tmp_name  Ruta temporal del archivo
 * @param string $nombre_original  Nombre original del archivo
 * @return string  Extensión validada (pdf, jpg, png)
 * @throws Exception  Si el archivo no es válido
 */
function validarArchivo($tmp_name, $nombre_original) {
    global $MIME_PERMITIDOS;

    // 1. Verificar tamaño
    $tamanio = filesize($tmp_name);
    if ($tamanio > TAMANIO_MAXIMO) {
        $mb = round($tamanio / 1024 / 1024, 2);
        throw new Exception("El archivo '{$nombre_original}' pesa {$mb}MB. El máximo permitido es 5MB.");
    }

    // 2. Validar MIME type real del archivo (no confiar en la extensión)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_real = $finfo->file($tmp_name);

    if (!array_key_exists($mime_real, $MIME_PERMITIDOS)) {
        throw new Exception("El archivo '{$nombre_original}' no es válido. Solo se permiten PDF, JPG y PNG.");
    }

    // 3. Verificar que la extensión del nombre coincida con el MIME real
    $extension_nombre = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    $extension_mime   = $MIME_PERMITIDOS[$mime_real];

    // jpeg y jpg son equivalentes
    if ($extension_nombre === 'jpeg') $extension_nombre = 'jpg';

    if ($extension_nombre !== $extension_mime) {
        throw new Exception("El archivo '{$nombre_original}' tiene una extensión incorrecta.");
    }

    return $extension_mime;
}