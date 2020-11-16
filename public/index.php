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

require('../src/Controllers/MateriaController.php');
require('../src/Controllers/LoginController.php');
use App\Controllers\MateriaController;
use App\Controllers\LoginController;


$conn = new Database;

$app = AppFactory::create();
$app->setBasePath('/php/examen-prog/public');

$app->group('/', function (RouteCollectorProxy $group) {

    $group->post('login[/]', LoginController::class . ":login");

    $group->post('users[/]', LoginController::class . ":create");
    

})->add(new JsonMiddleware);

$app->group('/materia', function (RouteCollectorProxy $group) {

    $group->post('[/]', MateriaController::class . ":addOne");
    
    $group->post('/inscripcion/{idMateria}', MateriaController::class . ":inscription");
    
    $group->post('/notas/{idMateria}', MateriaController::class . ":addScore");

    $group->get('/inscripcion/{idMateria}', MateriaController::class . ":getInscriptions");

    $group->get('[/]', MateriaController::class . ":getAll");

    $group->get('/notas/{idMateria}', MateriaController::class . ":getMateriaScore");

})->add(new AuthMiddleware)->add(new JsonMiddleware);

$app->run();
