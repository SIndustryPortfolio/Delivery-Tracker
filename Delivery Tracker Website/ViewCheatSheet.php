<?php
session_start();

// Dirs
$ModulesFolder = "Modules/";
$ViewsFolder = "Views/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// Modules
require_once($PHPModulesFolder . "Shortcuts.php");

// CORE
$Shortcuts = new Shortcuts();

// Functions
// INIT
$Shortcuts->NewPageSetup();

if (!isset($_SESSION["LoggedIn"]))
{
    return header("Location: Login.php");
}

$View = $_SESSION["View"];

require_once($ViewsFolder . "ViewCheatSheet.phtml");


