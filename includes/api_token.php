<?php

	if (!defined('ABSPATH')) exit;

	$isAuth = get_option('bpetr_is_auth');
    $propertyId = get_option('bpetr_property_id');
    $bpUsername = get_option('bpetr_username');
    $bpToken = get_option('bpetr_token');

    if ($propertyId != null && $propertyId != '' && $isAuth == 1 && $bpUsername != '') {
    	if ($bpToken != '') {
    		$route = sanitize_url(
    			sprintf("%s/api/v1/website/%s/token-validity", BPETR_Admin::$bpetrApiUrl, $propertyId)
    		);
    		
	    	$data = wp_remote_get(
	            $route, [
	                'headers' => ["Authorization" => sprintf("Bearer %s", $bpToken)],
	                'timeout' => 30
	            ]
	        );

	        if (isset($data->errors) && count($data->errors) > 0) {
	        	BPETR_Admin::$bpetrServerState = 0;
	        	return;
	        }

	    	if (isset($data['body'])) {
	    		$data = json_decode($data['body']);
	    		if (isset($data->code) && $data->code == '401') {
	    			bpetr_getApiToken($bpUsername);
	    		}
	    	}
    	} else {
	    	bpetr_getApiToken($bpUsername);
		}
    }

    function bpetr_getApiToken($bpUsername)
    {
    	$requestBody = [
    		'username' => $bpUsername,
    		'password' => $bpUsername
    	];

    	$headers = [
    		'Content-Type' => 'application/json'
    	];

    	$route = sprintf("%s/api/login_check", BPETR_Admin::$bpetrApiUrl);

    	$data = wp_remote_post(
	        sanitize_url($route), [
	            'headers' => $headers,
	            'body' => json_encode($requestBody)
	        ]
	    );

	    if (isset($data['body'])) {
	    	$response = json_decode($data['body']);
	    	if (isset($response->token)) {
	    		update_option( 'bpetr_token', $response->token );
	    	}
	    }
    }
