<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Detectamos el entorno
$appEnv   = $_ENV['APP_ENV']   ?? $_SERVER['APP_ENV']   ?? 'production';
$basePath = dirname(__DIR__);

// Elegimos el fichero según el entorno
$envFile = $appEnv === 'testing' && file_exists($basePath . '/.env.testing')
    ? '.env.testing'
    : '.env';

// Cargamos las variables y permitimos que sobreescriban las de Docker
Dotenv::createMutable($basePath, $envFile)
    ->load();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

// $app->withFacades();

// $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like, or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory, it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that runs before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

$app->routeMiddleware([
    // ... otros middlewares ...
    'auth.token' => App\Http\Middleware\AuthMiddleware::class,
]);
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\RepositoryServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes' file so that they can all be added to
| the application. This will provide all the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

$app->withFacades();

if (! class_exists('DB')) {
    class_alias(\Illuminate\Support\Facades\DB::class, 'DB');
}

// Manager
/*
$app->singleton(
    App\Services\TokenManager::class,
    App\Services\TokenManager::class
);
$app->singleton(
    App\Services\TwitchManager::class,
    App\Services\TwitchManager::class
);
*/

// Repository
/*
$app->singleton(
    App\Repository\UserRepository::class,
    App\Repository\UserRepository::class
);
$app->singleton(
    App\Repository\StreamerRepository::class,
    App\Repository\StreamerRepository::class
);
$app->singleton(
    App\Repository\TopOfTheTopsRepository::class,
    App\Repository\TopOfTheTopsRepository::class
);
*/

// Services
/*
$app->singleton(
    App\Services\AuthService::class,
    App\Services\AuthService::class
);
$app->singleton(
    App\Services\EnrichedStreamsService::class,
    App\Services\EnrichedStreamsService::class
);
$app->singleton(
    App\Services\RegisterService::class,
    App\Services\RegisterService::class
);
$app->singleton(
    App\Services\StreamerService::class,
    App\Services\StreamerService::class
);
$app->singleton(
    App\Services\StreamsService::class,
    App\Services\StreamsService::class
);
$app->singleton(
    App\Services\TokenService::class,
    App\Services\TokenService::class
);
$app->singleton(
    App\Services\TopOfTheTopsService::class,
    App\Services\TopOfTheTopsService::class
);
$app->singleton(
    App\Services\TwitchAuthService::class,
    App\Services\TwitchAuthService::class
);
*/

// Validators
/*
$app->singleton(
    App\Validators\EnrichedStreamsValidator::class,
    App\Validators\EnrichedStreamsValidator::class
);
$app->singleton(
    App\Validators\RegisterValidator::class,
    App\Validators\RegisterValidator::class
);
$app->singleton(
    App\Validators\StreamerValidator::class,
    App\Validators\StreamerValidator::class
);
$app->singleton(
    App\Validators\StreamsValidator::class,
    App\Validators\StreamsValidator::class
);
$app->singleton(
    App\Validators\TokenValidator::class,
    App\Validators\TokenValidator::class
);
$app->singleton(
    App\Validators\TopOfTheTopsValidator::class,
    App\Validators\TopOfTheTopsValidator::class
);
*/

return $app;
