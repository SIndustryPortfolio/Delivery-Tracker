<?php

class Status
{
    private $StatusInfo;

    public function __construct()
    {
        // Dirs
        $RootFolder = "./";
        $ResourcesFolder = $RootFolder . "Resources/";
        $JSONFolder = $ResourcesFolder . "JSON/";

        // JSON
        $StatusJSONFile = file_get_contents($JSONFolder . "Status.json");

        // CORE
        $this->StatusInfo = json_decode($StatusJSONFile, true);
    }

    // Functions
    // MECHANICS
    public function GetStatusIdFromName($Name)
    {
        // CORE
        //global $StatusInfo;

        // Functions
        // INIT
        foreach($this->StatusInfo as $Id => $Info)
        {
            if ($Name == $Info["Name"])
            {
                return $Id;
            }
        }
    }
}