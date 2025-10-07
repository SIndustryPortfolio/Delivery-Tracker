<?php

// Modules
require_once("Database.php");
require_once("Shortcuts.php");

// CORE


class SessionDataSet
{
    private $DatabaseConnection;
    private $Debug;
    public function __construct()
    {
        $this->Debug = new Debug();
        $db = new Database();
        $this->DatabaseConnection = $db->GetConnection();
    }

    // Functions
    public function GenerateSessionTokenFromUserId($UserId)
    {
        $Shortcuts = new Shortcuts();

        $RandomLength = 255 - strlen($UserId);
        $Token = $UserId . $Shortcuts->GenerateRandomString($RandomLength);

        return $Token;
    }

    public function SetUserSessionToken($UserId, $Token)
    {
        // CORE
        //global $Debug;

        // Functions
        // INIT
        $OldSessionToken = $this->GetSessionTokenFromUserId($UserId);

        try
        {
            $stmt = null;

            if ($OldSessionToken !== null)
            {
                $stmt = $this->DatabaseConnection->prepare("UPDATE session SET Token = :Token WHERE UserId = :UserId");
            }
            else
            {
                $stmt = $this->DatabaseConnection->prepare("INSERT INTO session VALUES(:UserId, :Token)");
            }

            $stmt->bindValue(":Token", $Token);
            $stmt->bindValue(":UserId", $UserId);
            $stmt->execute();

        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }
    }

    public function GetSessionTokenFromUserId($UserId)
    {
        $Result = null;
        //global $Debug;

        try
        {
            $stmt = $this->DatabaseConnection->prepare("SELECT Token FROM session WHERE UserId=:UserId");
            $stmt->bindValue(":UserId", $UserId);
            $stmt->execute();

            $Result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }

        if (isset($Result) and isset($Result["Token"]))
        {
            return $Result["Token"];
        }
    }

    // ACCESSORS
    public function GetDatabaseConnection()
    {
        return $this->DatabaseConnection;
    }
}