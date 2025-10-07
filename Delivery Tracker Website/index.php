<?php
session_start();

$_SESSION["NewSession"] = true;

return header("Location: Login.php");