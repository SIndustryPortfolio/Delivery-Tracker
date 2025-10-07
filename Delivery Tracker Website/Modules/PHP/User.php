<?php

// Modules
require_once("SessionDataSet.php");
require_once("Database.php");

class User
{
    private $DatabaseConnection;
    private $Id;
    private $Username;
    private $Password;
    private $UserType;
    private $LastLoginTime;
    private $Email;
    private $PhoneNumber;
    private $Token;
    private $UserTypeInfo;

    public function __construct($_Id, $_Username, $_Password, $_UserType, $_LastLoginTime, $_Email, $_PhoneNumber)
    {
        // Dirs
        $RootFolder = "./";
        $ResourcesFolder = $RootFolder . "Resources/";
        $JSONFolder = $ResourcesFolder . "JSON/";

        // JSON
        $UserTypeJSONFile = file_get_contents($JSONFolder . "UserType.json");

        // CORE
        $this->UserTypeInfo = json_decode($UserTypeJSONFile, true);
        $SessionDataSet = new SessionDataSet();

        // Functions
        // INIT
        $this->Id = $_Id;
        $this->Username = $_Username;
        $this->Password = $_Password;
        $this->UserType = $this->UserTypeInfo[$_UserType]["Name"];
        $this->LastLoginTime = $_LastLoginTime;
        $this->Email = $_Email;
        $this->PhoneNumber = $_PhoneNumber;

        $db = new Database();
        $this->DatabaseConnection = $db->GetConnection();

        $this->Token = $SessionDataSet->GetSessionTokenFromUserId($_Id);

        if (!isset($this->Token))
        {
            $this->Token = $SessionDataSet->GenerateSessionTokenFromUserId($_Id);
            $SessionDataSet->SetUserSessionToken($_Id, $this->Token);
        }
    }

    // MUTATORS
    public function SetLastLoginTime($_LastLoginTime)
    {
        $this->LastLoginTime = $_LastLoginTime;
    }

    // Accessors
    public function GetLastLoginTime()
    {
        return $this->LastLoginTime;
    }

    public function GetPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    public function GetEmail()
    {
        return $this->Email;
    }

    public function GetUsername()
    {
        return $this->Username;
    }

    public function GetToken()
    {
        return $this->Token;
    }

    public function GetUserType()
    {
        return $this->UserType;
    }

    public function GetPassword()
    {
        return $this->Password;
    }

    public function GetId()
    {
        return $this->Id;
    }
}