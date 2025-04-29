<?php

namespace App\TwitchApi;

function callTwitchApi(string $url): array
{
    $clientId = "pl90uakzou662frdn51bgohgalbxj5";
    $clientSecret = "adjy51fk8zhihbi3qnacmcwqij3qm0";

    $twitchUrl = "https://id.twitch.tv/oauth2/token";
    $data = http_build_query([
        "client_id" => $clientId,
        "client_secret" => $clientSecret,
        "grant_type" => "client_credentials"
    ]);

    $headers = [
        "Content-Type: application/x-www-form-urlencoded",
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $twitchUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($curl);

    if ($response === false) {
        http_response_code(500);
        return ['error' => 'Internal server error.'];
    }
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $data = json_decode($response, true);

    if ($httpCode !== 200 || !isset($data['access_token'])) {
        return ["error" => "Internal server error."];
    }

    $oauthToken = $data['access_token'];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/../cacert.pem');
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $oauthToken",
        "Client-Id: $clientId",
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        http_response_code(500);
        return ['error' => 'Internal server error.'];
    }

    curl_close($curl);

    return json_decode($response, true);
}