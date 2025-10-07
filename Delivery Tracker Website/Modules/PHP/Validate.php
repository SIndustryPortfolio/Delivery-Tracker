<?php

// Modules
require_once("Shortcuts.php");

class Validate
{
    private $TypeToFunction;
    private $Shortcuts;

    public function __construct()
    {
        $this->Shortcuts = new Shortcuts();

        $ValidatePhoneNumber = function($String)
        {
            // CORE
            $Pass = true;
            $Numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

            // Functions
            // INIT
            for ($i = 0; $i < strlen($String) - 1; $i++)
            {
                if (array_search($this->Shortcuts->GetCharacterFromString($String, $i), $Numbers) === false)
                {
                    $Pass = false;
                    break;
                }
            }

            return $Pass;
        };

        $ValidateEmail = function($String)
        {
            // CORE
            $TopLevels = array(".com", ".co.uk", ".ac.uk", ".org", ".gov", ".edu", ".net", ".pro", ".tel");

            if ($this->Shortcuts->CountNumberOfChars($String, "@") !== 1)
            {
                return false;
            }

            $Pass = false;

            foreach($TopLevels as &$TopLevel)
            {
                if (substr(strtolower($String), ((strlen($String) - strlen($TopLevel))), strlen($TopLevel)) == strtolower($TopLevel))
                {
                    $Pass = true;
                    break;
                }
            }

            return $Pass;
        };


        // CORE
        $this->TypeToFunction = array(
            "Email" => $ValidateEmail,
            "PhoneNumber" => $ValidatePhoneNumber
        );
    }

    // Functions
    // MECHANICS


    // MECHANICS
    public function ValidateInput($Type, $String)
    {
        return $this->TypeToFunction[$Type]($String);
    }
}