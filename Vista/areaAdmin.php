<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != "administrador") {
    session_destroy();
    header("location:login.html");
} else {
    echo nl2br("Bienvenido " . $_SESSION['usuario'] . ", " . $_SESSION['rol'] . ".\n");
    if (filter_has_var(INPUT_POST, "eliminar") || filter_has_var(INPUT_POST, "buscar") || filter_has_var(INPUT_POST, "asignar")) {
        header("Location: ../Controlador/controladorActor.php");
    }
    ?>
    <!DOCTYPE html>
    <!--
    Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
    Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
    -->
    <html>
        <head>
            <meta charset="UTF-8">
            <title>AreaAdmin</title>
            <link rel="stylesheet" href="../Estilos/style.css"/>
        </head>
        <body>
            <?php
            require_once '../Controlador/funcionesValidacion.php';
            require_once '../Modelo/Conexion.php';
            $conexionBD = Conexion::conectarEspectaculosMySQLi();
            try {
                $consultaActor = $conexionBD->query("SELECT cdactor,nombre FROM actor");
                while ($actores = $consultaActor->fetch_assoc()) {
                    $tablaActor[] = $actores;
                }
                Conexion::desconectar();
            } catch (Exception $ex) {
                $mensajeError .= "ERROR: " . $ex->getMessage();
            }
            ?>
            <form action="../Controlador/controladorActor.php" method="post">
                <label>Selecciona el nombre del Actor: </label>
                <select name="seleccionActor">
                    <option value="0">Selecciona un Actor</option>
                    <?php
                    if ($tablaActor) {
                        foreach ($tablaActor as $actoresExistentes) {
                            ?>
                            <option value = "<?php echo $actoresExistentes['cdactor'] ?>"><?php echo $actoresExistentes['nombre'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select><br><br>
                <label>Selecciona el Supervisor: </label>
                <select name="seleccionSupervisor">
                    <option value="0">Selecciona un Supervisor</option>
                    <?php
                    if ($tablaActor) {
                        foreach ($tablaActor as $actoresExistentes) {
                            ?>
                            <option value="<?php echo $actoresExistentes['cdactor'] ?>"'><?php echo $actoresExistentes['nombre'] ?></option>;
                            <?php
                        }
                    }
                    ?>
                </select><br><br>
                <button type="submit" name="eliminar">Eliminar</button>
                <button type="submit" name="buscar">Buscar</button>
                <button type="submit" name="asignar">Asignar</button>
            </form><br>
            <?php
            include_once 'cerrarSesion.html';
        }
        ?>
    </body>
</html>
