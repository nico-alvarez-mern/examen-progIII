<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require('../src/Models/Materia.php');
require('../src/Models/AlumnoMateria.php');
require('../src/Models/AlumnoNotas.php');
use App\Models\User;
use App\Models\Materia;
use App\Models\AlumnoMateria;
use App\Models\AlumnoNota;

class MateriaController {

    public function addOne (Request $request, Response $response, $args) {
        $tipo = $_POST['tipo'];
        $body = $request->getParsedBody();
        if($tipo !== 'admin'){
            $response->getBody()->write('No tienes permisos para agregar una nueva materia');
            return $response;
        }
        if(!$this->validatorPost(['materia','cuatrimestre','cupos'],$response)){
            return $response;
        }
        $materia = new Materia;
        $materia->materia = $body['materia'];
        $materia->cuatrimestre = $body['cuatrimestre'];
        $materia->cupos = $body['cupos'];
        if( $materia->save() ){
            $response->getBody()->write('Materia creada con exito');
            return $response->withStatus(201);
        }else{
            $response->getBody()->write('No se pudo crear la materia');
            return $response->withStatus(500);
        }
    }

    public function inscription(Request $request, Response $response, $args)
    {
        $tipo = $_POST['tipo'];
        if($tipo !== 'alumno'){
            $response->getBody()->write('Solo alumnos pueden inscribirse a materias');
            return $response->withStatus(404);
        }
        $alumnoMateria = new AlumnoMateria;    
        $alumnoMateria->idAlumno = $_POST['idAlumno'];    
        $alumnoMateria->idMateria = $args['idMateria'];
        if($alumnoMateria->save()){
            $response->getBody()->write('Inscripcion a materia exitosa');
            return $response;
        }else{
            $response->getBody()->write('No pudimos realizar la inscripcion');
            return $response->withStatus(500);
        }
    }

    public function addScore(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $tipo = $_POST['tipo'];
        if($tipo !== 'profesor'){
            $response->getBody()->write('Solo Profesores pueden asignar notas');
            return $response->withStatus(404);
        }
        $alumnoNota = new AlumnoNota;
        $alumnoNota->nota = $body['nota'];
        $alumnoNota->idAlumno = $body['idAlumno'];
        $alumnoNota->idMateria = $args['idMateria'];
        if($alumnoNota->save()){
            $response->getBody()->write('Nota asignada con exito');
            return $response;
        }else{
            $response->getBody()->write('No pudimos asignar la nota');
            return $response->withStatus(500);
        }
    }

    public function getInscriptions(Request $request, Response $response, $args)
    {
        $tipo = $_POST['tipo'];
        if($tipo == 'alumno'){
            $response->getBody()->write('Los alumnos no pueden ver las inscripciones');
            return $response->withStatus(404);
        }
        $alumnos = array();
        $rta = AlumnoMateria::where('idMateria','=',$args['idMateria'])->get();
        foreach ($rta as $key => $value) {
            $alumno = User::where('id','=',$value['idAlumno'])->get()->first();
            array_push($alumnos,$alumno);
        }
        $response->getBody()->write(json_encode($alumnos));
        return $response;
    }

    public function getMateriaScore(Request $request, Response $response, $args)
    {
        $notas = array();
        $rta = Materia::where('id','=',$args['idMateria'])->get();
        foreach ($rta as $key => $value) {
            $nota = AlumnoNota::where('idMateria','=',$value['id'])->get()->first();
            array_push($notas,$nota);
        }
        $response->getBody()->write(json_encode($notas));
        return $response;
    }
    
    public function getAll(Request $request, Response $response, $args)
    {
        $materias = Materia::get();

        $response->getBody()->write(json_encode($materias));
        return $response;
    }

    private function validatorPost($params,$response){
        $retorno = true;
        for ($i=0; $i < count($params); $i++) { 
            if( !isset($_POST[$params[$i]]) ){
                $response->getBody()->write("El $params[$i] es obligatorio");
                $retorno = false;
                break;
            }
        }
        return $retorno;
    }
}