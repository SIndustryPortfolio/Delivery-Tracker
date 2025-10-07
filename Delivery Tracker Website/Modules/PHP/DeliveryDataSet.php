<?php

// Modules
require_once("Database.php");
require_once("Debug.php");
require_once("Shortcuts.php");
require_once("Delivery.php");

// CORE


class DeliveryDataSet
{
    private $DatabaseConnection;
    private $Debug;
    private $CoreInfo;

    public function __construct()
    {
        // Dirs
        $RootFolder = "./";
        $ResourcesFolder = $RootFolder . "Resources/";
        $JSONFolder = $ResourcesFolder . "JSON/";

        // JSON
        $CoreJSONFile = file_get_contents($JSONFolder . "Core.json");

        // Functions
        // INIT
        $db = new Database();
        $this->DatabaseConnection = $db->GetConnection();

        $this->Debug = new Debug();
        $this->CoreInfo = json_decode($CoreJSONFile, false);
    }

    // Functions
    // MECHANICS
    public function CreateDeliveryFromRow($Row)
    {
        // Functions
        // INIT
        return new Delivery($Row["Id"], $Row["Name"], $Row["Address1"], $Row["Address2"], $Row["Postcode"], $Row["Deliverer"], $Row["Latitude"], $Row["Longitude"], $Row["Status"], $Row["DelPhoto"]);
    }

    // CRUD
    public function RemoveDeliveryFromId($DeliveryId)
    {
        // Functions
        // INIT
        //global $Debug;

        try
        {
            $stmt = $this->DatabaseConnection->prepare("DELETE FROM delivery WHERE Id=:DeliveryId ;");
            $stmt->bindValue(":DeliveryId", $DeliveryId);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }
    }

    public function UpdateDeliveryFromId($DeliveryId, $UpdateDict)
    {
        // Functions
        // INIT
        //global $Debug;

        $DeliveryToWorkOn = $this->GetDeliveryFromId($DeliveryId);

        // Remove duplicates
        foreach($DeliveryToWorkOn->GetDictionary() as $Field => $Value)
        {
            if (!isset($UpdateDict[$Field]))
            {
                continue;
            }

            if ($UpdateDict[$Field] === $Value)
            {
                unset($UpdateDict[$Field]);
            }
        }

        try
        {
            $TotalKeys = count($UpdateDict);
            $Index = 0;

            $String = "UPDATE delivery SET ";

            foreach($UpdateDict as $Field => $Value)
            {
                $String = $String . $Field . " = :" . $Field;

                if ($Index < $TotalKeys - 1)
                {
                    $String = $String . ", ";
                }

                $Index += 1;
            }

            $String = $String . " WHERE Id=:DeliveryId ;";

            $this->Debug->Log($String);
            //$Debug->Log($String);

            $stmt = $this->DatabaseConnection->prepare($String);

            foreach($UpdateDict as $Field => $Value)
            {
                //$Debug->Log($Field . " => " . $Value);
                $stmt->bindValue(":$Field", $Value);
            }

            $stmt->bindValue(":DeliveryId", $DeliveryId);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }
    }

    public function GetDeliveryColumns()
    {
        // Functions
        // INIT
        //global $Debug;
        $Result = null;
        $Columns = array();

        try
        {
            //$stmt = $this->DatabaseConnection->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'delivery' ;");
            $stmt = $this->DatabaseConnection->prepare("SHOW COLUMNS FROM delivery ;");
            //$stmt = $this->DatabaseConnection->prepare("select COLUMN_NAME from information_schema.columns where table_schema = 'betterdeliverydb' order by table_name,ordinal_position");
            $stmt->execute();
            //$Result = $stmt->fetch(PDO::FETCH_ASSOC);

            while ($Row = $stmt->fetch())
            {
                array_push($Columns, $Row["Field"]);
            }
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->GetMessage());
        }

        return $Columns;
    }

    public function AddDelivery($DeliveryDict)
    {
        // CORE
        //global $Debug;

        // Functions
        // INIT
        try
        {
            $String = "INSERT INTO delivery (";
            $Index = 0;

            foreach($DeliveryDict as $ColumnName => $ColumnValue)
            {
                $String = $String . $ColumnName;

                if ($Index < count($DeliveryDict) - 1)
                {
                    $String = $String . ", ";
                }

                $Index += 1;
            }

            //var_dump($DeliveryDict);

            unset($Index);
            $Index = 0;
            $String = $String . ") VALUES (";
            //$this->Debug->Log("DeliveryDataSet.php | AddDelivery | String1: " . $String);

            foreach($DeliveryDict as $ColumnName => $ColumnValue)
            {
                $String = $String . ":" . $ColumnName;

                if ($Index < count($DeliveryDict) - 1)
                {
                    $String = $String . ", ";
                }

                $Index += 1;
            }

            $String = $String . ") ;";

            $this->Debug->Log("DeliveryDataSet.php | AddDelivery | String2: " . $String);

            $stmt = $this->DatabaseConnection->prepare($String);

            foreach($DeliveryDict as $ColumnName => $ColumnValue)
            {
                $stmt->bindValue(":" . $ColumnName, $ColumnValue);
            }

            $stmt->execute();
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e);
        }
    }

    public function GetAllDeliveries()
    {
        // CORE
        //global $Debug;
        $Deliveries = array();

        // Functions
        // INIT
        try
        {
            $stmt = $this->DatabaseConnection->prepare("SELECT * FROM delivery ORDER BY Id ;");
            $stmt->execute();

            while ($Row = $stmt->fetch())
            {
                array_push($Deliveries, $this->CreateDeliveryFromRow($Row));
            }
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }

        return $Deliveries;
    }

    public function GetDeliveryFromId($DeliveryId)
    {
        // CORE
        //global $Debug;
        $Result = null;

        // Functions
        // INIT
        try
        {
            $stmt = $this->DatabaseConnection->prepare("SELECT * FROM delivery WHERE Id=:DeliveryId ;");
            $stmt->bindValue(":DeliveryId", $DeliveryId);
            $stmt->execute();

            $Result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }

        return $this->CreateDeliveryFromRow($Result);
    }
    public function GetDeliveriesFromUser($UserId)
    {
        // Functions
        // INIT
        $Deliveries = array();
        //global $Debug;
        //global $CoreInfo;
        $stmt = null;
        $String = "";

        try
        {
            /*if (isset($SearchQuery) and $SearchQuery != "")
            {
                $String = "SELECT * FROM delivery WHERE deliverer=:UserId AND (";

                //$stmt = $this->DatabaseConnection->prepare("SELECT * FROM delivery WHERE deliverer=:UserId ;");

                for ($i = 0; $i < sizeof($CoreInfo->Searchable); $i++)
                {
                    $ColumnName = $CoreInfo->Searchable[$i];

                    $String = $String . "[$ColumnName] LIKE %$SearchQuery%";

                    if ($i != sizeof($CoreInfo->Searchable) - 1)
                    {
                        $String = $String . " OR ";
                    }
                    else
                    {
                        $String = $String . ") ;";
                    }
                }

                $Debug->Log("DeliveryDataSet.php | Search stmt String: " . $String);

            }
            else
            {
                $String = "SELECT * FROM delivery WHERE deliverer=:UserId ;"
            }*/

            $String = "SELECT * FROM delivery WHERE deliverer=:UserId ORDER BY Id;";

            try
            {
                $stmt = $this->DatabaseConnection->prepare($String);
                $stmt->bindValue(":UserId", $UserId);
                $stmt->execute();
            }
            catch(PDOException $e)
            {
                $this->Debug->Log($e->getMessage());
            }

            //$Result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            while ($Row = $stmt->fetch())
            {
                array_push($Deliveries, $this->CreateDeliveryFromRow($Row));
            }
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }

        return $Deliveries;
    }
}
