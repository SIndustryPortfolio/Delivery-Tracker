<?php

class Database
{
    private $Connection;
    public function __construct()
    {
        // Dirs
        $ResourcesFolder = "./Resources/";
        $ResourcesJSONFolder = $ResourcesFolder . "JSON/";


        // CORE
        $DatabaseJSONFile = file_get_contents($ResourcesJSONFolder . "Database.json");
        $DatabaseInfo = json_decode($DatabaseJSONFile, false);

        //echo getcwd() . $DatabaseJSONFile;
        //var_dump($DatabaseInfo);

        $_Connection = null;

        // INIT
        try {
            $_Connection = new PDO("mysql:host=$DatabaseInfo->ServerName;dbname=$DatabaseInfo->DatabaseName", $DatabaseInfo->Username, $DatabaseInfo->Password);
            $_Connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e)
        {
            echo $e->getMessage();
        }

        $this->Connection = $_Connection;
    }

    // Accessors
    public function GetConnection()
    {
        return $this->Connection;
    }
}