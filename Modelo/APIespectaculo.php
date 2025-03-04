<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'Conexion.php';
require_once 'Espectaculos.php';

class APIespectaculo {

    public function api() {
        // Decodificar el cuerpo de la solicitud en formato JSON
        $request = json_decode(file_get_contents("php://input"), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $codigoEspectaculo = $request["cdespec"] ?? null;
                if ($codigoEspectaculo) {
                    $espectaculosRecogido = Espectaculos::buscarEspectaculo($codigoEspectaculo);
                    if ($espectaculosRecogido) {
                        echo json_encode($espectaculosRecogido);
                    } else {
                        echo json_encode(array("message" => "El espectáculo no existe."), JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $espectaculosExistentes = Espectaculos::buscarEspectaculos();
                    if ($espectaculosExistentes) {
                        echo json_encode($espectaculosExistentes);
                    } else {
                        echo json_encode(array("message" => "No se encontraron espectáculos."), JSON_UNESCAPED_UNICODE);
                    }
                }
                break;

            case 'POST':
                if (isset($request["cdespec"], $request["nombre"], $request["cdgru"])) {
                    $codigoEspectaculo = $request["cdespec"];
                    $nombreEspectaculo = $request["nombre"];
                    $codigoGrupoEspectaculo = $request["cdgru"];

                    $espectaculo = new Espectaculos($codigoEspectaculo, $nombreEspectaculo, $codigoGrupoEspectaculo);

                    if ($espectaculo->altaEspectaculos()) {
                        echo json_encode(array("message" => "Espectáculo insertado correctamente."), JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(array("message" => "El espectáculo no se ha podido insertar."), JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    echo json_encode(array("message" => "Faltan datos para insertar el espectáculo."), JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'PUT':
                if (isset($request["cdespec"], $request["nombre"], $request["cdgru"])) {
                    $codigoEspectaculo = $request["cdespec"];
                    $nombreEspectaculo = $request["nombre"];
                    $codigoGrupoEspectaculo = $request["cdgru"];

                    $espectaculo = new Espectaculos($codigoEspectaculo, $nombreEspectaculo, $codigoGrupoEspectaculo);

                    if ($espectaculo->altaEspectaculos()) {
                        echo json_encode(array("message" => "Espectáculo actualizado correctamente."), JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(array("message" => "Espectáculo no se ha actualizado."), JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    echo json_encode(array("message" => "Faltan datos para actualizar el espectáculo."), JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'DELETE':
                $codigoEspectaculo = $request["cdespec"];
                if ($codigoEspectaculo && Espectaculos::eliminarEspectaculo($codigoEspectaculo)) {
                    echo json_encode(array("message" => "Espectáculo eliminado correctamente."), JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(array("message" => "Espectáculo no se ha eliminado."), JSON_UNESCAPED_UNICODE);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(array("message" => "Método no permitido."), JSON_UNESCAPED_UNICODE);
        }
    }
}
