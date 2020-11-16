<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Config\Database;

require __DIR__ . '/../vendor/autoload.php';

require('../src/Middlewares/AuthMiddleware.php');
require('../src/Middlewares/JsonMiddleware.php');
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;

require('../src/Controllers/UsuarioController.php');
require('../src/Controllers/LoginController.php');
use App\Controllers\AlumnoController;
use App\Controllers\LoginController;


$conn = new Database;

$app = AppFactory::create();
$app->setBasePath('/php/examen-prog/public');

$app->group('/user', function (RouteCollectorProxy $group) {

    $group->post('/login[/]', LoginController::class . ":login");
    
    $group->post('/create[/]', LoginController::class . ":create");
    

})->add(new JsonMiddleware);

$app->group('/alumnos', function (RouteCollectorProxy $group) {

    $group->get('[/]', AlumnoController::class . ":getAll");
    
    $group->post('[/]', AlumnoController::class . ":addOne");
    
    $group->get('/{id}', AlumnoController::class . ":getOne");

    $group->put('/{id}', AlumnoController::class . ":updateOne");

    $group->delete('/{id}', AlumnoController::class . ":deleteOne");

})->add(new AuthMiddleware)->add(new JsonMiddleware);




// ->add(function (Request $request, RequestHandler $handler) {
//     $response = $handler->handle($request);
//     // $existingContent = (string) $response->getBody();

//     // $response = new Response();
//     $response = $response->withHeader('Content-type', 'application/json');

//     return $response;
// });

$app->run();
