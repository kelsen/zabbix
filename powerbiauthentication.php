#!/usr/bin/php
<?php
require_once 'HTTP/Request2.php';

function getAccessToken() {
	
    $tenant_id = '';
    $client_id = '';
    $client_secret = '';
    
    $request = new HTTP_Request2();
    $request->setUrl('https://login.microsoftonline.com/' . $tenant_id . '/oauth2/token');
    $request->setMethod(HTTP_Request2::METHOD_POST);
    $request->setConfig(array(
        'follow_redirects' => TRUE
    ));
    $request->setHeader(array(
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Cookie' => 'fpc=An6WDyp3VFdBi1iajHEoJco2FzvnAQAAAKRvfNsOAAAA; stsservicecookie=estsfd; x-ms-gateway-slice=estsfd'
    ));
    $request->addPostParameter(array(
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'resource' => 'https://analysis.windows.net/powerbi/api'
    ));

    try {
	$response = $request->send();
        if ($response->getStatus() == 200) {
            $bodyAsArray = json_decode($response->getBody(), true);
            return $bodyAsArray['access_token']; // Retorna o token de acesso
        } else {
            echo 'Unexpected Authentication HTTP status: ' . $response->getStatus() . ' ' .
                $response->getReasonPhrase() . ' ' . $response->getBody();
            return null; // Retorna null em caso de erro
        }
    } catch (HTTP_Request2_Exception $e) {
        echo 'Error: ' . $e->getMessage();
        return null; // Retorna null em caso de exceção
    }
}

?>
