<?php
session_start();

// Dirs
$ModulesFolder = "Modules/";
$ResourcesFolder = "Resources/";
$ViewsFolder = "Views/";
$JSONFolder = $ResourcesFolder . "JSON/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// JSON
$CoreJSONFile = file_get_contents($JSONFolder . "Core.json");

// Modules
require_once($PHPModulesFolder . "Shortcuts.php");
require_once($PHPModulesFolder . "DeliveryDataSet.php");
require_once($PHPModulesFolder . "Debug.php");
require_once($PHPModulesFolder . "UserTypeDataSet.php");

// CORE
$CoreInfo = json_decode($CoreJSONFile, false);

$UserTypeDataSet = new UserTypeDataSet();
$Debug = new Debug();
$Shortcuts = new Shortcuts();
$DeliveryDataSet = new DeliveryDataSet();

// Functions
// MECHANICS
function HandleBulkImport()
{
    // CORE
    global $DeliveryDataSet;
    global $View;
    global $Shortcuts;
    global $Debug;

    // Functions
    // INIT
    $ColumnNames = $DeliveryDataSet->GetDeliveryColumns();
    //var_dump($_FILES["BulkImport"]);

    $Text = file_get_contents($_FILES["BulkImport"]["tmp_name"]); //$_FILES["file"]["tmp_name"]);

    $Lines = preg_split('/\r\n|\r|\n/', $Text);

    //var_dump($Lines);

    //$Debug->Log("Create.php | HandleBulkImport | Lines: " . json_encode($Lines));

    foreach($Lines as &$Line)
    {
        $Segments = explode(",", $Line);
        $PackagedData = [];

        $Debug->Log("Create.php | HandleBulkImport | Segments: " . json_encode($Segments));

        for ($i = 0; $i < sizeof($ColumnNames); $i++)
        {
            $PackagedData[$ColumnNames[$i]] = $Segments[$i];
        }

        $DeliveryDataSet->AddDelivery($PackagedData);
    }

    $View->LoadAlert = $Shortcuts->CreateAlert("success", "Successfully created deliveries");
}

function HandleCreate()
{
    // CORE
    global $DeliveryDataSet;
    global $View;
    global $Shortcuts;
    global $Debug;

    // Functions
    // INIT
    //var_dump($_POST["IndexesStored"]);

    $ColumnNames = $DeliveryDataSet->GetDeliveryColumns();
    $IndexesTable = json_decode($_POST["IndexesStored"], false);

    //var_dump($IndexesTable);

    for ($i = 0; $i < sizeof($IndexesTable); $i++)
    {
        $PackagedData = [];
        $Id = $IndexesTable[$i];

        foreach($ColumnNames as &$ColumnName)
        {
            $InputName = "Create" . $ColumnName . $Id;

            if (isset($_POST[$InputName]))
            {
                $PackagedData[$ColumnName] = $_POST[$InputName];
            }
            else
            {
                $PackagedData[$ColumnName] = null;
            }
        }

        if (count($PackagedData) > 0)
        {
            $Debug->Log("Create.php | Adding Delivery");
            $DeliveryDataSet->AddDelivery($PackagedData);
        }
    }

    $View->LoadAlert = $Shortcuts->CreateAlert("success", "Successfully created deliveries");
}

// INIT
$Shortcuts->NewPageSetup();

if (!isset($_SESSION["LoggedIn"]))
{
    return header("Location: Login.php");
}

$View = $_SESSION["View"];
$View->Page->Columns = $DeliveryDataSet->GetDeliveryColumns();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_POST["IndexesStored"]) and sizeof(json_decode($_POST["IndexesStored"])) > 0)
    {
        if ($UserTypeDataSet->IsValidAccessLevel($_SESSION["Account"]["UserType"], "Manager"))
        {
            HandleCreate();
        }
        else
        {
            $View->LoadAlert = $Shortcuts->CreateAlert("danger", $CoreInfo->LackPrivilegesMessage);
        }
    }

    if (isset($_FILES["BulkImport"]) and $_FILES["BulkImport"]["tmp_name"] != "")
    {
        if ($UserTypeDataSet->IsValidAccessLevel($_SESSION["Account"]["UserType"], "Manager"))
        {
            HandleBulkImport();
        }
        else
        {
            $View->LoadAlert = $Shortcuts->CreateAlert("danger", $CoreInfo->LackPrivilegesMessage);
        }
    }
}

require_once($ViewsFolder . "Create.phtml");


