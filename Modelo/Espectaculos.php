<?php

require_once 'Conexion.php';
require_once 'funcionesBaseDeDatos.php';
require_once './Controlador/funcionesValidacion.php';

/**
 * Description of Espectaculos
 * Clase Espectaculos que crea un objeto espectaculo.
 * @author José Martínez Estrada
 */
class Espectaculos {

    //Atributos Estáticos
    private static int $numEspectaculos = 0;
    //Atributos Privados
    private string $cdespec;
    private string $cdgru;
    //Atributos Públicos
    private string $nombre;
    private string $tipo;
    private string $descripción;
    private int $estrellas;

    //Atributos Protegidos

    /**
     * Constructor de la clase Espectaculos que crea un espectaculo con los campos requeridos de la base de datos 
     * @param type $cdespec código del espectáculo
     * @param type $nombre nombre del espectáculo
     * @param type $cdgru código del grupo que interpretaá el espectáculo 
     */
    public function __construct($cdespec, $nombre, $cdgru) {
        $this->cdespec = validarCadena($cdespec);
        $this->nombre = validarCadena($nombre);
        $this->cdgru = validarNumero($cdgru);
        $this->tipo = "teatro";
        $this->estrellas = 0;
        $this->descripción = "";
        self::$numEspectaculos++;
    }

    //Métodos getter y setter
    public static function getNumEspectaculos() {
        return self::$numEspectaculos;
    }

    public function getCdespec() {
        return $this->cdespec;
    }

    public function getCdgru() {
        return $this->cdgru;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getDescripción() {
        return $this->descripción;
    }

    public function getEstrellas() {
        return $this->estrellas;
    }

    public function setTipo(string $tipo) {
        $this->tipo = $tipo;
    }

    public function setDescripción(string $descripción) {
        $this->descripción = $descripción;
    }

    public function setEstrellas(int $estrellas) {
        $this->estrellas = $estrellas;
    }

    //Métodos de la base de datos

    /**
     * Método que da de alta o actualiza un espectaculo a la base de datos
     * @param type $espectaculo objeto espectaculo que será el nuevo espectaculo que queremos crear en la base de datos
     * @return string devuelve si se ha realizado o no correctamente la inserción
     */
    public function altaEspectaculos() {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $esValido = false;
        if (!$conexionBD || $conexionBD->connect_error) {
            echo "Error en la conexión: " . $conexionBD->connect_error;
        }
        $codigoEspectaculo = $this->getCdespec();
        $nombreEspectaculo = $this->getNombre();
        $tipoEspectaculo = $this->getTipo();
        $estrellasEspectaculo = $this->getEstrellas();
        $codigoGrupo = $this->getCdgru();
        if (noExisteCodigoEspectaculo($codigoEspectaculo, $conexionBD)) {
            try {
                // Inserción del nuevo espectaculo
                $consultaEspectaculo = "INSERT INTO espectaculo (cdespec, nombre, tipo, estrellas, cdgru) VALUES (?, ?, ?, ?, ?)";
                $consultaPreparada = $conexionBD->prepare($consultaEspectaculo);
                $consultaPreparada->bind_param("sssis", $codigoEspectaculo, $nombreEspectaculo, $tipoEspectaculo, $estrellasEspectaculo, $codigoGrupo);
                if ($consultaPreparada->execute()) {
                    $esValido = true;
                }
            } catch (Exception $ex) {
                echo "ERROR: " . $ex->getMessage();
            }
        } else {
            // Si el código existe, procedemos  a actualizar
            try {
                $consultaActualizacion = "UPDATE espectaculo SET nombre = ?, tipo = ?, estrellas = ?, cdgru = ? WHERE cdespec = ?";
                $consultaPreparada = $conexionBD->prepare($consultaActualizacion);

                $consultaPreparada->bind_param("ssiss", $nombreEspectaculo, $tipoEspectaculo, $estrellasEspectaculo, $codigoGrupo, $codigoEspectaculo);

                if ($consultaPreparada->execute()) {
                    $esValido = true;
                }
            } catch (Exception $ex) {
                echo "ERROR: " . $ex->getMessage();
            }
        }
        $consultaPreparada->close();
        return $esValido ? true : false;
    }

    /**
     * Método que elimina un espectáculo existente en la base de datos
     * @param type $codigoEspectaculo el código del espectaculo que queremos eliminar
     * @return string devuelve una cadena de texto en el caso de que se realice correctamente u ocurra algún error
     */
    public static function eliminarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $esValido = false;

        // Verificamos si el código del espectáculo existe en la base de datos
        if (!noExisteCodigoEspectaculo($codigoEspectaculo, $conexionBD)) {
            try {
                $consultaEliminarEspectaculos = $conexionBD->stmt_init();

                // Preparamos la consulta SQL para eliminar el espectáculo
                $consultaEliminarEspectaculos->prepare("DELETE FROM espectaculo WHERE cdespec = ?");

                // Asociamos el parámetro a la consulta
                $consultaEliminarEspectaculos->bind_param("s", $codigoEspectaculo);

                // Ejecutamos la consulta
                if ($consultaEliminarEspectaculos->execute()) {
                    $esValido = true;
                }
                // Cerramos la consulta
                $consultaEliminarEspectaculos->close();
            } catch (Exception $ex) {
                echo "ERROR: ", $ex->getMessage();
            }
        }
        return $esValido ? true : false;
    }

    /**
     * Método que busca el espectáculo existente con todos sus campos
     * @param type $codigoEspectaculo  el código del espectaculo del que queremos observar sus campos
     * @return array|bool devuelve un array con los datos del espectáculo o false si no existe
     */
    public static function buscarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $esValido = false;
        if (!$conexionBD || $conexionBD->connect_error) {
            echo "Error en la conexión: " . $conexionBD->connect_error;
        }
        try {
            // Verificar si el espectáculo existe
            $consultaExiste = $conexionBD->prepare("SELECT * FROM espectaculo WHERE cdespec = ?");
            $consultaExiste->bind_param("s", $codigoEspectaculo);
            $consultaExiste->execute();
            $resultado = $consultaExiste->get_result();

            if ($resultado->num_rows > 0) {
                $esValido = true;
                // Obtener los datos como array asociativo
                $datosEspectaculo = $resultado->fetch_assoc();
                $consultaExiste->close();
            }
        } catch (Exception $ex) {
            echo "ERROR: " . $ex->getMessage();
        }
        return $esValido ? $datosEspectaculo : false;
    }

    public static function mostrarEspectaculo($codigoEspectaculo) {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        if (!$conexionBD || $conexionBD->connect_error) {
            echo "Error en la conexión: " . $conexionBD->connect_error;
        }
        try {
            // Verificar si el espectáculo existe
            $consultaExiste = $conexionBD->prepare("SELECT * FROM espectaculo WHERE cdespec = ?");
            $consultaExiste->bind_param("s", $codigoEspectaculo);
            $consultaExiste->execute();
            $resultado = $consultaExiste->get_result();

            if ($resultado->num_rows > 0) {
                // Obtener los datos
                $datosEspectaculo = $resultado->fetch_assoc();

                foreach ($datosEspectaculo as $campo => $valor) {
                    $espectaculo .= "$campo: $valor, ";
                }

                // Eliminar la última coma y espacio extra
                $espectaculo = rtrim($espectaculo, ", ");
                $consultaExiste->close();
            }
        } catch (Exception $ex) {
            echo "ERROR: " . $ex->getMessage();
        }
        return $espectaculo;
    }

    /**
     * Método que muestra todos los espectáculos de la base de datos existentes
     * @return array|bool devuelve un array con todos los espectáculos encontrados o false si no hay resultados
     */
    public static function buscarEspectaculos() {
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        $esValido = false;
        if (!$conexionBD || $conexionBD->connect_error) {
            echo "Error en la conexión: " . $conexionBD->connect_error;
        }
        try {
            // Verificar si el espectáculo existe
            $consultaExistenEspectaculos = $conexionBD->query("SELECT * FROM espectaculo");
            if ($consultaExistenEspectaculos) {
                $espectaculosRecibidos = $consultaExistenEspectaculos->fetch_all(MYSQLI_ASSOC);
                if (count($espectaculosRecibidos) > 0) {
                    $esValido = true;
                    // Obtener todos los espectáculos
                    $datosEspectaculoRecibidos = $espectaculosRecibidos;
                    $consultaExistenEspectaculos->close();
                }
            }
        } catch (Exception $ex) {
            echo "ERROR: " . $ex->getMessage();
        }
        return $esValido ? $datosEspectaculoRecibidos : false;
    }

    /**
     * Método que muestra todos los espectáculos de la base de datos existentes
     * @return string devuelve una cadena con todos los espectáculos encontrados en caso de exito, sino devuelve error.
     */
    public static function mostrarTodosEspectaculos() {
        // Conectar a la base de datos
        $conexionBD = Conexion::conectarEspectaculosMySQLi();
        try {
            // Consulta para obtener todos los espectáculos
            $consultaMostrarEspectaculos = "SELECT * FROM espectaculo";
            $resultadoConsultaMostrarEspectaculos = $conexionBD->query($consultaMostrarEspectaculos);
            // Verificar que la consulta se haya ejecutado y que existan registros
            if ($resultadoConsultaMostrarEspectaculos->num_rows > 0) {
                $datosConsultaMostrarEspectaculos = $resultadoConsultaMostrarEspectaculos->fetch_all();
                foreach ($datosConsultaMostrarEspectaculos as $campo => $valor) {
                    $espectaculos .= $campo . ": " . $valor;
                }
            }
        } catch (Exception $ex) {
            echo "ERROR: " . $ex->getMessage();
        }
        return $espectaculos;
    }
}
