<?php
session_start();

// Dirs
$ModulesFolder = "Modules/";
$UploadsFolder = "Uploads/";
$ResourcesFolder = "Resources/";
$ViewsFolder = "Views/";
$JSONFolder = $ResourcesFolder . "JSON/";
$DeliveryUploadsFolder = $UploadsFolder . "Delivery/";
$PHPModulesFolder = $ModulesFolder . "PHP/";

// JSON
$CoreJSONFile = file_get_contents($JSONFolder . "Core.json");
$StatusJSONFile = file_get_contents($JSONFolder . "Status.json");

// Modules
require_once($PHPModulesFolder . "DeliveryDataSet.php");
require_once($PHPModulesFolder . "UserTypeDataSet.php");
require_once($PHPModulesFolder . "Shortcuts.php");
require_once($PHPModulesFolder . "Debug.php");

// CORE
$CoreInfo = json_decode($CoreJSONFile, false);
$StatusInfo = json_decode($StatusJSONFile, true);

$Debug = new Debug();
$DeliveryDataSet = new DeliveryDataSet();
$UserTypeDataSet = new UserTypeDataSet();
$Shortcuts = new Shortcuts();

// Functions
// MECHANICS


// INIT
$Shortcuts->NewPageSetup();

if (!isset($_SESSION["LoggedIn"]))
{
    return header("Location: Login.php");
}

$View = $_SESSION["View"];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $View->ColumnNames = $DeliveryDataSet->GetDeliveryColumns();

    if (isset($_POST["Edit"]))
    {
        if ($UserTypeDataSet->IsValidAccessLevel($_SESSION["Account"]["UserType"], "Manager"))
        {
            $View->ColumnNames = $DeliveryDataSet->GetDeliveryColumns();
            $UpdateDict = array();

            foreach($View->ColumnNames as $Index => $ColumnName)
            {
                $UpdateDict[$ColumnName] = $_POST["Update" . $Shortcuts->RemoveWhiteSpaces($ColumnName)];
            }

            //for ($i = 0; $i < sizeof($View->ColumnNames); $i++)
            //{
            //    $ColumnInfo = $View->ColumnNames;

            //    $UpdateDict[$ColumnInfo["Field"]] = $_POST["Update" . $ColumnInfo["Field"]];
            //}

            $DeliveryDataSet->UpdateDeliveryFromId($_POST["Edit"], $UpdateDict);
            $View->LoadAlert = $Shortcuts->CreateAlert("success", "Successfully editted entry");
        }
        else
        {
            $View->LoadAlert = $Shortcuts->CreateAlert("danger", $View->CoreInfo->LackPrivilegesMessage);
        }
    }

    if (isset($_POST["Delete"]))
    {
        if ($UserTypeDataSet->IsValidAccessLevel($_SESSION["Account"]["UserType"], "Manager"))
        {
            $DeliveryDataSet->RemoveDeliveryFromId($_POST["Delete"]);
            $View->LoadAlert = $Shortcuts->CreateAlert("success", "Successfully deleted entry");
        }
        else
        {
            $View->LoadAlert = $Shortcuts->CreateAlert("danger", $View->CoreInfo->LackPrivilegesMessage);
        }
    }

    if (isset($_POST["PageNumber"]))
    {
        $View->Page->PageNumber = $_POST["PageNumber"];
    }
    elseif (!isset($View->PageNumber))
    {
        $View->Page->PageNumber = 1;
    }
}
else
{
    $View->Page->PageNumber = 1;
}


if (isset($_FILES["ImageUpload"]) and $_FILES["ImageUpload"]["tmp_name"] != "")
{
    $DeliveryId = $_POST["DeliveryId"];
    $Extension = "." . pathinfo($_FILES["ImageUpload"]["name"], PATHINFO_EXTENSION);
    $FileName = $DeliveryId . $Extension;
    $TargetPath = $DeliveryUploadsFolder . $FileName;

    //var_dump($_FILES["ImageUpload"]);
    //var_dump(pathinfo($_FILES["ImageUpload"]["tmp_name"], PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["ImageUpload"]["tmp_name"], $TargetPath))
    {
        $DeliveryDataSet->UpdateDeliveryFromId($DeliveryId, array("DelPhoto" => $FileName));
    }
    else
    {
        $View->LoadAlert = $Shortcuts->CreateAlert("danger", "Failed to upload image");
    }
}


if ($UserTypeDataSet->IsValidAccessLevel($_SESSION["Account"]["UserType"], "Manager"))
{
    $View->Page->Deliveries = $DeliveryDataSet->GetAllDeliveries();
}
else
{
    $View->Page->Deliveries = $DeliveryDataSet->GetDeliveriesFromUser($_SESSION["Account"]["Id"]);
}

$View->Page->Filters = array();

$NewDeliveries = array();

foreach($StatusInfo as $StatusNumber => $Info)
{
    $Filter = true;

    //var_dump($_POST);

    //$Debug->Log("ViewOrders.php | Info Name: " . $Info["Name"]);

    if (isset($_POST["Filter" . $StatusNumber]))
    {
        //$Debug->Log("ViewOrders.php | Filter " . $StatusNumber . ": " . $_POST["Filter" . $StatusNumber]);
        $Filter = $Shortcuts->StringToBool($_POST["Filter" . $StatusNumber]);
    }
    else
    {
        $Filter = true;
    }

    $View->Page->Filters[$StatusNumber] = $Filter;

    if ($Filter)
    {
        foreach($View->Page->Deliveries as $Index => $Delivery)
        {
            if ($Delivery->GetStatus() === $StatusNumber)
            {
                //$Debug->Log("ViewOrders.php | DeliveryGetStatus: " . $Delivery->GetStatus() . " | DeliveryStatus: " . $DeliveryStatus);
                $NewDeliveries[] = $Delivery;
            }
        }
    }
}

//$View->Page->OldDeliveries = $NewDeliveries;
unset($View->Page->Deliveries);
$View->Page->Deliveries = $NewDeliveries;

if (isset($_POST["Search"]))
{
    $SearchDeliveries = array();
    $Ids = array();

    foreach($NewDeliveries as $Index => $Delivery)
    {
        for ($i = 0; $i < sizeof($CoreInfo->Searchable); $i++)
        {
            $ColumnName = $CoreInfo->Searchable[$i];
            $ColumnValue = $Delivery->{"Get" . $Shortcuts->RemoveWhiteSpaces($ColumnName)}();

            if ($Shortcuts->IsEmptyString($ColumnValue))
            {
                continue;
            }

            $lowerSearchQuery = strtolower($_POST["Search"]);
            $lowerColumnValue = strtolower($ColumnValue);

            if (str_contains($lowerColumnValue, $lowerSearchQuery) === true)
            {
                if (!in_array($Delivery->GetId(), $Ids))
                {
                    //$Debug->Log("ViewOrders.php | Search | Adding: " . $Delivery->GetId() . " | Name: " . $Delivery->GetName());

                    $SearchDeliveries[] = $Delivery;
                    $Ids[] = $Delivery->GetId();
                }

                break;
            }
        }
    }

    //$Debug->Log($SearchDeliveries);

    unset($NewDeliveries);
    $View->Page->Deliveries = $SearchDeliveries;
    $View->Page->SearchQuery = $_POST["Search"];
}
else
{
    //$View->Page->SearchQuery = null;
}

$TotalDeliveries = sizeof($View->Page->Deliveries);
$PossiblePages = ceil($TotalDeliveries / $View->CoreInfo->ItemsPerPage);

$View->Page->PageNumber = $Shortcuts->Clamp($View->Page->PageNumber, 1, $PossiblePages); // Recalc


$TotalPages = ceil(sizeof($View->Page->Deliveries) / $CoreInfo->ItemsPerPage);
$StartIndex = 0;
$EndIndex = 0;
$ActualPageNumber = $View->Page->PageNumber - 1;

// FUNCTIONS
// INIT
if ($ActualPageNumber > 0)
{
    $StartIndex = ($ActualPageNumber) * $View->CoreInfo->ItemsPerPage;
}

//$TotalPages = ceil(sizeof($View->Deliveries) / $View->CoreInfo->ItemsPerPage);

//$Debug->Log("ViewOrders.phtml | PageNumber: " . $View->Page->PageNumber . " | TotalPages: " . $TotalPages);

if ($View->Page->PageNumber == $TotalPages)
{
    //$Debug->Log("ViewOrders.phtml | Last Page");
    $EndIndex = $StartIndex + (sizeof($View->Page->Deliveries) - ($ActualPageNumber * $View->CoreInfo->ItemsPerPage));
}
else
{
    //$Debug->Log("ViewOrders.phtml | Other Page");
    $EndIndex = ($StartIndex) + $View->CoreInfo->ItemsPerPage;
}

$View->Page->EndIndex = $EndIndex;
$View->Page->StartIndex = $StartIndex;
$View->Page->TotalPages = $TotalPages;
$View->Page->ResultsFound = sizeof($View->Page->Deliveries);

$FinalDeliveryDict = array();

for ($i = $StartIndex; $i < $EndIndex; $i++)
{
    if (isset($View->Page->Deliveries[$i]))
    {
        $FinalDeliveryDict[] = $View->Page->Deliveries[$i];
    }
}

$View->Page->Deliveries = $FinalDeliveryDict;

// Render
if (isset($_POST["Ajax"]))
{
    foreach($View->Page->Deliveries as &$Delivery)
    {
        echo "<div id='SearchGroup' class='card card-body'>";
            echo "<h5 class='text-center'>Order Id: " . $Delivery->GetId() . " | Name: " . $Delivery->GetName() . "</h5>";

            $Shortcuts->CreateQrCode($Delivery->GetId() . "LiveSearch", $Shortcuts->GetRoot() . "Track.php?Id=" . $Delivery->GetId(), "Small");

            //echo "<br>";
            echo "<p class='text-center' style='margin-top: 5px;'><a href='" . $Shortcuts->GetRoot() . "/Track.php?Id=" . $Delivery->GetId() . "' class='link link-dark'>Track</a></p>";
        echo "</div>";
    }
}
else
{
    $_SESSION["View"] = $View;
    require_once($ViewsFolder . "ViewOrders.phtml");
}



