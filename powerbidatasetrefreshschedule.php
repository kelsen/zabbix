#!/usr/bin/php
<?php

/**
 * This is script is used by zabbix LLD to connect to Power BI API
 * and retrieve dataset refresh schedule status.
 *
 * @author Kelsen Faria <kelsencrist@gmail.com>
 **/

require_once 'powerbiauthentication.php';
require_once 'HTTP/Request2.php';

$group_id = $argv[1];
$dataset_id = $argv[2];

$bearer = getAccessToken();

if ($bearer) {

	$request = new HTTP_Request2();
	$request->setUrl('https://api.powerbi.com/v1.0/myorg/groups/' . $group_id . '/datasets/' . $dataset_id . '/refreshSchedule');
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	$request->setHeader(array(
		'Authorization' => 'Bearer ' . $bearer
	));
	try {
		$response = $request->send();
		if ($response->getStatus() == 200) {
			$bodyAsArray = json_decode($response->getBody(), false);
			echo ($bodyAsArray->enabled ? 1 : 0); // Exibe "false"
		} else {
			echo 'Unexpected API Request HTTP status: ' . $response->getStatus() . ' ' .
				$response->getReasonPhrase() .' '. $response->getBody();
		}
	} catch (HTTP_Request2_Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}
}

