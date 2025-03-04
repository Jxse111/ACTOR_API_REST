<?php

require_once './Modelo/Actor.php';
session_start();
if (isset($_SESSION['usuario']) && $_SESSION['rol'] == "administrador") {
    echo nl2br("Bienvenido " . $_SESSION['usuario'] . ", " . $_SESSION['rol'] . ".\n");
    if (filter_has_var(INPUT_POST, "eliminar")) {
        $codigoActor = filter_input(INPUT_POST, "seleccionActor");
        if (Actor::eliminarActor($codigoActor)) {
            header("Location: ../Vista/muestraMensajes.php");
        } else {
            header("Location: ../Vista/muestraMensajesError.php");
        }
    }
    if (filter_has_var(INPUT_POST, "buscar")) {
        $codigoActor = filter_input(INPUT_POST, "seleccionActor");
        if (Actor::verActor($codigoActor)) {
            header("Location: ../Vista/muestraMensajes.php");
        } else {
            header("Location: ../Vista/muestraMensajesError.php");
        }
    }
    if (filter_has_var(INPUT_POST, "asignar")) {
        $codigoActor = filter_input(INPUT_POST, "seleccionActor");
        $codigoSupervisor = filter_input(INPUT_POST, "seleccionSupervisor");
        if ($codigoActor == $codigoSupervisor) {
            header("Location: ../Vista/muestraMensajesError.php");
        } else {
            $actorBD = Actor::verActor($codigoActor);
            $actorSeleccionadoBD = new Actor($actorBD['cdactor'], $actorBD['nombre'], $actorBD['sexo'], $actorBD['cdgrupo'], $actorBD['fecha_alta'], $actorBD['cache_base'] . $actorBD['cdSupervisa']);
            $actorSeleccionadoBD->setSupervisor($codigoSupervisor);
            if ($actorSeleccionadoBD->guardarActor()) {
                header("Location: ../Vista/muestraMensajes.php");
            }
        }
    }
}