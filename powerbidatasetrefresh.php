#!/usr/bin/php
<?php

/**
 * This is script is used by zabbix LLD to connect to Power BI API
 * and retrieve dataset refresh status.
 *
 * @author Kelsen Faria <kelsencrist@gmail.com>
 **/

require_once 'powerbiauthentication.php';
require_once 'HTTP/Request2.php';

#$groupid_parse = explode('/', parse_url(str_replace('\\', '', $argv[1]), PHP_URL_PATH));
#$group_id = $groupid_parse[2];
#$datasetid_parse = explode('/', parse_url(str_replace('\\', '', $argv[1]), PHP_URL_PATH));
#$dataset_id = $datasetid_parse[4];

$group_id = $argv[1];
$dataset_id = $argv[2];

$bearer = getAccessToken();

if ($bearer) {

	$request = new HTTP_Request2();
	$request->setUrl('https://api.powerbi.com/v1.0/myorg/groups/' . $group_id . '/datasets/' . $dataset_id . '/refreshes?$top=1');
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
			$bodyAsArray = json_decode($response->getBody(), true);
			echo $bodyAsArray['value'][0]['status'];
		} else {
			echo 'Unexpected API HTTP status: ' . $response->getStatus() . ' ' .
				$response->getReasonPhrase() .' '. $response->getBody();
		}
	} catch (HTTP_Request2_Exception $e) {
		echo 'Error: ' . $e->getMessage();
	}
}
