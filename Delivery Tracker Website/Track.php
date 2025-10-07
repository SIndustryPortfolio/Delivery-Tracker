<?php
session_start();

// Dirs
$ModulesFolder = "Modules/";
$ViewsFolder = "Views/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// Modules
require_once($PHPModulesFolder . "Shortcuts.php");
require_once($PHPModulesFolder . "DeliveryDataSet.php");

// CORE
$Shortcuts = new Shortcuts();
$DeliveryDataSet = new DeliveryDataSet();

// Functions
// INIT
$Shortcuts->NewPageSetup();

if (!isset($_SESSION["LoggedIn"]))
{
    return header("Location: Login.php");
}

$View = $_SESSION["View"];
$View->Page->Id = $_GET["Id"];
$View->Page->Delivery = $DeliveryDataSet->GetDeliveryFromId($_GET["Id"]);

require_once($ViewsFolder . "Track.phtml");


