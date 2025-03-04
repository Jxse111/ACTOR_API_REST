<?php

session_start();
if (isset($_SESSION['usuario']) && filter_has_var(INPUT_POST, "cerrarSesion")) {
    header("location: ../Vista/login.html");
    session_destroy();
}

