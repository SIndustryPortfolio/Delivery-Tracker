<?php
class UserTypeDataSet
{
    // CORE
    private $UserTypeInfo;

    public function __construct()
    {
        // Dirs
        $RootFolder = "./";
        $ResourcesFolder = $RootFolder . "Resources/";
        $JSONFolder = $ResourcesFolder . "JSON/";

        // Functions
        // INIT
        $UserTypeJSONFile = file_get_contents($JSONFolder . "UserType.json");

        $this->UserTypeInfo = json_decode($UserTypeJSONFile, true);
    }

    public function GetUserTypeFromName($RoleName)
    {
        // CORE
        //global $UserTypeInfo;

        // Functions
        // INIT
        foreach($this->UserTypeInfo as $RoleIndex => $Info)
        {
            if ($Info["Name"]  == $RoleName)
            {
                return $RoleIndex;
            }
        }

        return null;
    }

    public function IsValidAccessLevel($BaseRoleName, $ToCompareToRoleName)
    {
        // Functions
        // INIT
        $BaseRoleIndex = $this->GetUserTypeFromName($BaseRoleName);
        $ToCompareRoleIndex = $this->GetUserTypeFromName($ToCompareToRoleName);

        if ($BaseRoleIndex >= $ToCompareRoleIndex)
        {
            return true;
        }

        return false;
    }
}