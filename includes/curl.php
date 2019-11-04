<?php

function minutemailer_curl_send_request($data, $endpoint, $httpMethod, $authorization = null)
{
    $ch = curl_init();

    if ($httpMethod == 'post') {
        curl_setopt($ch, CURLOPT_POST, true);
    }
    elseif ($httpMethod == 'put') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    }
    elseif ($httpMethod == 'get') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    if ($data != '') {
        $post_json = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    if($authorization != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    //$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);
    if($curl_errors) {
        echo "CURL Error: ".$curl_errors."\n";
    }
    curl_close($ch);

    return json_decode($response);
}