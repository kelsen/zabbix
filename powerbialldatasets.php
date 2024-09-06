#!/usr/bin/php
<?php

/**
 * This is script is used by zabbix LLD to connect to Power BI API
 * and retrieve workspaces dataset list.
 *
 * @author Kelsen Faria <kelsencrist@gmail.com>
 **/
require_once 'powerbiauthentication.php';
require_once 'HTTP/Request2.php';

$workspaces = explode('|', $argv[1]);

$bearer = getAccessToken();

if ($bearer) {

	$datasets = [];
	//loop through defined workspaces to get dataset list
	foreach ($workspaces as $w) {

		$request = new HTTP_Request2();
		$request->setUrl('https://api.powerbi.com/v1.0/myorg/groups/' . $w . '/datasets');
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

				$datasetsArray = json_decode($response->getBody(), true)['value'];
				// Filtra os datasets e adiciona o campo "group"
       		        	foreach ($datasetsArray as $dataset) {
	                        // Extrai o grupo da URL usando expressão regular
       		        	        preg_match('/groups\/([a-z0-9-]+)\//', $dataset['webUrl'], $matches);
       	        		        $group = $matches[1];

	        	                // Mantém apenas os campos desejados e adiciona o campo "group"
        		                $filteredDataset = [
       	                		    'id' => $dataset['id'],
		                            'name' => $dataset['name'],
	                        	    'webUrl' => $dataset['webUrl'],
	                	            'group' => $group // Adiciona o campo "group"
	        	                ];
		
	                	        // Adiciona ao array final
	        	                $datasets[] = $filteredDataset;
		                }
			} else {
				echo 'Unexpected API HTTP status: ' . $response->getStatus() . ' ' .
					$response->getReasonPhrase() . ' ' . $responde->getBody();
			}
		} catch (HTTP_Request2_Exception $e) {
			echo 'Error: ' . $e->getMessage();
		}
	}
	//build json format required by zabbix
       	echo json_encode($datasets, JSON_PRETTY_PRINT);
}
