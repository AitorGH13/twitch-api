<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*$router->post('/register', function (\Illuminate\Http\Request $request) {
    return response()->json(['message' => 'User registered successfully', 'email' => $request->input('email')]);
});*/
$router->post('/register', 'RegisterController');  // invoca __invoke

/*$router->post('/token', function (\Illuminate\Http\Request $request) {
    return response()->json(['token' => 'generated_token', 'email' => $request->input('email')]);
});*/
$router->post('/token',    'TokenController');     // invoca __invoke

/*$router->get('/analytics/user', function (\Illuminate\Http\Request $request) {
    $userId = $request->input('id');
    return response()->json(['id' => $userId, 'name' => 'Sample User']);
});*/
$router->get('/analytics/user', 'UserController');

/*$router->get('/analytics/streams', function () {
    return response()->json(['streams' => ['stream1', 'stream2']]);
});*/
$router->get('/analytics/streams', 'StreamsController');

/*$router->get('/analytics/topsofthetops', function () {
    return response()->json(['top_games' => ['game1', 'game2', 'game3']]);
});*/
$router->get('/analytics/topsofthetops', 'TopOfTheTopsController');

$router->get('/analytics/streams/enriched', function (\Illuminate\Http\Request $request) {
    $limit = $request->input('limit', 3);
    return response()->json(['enriched_streams' => array_fill(0, $limit, 'enriched_stream')]);
});
