<?php
session_start();

// Dirs
$ModulesFolder = "Modules/";
$ResourcesFolder = "Resources/";
$JSONFolder = $ResourcesFolder . "JSON/";
$ViewsFolder = "Views/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// JSON
$UserJSONFile = file_get_contents($JSONFolder . "User.json");

// Modules
require_once($PHPModulesFolder . "Shortcuts.php");
require_once($PHPModulesFolder . "UserDataSet.php");
require_once($PHPModulesFolder . "Validate.php");

// CORE
$UserInfo = json_decode($UserJSONFile, true);

$Validate = new Validate();
$UserDataSet = new UserDataSet();
$Shortcuts = new Shortcuts();
$LoadAlert = null;

// Functions
// MECHANICS
function EditProfile($FieldName)
{
    // CORE
    global $Validate;
    global $Shortcuts;
    global $UserDataSet;
    global $LoadAlert;
    global $UserInfo;

    $User = $UserDataSet->GetUserFromUsername($_SESSION["Account"]["Username"]);
    $FieldInfo = $UserInfo["Fields"][$FieldName];


    $KeysToCheck = array("EditOld" . $FieldName, "EditNew" . $FieldName, "EditConfirmNew" . $FieldName);

    // Functions
    // INIT
    foreach($KeysToCheck as &$Key)
    {
        if (!isset($_POST[$Key]) or $Shortcuts->IsEmptyString($_POST[$Key]))
        {
            $LoadAlert = $Shortcuts->CreateAlert("danger", "All fields must have values");
            return null;
        }
    }

    if (isset($FieldInfo["Validate"]) and $FieldInfo["Validate"])
    {
        if (!$Validate->ValidateInput($FieldName, $_POST["EditNew" . $FieldName]))
        {
            $LoadAlert = $Shortcuts->CreateAlert("danger", "Enter a valid " . $FieldInfo["DisplayName"]);
            return null;
        }
    }

    $DoesMatch = false;

    if (!$Shortcuts->DoStringsMatch( $User->{"Get" . $FieldName}(),  $_POST["EditOld" . $FieldName], $FieldInfo["CaseSensitive"]))
    {
        $LoadAlert = $Shortcuts->CreateAlert("danger", "Invalid credentials");
        return null;
    }

    if (!$Shortcuts->DoStringsMatch($_POST["EditConfirmNew" . $FieldName], $_POST["EditNew" . $FieldName], $FieldInfo["CaseSensitive"]))
    {
        $LoadAlert = $Shortcuts->CreateAlert("danger", "New and Confirm values don't match");
        return null;
    }

    $UserDataSet->UpdateUserFromUserId($User->GetId(), array($FieldName => $_POST["EditNew" . $FieldName]));

    $LoadAlert = $Shortcuts->CreateAlert("success", "Successfully editted profile");
}

// INIT
$Shortcuts->NewPageSetup();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST["FieldName"]))
    {
        EditProfile($_POST["FieldName"]);
    }
}


if (!isset($_SESSION["LoggedIn"]))
{
    return header("Location: Login.php");
}

$View = $_SESSION["View"];

if ($LoadAlert !== null)
{
    $View->LoadAlert = $LoadAlert;
}

require_once($ViewsFolder . "Profile.phtml");


