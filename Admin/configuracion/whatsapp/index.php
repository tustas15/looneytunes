<?php
//TOKEN QUE NOS DA FACEBOOK
$token = 'EAAOq2GY6o5QBOyHci6ey2K05twoGQpYfizbfzBnydPjp8ypsKrCalJqIC8Ryy01o0ax24TcpHdWfPZBZB02LXkA5UgSPLLiB3ZA4Km99qL8ZApPxiE5SEWhGFTNkHaaYbxFEA4y8s7zhOeWjAB55XUc2YuU7mQarloyIYwoUFsQXWG3pKesLzyS3fCzxZB3NZBthJblZCjhFRiOZA5ZBr';
//NUESTRO TELEFONO
$telefono = '593963060020';
//URL A DONDE SE MANDARA EL MENSAJE
$url = 'https://graph.facebook.com/v19.0/345094562024872/messages';

// Obtener los parámetros
$NOM = $_POST['nombre'] ?? 'Nombre no proporcionado';
$VAL = $_POST['valor'] ?? '0.00';

//CONFIGURACION DEL MENSAJE
$mensaje = ''
    . '{'
    . '"messaging_product": "whatsapp", '
    . '"to": "' . $telefono . '", '
    . '"type": "template", '
    . '"template": '
    . '{'
    . '     "name": "pagoitsi",'
    . '     "language":{ "code": "es" }, '
    . '"components": ['
    . '{'
    . '"type": "body",'
    . '"parameters": ['
    . '{'
    . '"type": "text",'
    . '"text": "' . $NOM . '"'
    . '},'
    . '{'
    . '"type": "text",'
    . '"text": "' . $VAL . '"'
    . '}'
    . ']'
    . '}'
    . ']'
    . '} '
    . '}';

//DECLARAMOS LAS CABECERAS
$header = array("Authorization: Bearer " . $token, "Content-Type: application/json",);

//INICIAMOS EL CURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
$response = json_decode(curl_exec($curl), true);

//IMPRIMIMOS LA RESPUESTA 
print_r($response);

//OBTENEMOS EL CODIGO DE LA RESPUESTA
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//CERRAMOS EL CURL
curl_close($curl);
?>
