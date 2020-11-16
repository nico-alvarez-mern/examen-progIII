<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require('../src/Models/Alumno.php');
use App\Models\Alumno;

class AlumnoController {

    public function getAll (Request $request, Response $response, $args) {
        $rta = Alumno::get();

        echo($_POST['tipo']);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args)
    {
        $rta = Alumno::find($args['id']);
        if($rta == null){
            $response->getBody()->write(json_encode([]));
            return $response;
        }

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args)
    {

        $user = new Alumno;

        $user->nombre = "Pepe";
        $user->apellido = "pepe@gmail.com";
        $user->tipo = 3;
        $user->clave = '123456';

        $rta = $user->save();

        
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function updateOne(Request $request, Response $response, $args)
    {
        $user = Alumno::find(10);

        $user->usuario = "Pepe Grillo!!";

        $rta = $user->save();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function deleteOne(Request $request, Response $response, $args)
    {
        $user = Alumno::find(10);

        $rta = $user->delete();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

}