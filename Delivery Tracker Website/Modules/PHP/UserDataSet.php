<?php

// Modules
require_once("Debug.php");
require_once("Database.php");
require_once("User.php");
require_once("Shortcuts.php");
require_once("SessionDataSet.php");

class UserDataSet
{
    private $DatabaseConnection;
    private $Debug;
    private $Shortcuts;

    // Functions
    function __construct()
    {
        // Functions
        // INIT
        $db = new Database();
        $this->DatabaseConnection = $db->GetConnection();
        $this->Debug = new Debug();
        $this->Shortcuts = new Shortcuts();
    }

    public function UpdateUserFromUserId($UserId, $UpdateTable)
    {
        // Functions
        // INIT
        try
        {
            $String = "UPDATE user SET ";

            $Index = 0;
            $Total = count($UpdateTable);
            foreach($UpdateTable as $FieldName => $FieldValue)
            {
                $String = $String . $FieldName . "=:" . $FieldName;

                if ($Index < $Total - 1)
                {
                    $String = $String . ", ";
                }

                $Index += 1;
            }

            $String = $String . " WHERE Id =:UserId ;";

            $stmt = $this->DatabaseConnection->prepare($String);
            $stmt->bindValue(":UserId", $UserId);

            foreach($UpdateTable as $FieldName => $FieldValue)
            {
                $stmt->bindValue(":" . $FieldName, $FieldValue);
            }

            $stmt->execute();

            if (isset($_SESSION["Account"]) and $_SESSION["Account"]["Id"] === $UserId) // Update current
            {
                if (isset($_SESSION["Account"][$FieldName]))
                {
                    $_SESSION["Account"][$FieldName] = $FieldValue;
                }
            }

        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }
    }

    public function GetUserFromUsername($_Username)
    {
        // CORE
        $result = null;

        // Functions
        // INIT
        try {
            $stmt = $this->DatabaseConnection->prepare("SELECT * FROM user WHERE Username=:Username ;");
            $stmt->bindValue(":Username", $_Username);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }

        //var_dump($result);

        if (!$result)
        {
            $this->Debug->Log("UserDataSet.php | GetUserFromUsername | _Username: $_Username | result: $result | Error: No user found!");
            return null;
        }

        $User = new User($result["Id"], $result["Username"], $result["Password"], $result["UserType"], $result["LastLoginTime"], $result["Email"], $result["PhoneNumber"]);

        return $User;
    }

    public function AuthenticateUserCredentials($_Username, $_Password, $_Token)
    {
        // CORE
        $SessionDataSet = new SessionDataSet();
        $Auth = false;

        // Functions
        // INIT
        $User = $this->GetUserFromUsername($_Username);

        if (!isset($User))
        {
            return null;
        }

        if (gettype($_Token) == "string")
        {
            $TokenToCompareTo = $SessionDataSet->GetSessionTokenFromUserId($User->GetId());

            if ($TokenToCompareTo == $_Token)
            {
                $Auth = true;
            }
        }

        if (!$Auth and gettype($_Password) == "string")
        {
            if ($_Password == $User->GetPassword())
            {
                $Auth = true;
            }
        }

        return $Auth;
    }

    public function UserLogout()
    {
        // CORE
        //global $Shortcuts;

        // Functions
        // INIT
        $_SESSION["LoggedIn"] = null;
        $_SESSION["Account"] = null;
        $_SESSION["Cookies"] = null;

        $CookiesToRemove = array("Username", "Token");

        foreach($CookiesToRemove as &$CookieName)
        {
            $this->Shortcuts->RemoveCookie($CookieName);
        }
    }

    public function SaveLoginTime($_UserId, $DateTime)
    {
        // CORE
        //global $Debug;

        // Functions
        // INIT
        try
        {
            $stmt = $this->DatabaseConnection->prepare("UPDATE user SET LastLoginTime=:DateTime WHERE Id=:UserId");
            $stmt->bindValue(":DateTime", $DateTime);
            $stmt->bindValue(":UserId", $_UserId);
            $stmt->execute();
        }
        catch (PDOException $e)
        {
            $this->Debug->Log($e->getMessage());
        }
    }

    public function UserLogin($_Username, $_Password, $_Token)
    {
        // CORE
        //global $Debug;

        $Auth = $this->AuthenticateUserCredentials($_Username, $_Password, $_Token);

        // Functions
        // INIT
        $this->Debug->Log("UserDataSet.php | UserLogin | Auth: " . $Auth);

        if (!isset($Auth))
        {
            $this->Debug->Log("UserDataSet.php | UserLogin | Auth is not set");
            return null;
        }
        else
        {
            if ($Auth)
            {
                $this->Debug->Log("UserDataSet.php | UserLogin | Logging In");

                $User = $this->GetUserFromUsername($_Username);


                $_SESSION["LoggedIn"] = true;
                $_SESSION["Account"] = array(
                    "Username" => $User->GetUsername(),
                    "UserType" => $User->GetUserType(),
                    "LastLoginTime" => $User->GetLastLoginTime(),
                    "Email" => $User->GetEmail(),
                    "PhoneNumber" => $User->GetPhoneNumber(),
                    "Id" => $User->GetId(),
                    "Token" => $User->GetToken()
                );

                $this->SaveLoginTime($User->GetId(), date("d/m/y H:i"));
            }
        }

        return $Auth;
    }


    // Accessors
    public function GetDatabaseConnection()
    {
        return $this->DatabaseConnection;
    }
}
