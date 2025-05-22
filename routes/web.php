<?php

/** @var Router $router */

// Añade este bloque de comentarios al inicio del archivo
/**
 * @uses \App\Http\Controllers\RegisterController
 * @uses \App\Http\Controllers\TokenController
 * @uses \App\Http\Controllers\TopOfTheTopsController
 * @uses \App\Http\Controllers\StreamsController
 * @uses \App\Http\Controllers\EnrichedStreamsController
 * @uses \App\Http\Controllers\StreamerController
 */


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It is a breeze. Tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', 'RegisterController');
$router->post('/token', 'TokenController');

$router->group(['middleware' => 'auth.token'], function () use ($router) {
    $router->get('/analytics/topsofthetops', 'TopOfTheTopsController@list');
    $router->get('/analytics/streams', 'StreamsController@index');
    $router->get('/analytics/streams/enriched', 'EnrichedStreamsController@index');
    $router->get('/analytics/user', 'StreamerController@profile');
});
