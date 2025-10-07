<?php

if (session_status() == PHP_SESSION_NONE)
{
    session_start();
}

parse_ini_file("php.ini", true);

// Dirs
$ResourcesFolder = "Resources/";
$ModulesFolder = "Modules/";
$ViewsFolder = "Views/";
$ResourcesJSONFolder = $ResourcesFolder . "JSON/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// JSON
$CoreJSONFile = file_get_contents($ResourcesJSONFolder . "Core.json");

// MODULES
require_once($PHPModulesFolder . "UserDataSet.php");
require_once($PHPModulesFolder . "Debug.php");
require_once($PHPModulesFolder . "Shortcuts.php");

// CORE
//global $View;
$Debug = new Debug();
$Shortcuts = new Shortcuts();
$CoreInfo = json_decode($CoreJSONFile, false);
$LoadAlert = null;
$_FILES["file"]["tmp_name"] = $Shortcuts->GetRoot() . "/Uploads/Temp";
$LoggedOut = false;

// Functions
// MECHANICS
$CookieLogin = function() 
{
    // CORE
    global $Debug;
    global $Shortcuts;
    global $LoadAlert;
    $UserDataSet = new UserDataSet();

    $Result = $UserDataSet->UserLogin($_COOKIE["Username"], null, $_COOKIE["Token"]);
};

$Login = function()
{
    // CORE
    global $Debug;
    global $Shortcuts;
    global $LoadAlert;
    $UserDataSet = new UserDataSet();

    $ToCheck = array("Password", "Token");

    // Functions
    // INIT
    if (!isset($_POST["Password"]) and !isset($_POST["Token"]))
    {
        $Debug->Log("Login.php | Login | Error: No Password or Token provided!");
    }
    else
    {
        foreach($ToCheck as $Key)
        {
            if (!isset($_POST[$Key]))
            {
                $_POST[$Key] = false;
            }
        }

        if (isset($_POST["Username"]) and !$Shortcuts->IsEmptyString($_POST["Username"]))
        {
            $Result = $UserDataSet->UserLogin($_POST["Username"], $_POST["Password"], $_POST["Token"]);

            $Debug->Log($Result);

            if (!isset($Result) or !$Result) {
                $LoadAlert = $Shortcuts->CreateAlert("danger", "Username or Password Invalid!");
            }
        }
    }
};

$Logout = function()
{
    // CORE
    global $LoggedOut;
    $UserDataSet = new UserDataSet();

    // Functions
    // INIT
    $UserDataSet->UserLogout();
    $LoggedOut = true;
};

$ClientRequests = array(
    "Login" => $Login,
    "CookieLogin" => $CookieLogin,
    "Logout" => $Logout
);

// INIT
if ($_SERVER["REQUEST_METHOD"] == "POST") // Login Request
{
    //foreach($_POST as $Key => $Value)
    //{
    //    $Debug->Log("Login.php | " . $Key . " = " . $Value);
    //}

    //var_dump($_POST);

    $ClientRequests[$_POST["ClientRequest"]]();
}

if ($_COOKIE and isset($_COOKIE["Username"]))
{
    $ClientRequests["CookieLogin"]();
}


$View = new stdClass();
$View->CoreInfo = $CoreInfo;
$View->Page = new stdClass();
$View->Page->LoggedOut = $LoggedOut;

if (isset($_SESSION["NewSession"]))
{
    $View->NewSession = $_SESSION["NewSession"];
}

if (isset($LoadAlert))
{
    $View->LoadAlert = $LoadAlert;
}

$_SESSION["View"] = $View;

if (!isset($_SESSION["LoggedIn"]))
{
    require($ViewsFolder . "Login.phtml");
}
else
{
    header("Location: Home.php");
    //exit();
}

$_SESSION["NewSession"] = false;