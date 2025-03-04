<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'Conexion.php';
require_once 'Actor.php';

class APIactor {

    public function api() {
        $datos = json_decode(file_get_contents("php://input"), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $codigoActor = $datos["cdactor"] ?? null;
                if ($codigoActor) {
                    $actorRecogido = Actor::verActor($codigoActor);
                    if ($actorRecogido) {
                        echo json_encode($actorRecogido);
                    } else {
                        echo json_encode(array("message" => "El actor no existe."), JSON_UNESCAPED_LINE_TERMINATORS);
                    }
                } else {
                    $actoresExistentes = Actor::listarActores();
                    if ($actoresExistentes) {
                        echo json_encode($actoresExistentes);
                    } else {
                        echo json_encode(array("message" => "No se encontraron actores."), JSON_UNESCAPED_LINE_TERMINATORS);
                    }
                }
                break;

            case 'POST':
                if (isset($datos["cdactor"], $datos["nombre"], $datos["sexo"], $datos["cdgrupo"])) {
                    $codigoActor = $datos["cdactor"];
                    $nombreActor = $datos["nombre"];
                    $sexoActor = $datos["sexo"];
                    $codigoGrupo = $datos["cdgrupo"];

                    $actor = new Actor($codigoActor, $nombreActor, $sexoActor, $codigoGrupo);

                    if ($actor->guardarActor()) {
                        echo json_encode(array("message" => "Actor insertado correctamente."), JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(array("message" => "El actor no se ha podido insertar."), JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    echo json_encode(array("message" => "Faltan datos para insertar el actor."), JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'PUT':
                if (isset($datos["cdactor"], $datos["nombre"], $datos["sexo"], $datos["cdgrupo"])) {
                    $codigoActor = $datos["cdactor"];
                    $nombreActor = $datos["nombre"];
                    $sexoActor = $datos["sexo"];
                    $codigoGrupo = $datos["cdgrupo"];

                    $actor = new Actor($codigoActor, $nombreActor, $sexoActor, $codigoGrupo);

                    if ($actor->guardarActor()) {
                        echo json_encode(array("message" => "Actor actualizado correctamente."), JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(array("message" => "El actor no se ha actualizado."), JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    echo json_encode(array("message" => "Faltan datos para actualizar al actor."), JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'DELETE':
                $codigoActor = $datos["cdactor"];
                // Eliminar al actor recibido por parámetros
                if (Actor::eliminarActor($codigoActor)) {
                    echo json_encode(array("message" => "Actor eliminado correctamente."), JSON_UNESCAPED_LINE_TERMINATORS);
                } else {
                    echo json_encode(array("message" => "El actor  no se ha eliminado."), JSON_UNESCAPED_LINE_TERMINATORS);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(array("message" => "Método no permitido."), JSON_UNESCAPED_UNICODE);
        }
    }
}
