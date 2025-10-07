<?php
class Shortcuts
{
    public function __construct()
    {
    }

    // Functions
    public function GetCurrentLocation()
    {
        echo "<script>";
            echo "let currentLongitude;";
            echo "let currentLatitude;";
            //echo

            echo "if ('geolocation' in navigator) {";
                echo "navigator.geolocation.getCurrentPosition(function(Position) { currentLongitude = Position.coords.longitude; currentLatitude = Position.coords.latitude; }, function() { currentLatitude = 0; currentLongitude = 0; });";
            echo "} else {";

            echo "}";

        echo "</script>";
    }

    public function Clamp($current, $min, $max)
    {
        return max($min, min($max, $current));
    }

    public function CreateQrCode($Id, $String, $Size)
    {
        // CORE
        $Sizes = array(
            "Small" => array("Width" => 60, "Height" => 60),
            "Medium" => array("Width" => 120, "Height" => 120),
            "Large" => array("Width" => 240, "Height" => 240)
        );

        // Functions
        // INIT
        echo "<div id='QrCode" . $Id . $Size . "' class='text-center'>";
            echo "<script>";
                //echo "let QrCode = new QRCode('qrcode" . $Delivery->GetId() . "');";
                //echo "QrCode.makeCode(" . $Delivery->GetId() . ");";
                //echo "new QRCode(document.getElementById('qrcode" . $Delivery->GetId() . "'), '" . $Delivery->GetId() .  "');";

                $FullVarName = "QrCode" . $Id . $Size;

                echo "var " . $FullVarName . " = new QRCode('" . $FullVarName . "', { text: '" . $String . "', width: " . $Sizes[$Size]["Width"] . ", height: " . $Sizes[$Size]["Height"] . " });";
                echo "var " . $FullVarName . "Div = document.getElementById('" . $FullVarName . "');";


                echo "for (var i = 0; i < " . $FullVarName . "Div.children.length; i++) {";
                    echo "var Child = " . $FullVarName . "Div.children[i];";
                    echo "Child.classList.add('mx-auto');";
                echo "}";

                //echo "QrCode.makeCode()";
            echo "</script>";
        echo "</div>";
    }

    public function GetRoot()
    {
        return $this->GetUrl() . "/clientserver/Trimester2/Server%20Side";
    }

    public function GetUrl()
    {
        // CORE
        $Link = null;

        // Functions
        // INIT
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        {
            $Link = "https";
        }
        else {
            $Link = "http";
        }

        $Link .= "://";

        $Link .= $_SERVER['HTTP_HOST'];

        //$Link .= $_SERVER['REQUEST_URI'];

        return $Link;
    }

    public function RemoveWhiteSpaces($String)
    {
        // CORE
        $RunningString = "";

        // Functions
        // INIT
        $StringSegments = explode(" ", $String);

        for ($i = 0; $i < sizeof($StringSegments); $i++)
        {
            $RunningString = $RunningString . $StringSegments[$i];
        }

        return $RunningString;
    }

    public function StringToBool($String)
    {
        if (gettype($String) == "boolean")
        {
            return $String;
        }

        // CORE
        $Lower = strtolower($String);

        // Functions
        // INIT
        if ($Lower == "true" or $Lower == "on" or $Lower == "1")
        {
            return true;
        }
        elseif ($Lower == "false" or $Lower == "off" or $Lower == "0")
        {
            return false;
        }
        else
        {
            return null;
        }
    }

    public function CountNumberOfChars($String, $Char)
    {
        // CORE
        $Count = 0;

        // Functions
        // INIT
        for ($i = 0; $i < strlen($String); $i++)
        {
            if ($this->GetCharacterFromString($String, $i) == $Char)
            {
                $Count += 1;
            }
        }

        return $Count;
    }

    public function DoStringsMatch($String1, $String2, $CaseSensitive)
    {
        // Functions
        // INIT
        if (!$CaseSensitive)
        {
            $String1 = strtolower($String1);
            $String2 = strtolower($String2);
        }

        if ($String1 == $String2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function GetCharacterFromString($String, $CharacterPos)
    {
        // Functions
        // INIT
        return substr($String, $CharacterPos, 1);
    }

    public function DaysToSeconds($Days)
    {
        return 86400 * $Days;
    }

    public function HideRestOfString($String, $ToKeepUpTo)
    {
        // Functions
        // INIT
        $NewString = "";

        for ($i = 0; $i < strlen($String); $i++)
        {
            if ($i < $ToKeepUpTo)
            {
                $NewString = $NewString . $this->GetCharacterFromString($String, $i);
            }
            else
            {
                $NewString  = $NewString . "*";
            }
        }

        return $NewString;
    }

    public function GenerateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = "";

        for ($i = 0; $i < strlen($characters); $i++) {
            $randomString = $randomString . $characters[random_int(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function RemoveCookie($CookieName)
    {
        // Functions
        // INIT
        try
        {
            if (isset($_COOKIE[$CookieName]))
            {
                unset($_COOKIE[$CookieName]);
            }

            setcookie($CookieName, null, floor(time() - 3600), "/");

            echo "<script>";
                echo "document.cookie = '" . $CookieName . "=; Max-Age=-99999999;'";
            echo "</script>";
        }
        catch (Exception $e)
        {
            echo "<script>console.log('" . $e->getMessage() . "'); </script>";
        }
    }

    public function NewPageSetup()
    {
        // Functions
        // INIT
        if (!isset($_SESSION["View"]))
        {
            return null;
        }

        $_SESSION["View"]->LoadAlert = null;
        $_SESSION["View"]->Page = new stdClass();

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if (isset($_POST["Cookies"]))
            {
                $_SESSION["Cookies"] = $this->StringToBool($_POST["Cookies"]);
            }
        }
    }

    public function CreateAlert($Type, $Message)
    {
        // Functions
        // INIT
        $LoadAlert = new stdClass();
        $LoadAlert->Type = $Type;
        $LoadAlert->Message = $Message;

        return $LoadAlert;
    }

    public function IsEmptyString($String)
    {
        if (isset($String))
        {
            if (strlen($String) > 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }
}