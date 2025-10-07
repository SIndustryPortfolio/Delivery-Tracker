<?php

class Delivery
{
    private $Id;
    private $Name;
    private $Address1;
    private $Address2;
    private $Postcode;
    private $Deliverer;
    private $Latitude;
    private $Longitude;
    private $Status;
    private $DelPhoto;

    public function __construct($_Id, $_Name, $_Address1, $_Address2, $_Postcode, $_Deliverer, $_Latitude, $_Longitude, $_Status, $_DelPhoto)
    {
        // Init
        $this->Id = $_Id;
        $this->Name = $_Name;
        $this->Address1 = $_Address1;
        $this->Address2 = $_Address2;
        $this->Postcode = $_Postcode;
        $this->Deliverer = $_Deliverer;
        $this->Latitude = $_Latitude;
        $this->Longitude = $_Longitude;
        $this->Status = $_Status;
        $this->DelPhoto = $_DelPhoto;
    }

    // Functions
    // MECHANICS
    public function GetDictionary()
    {
        // Functions
        // INIT
        $Dict = array(
            "Id" => $this->Id,
            "Name" => $this->Name,
            "Address1" => $this->Address1,
            "Address2" => $this->Address2,
            "Postcode" => $this->Postcode,
            "Deliverer" => $this->Deliverer,
            "Latitude" => $this->Latitude,
            "Longitude" => $this->Longitude,
            "Status" => $this->Status,
            "DelPhoto" => $this->DelPhoto
        );

        return $Dict;
    }


    // ACCESSORS
    public function GetId()
    {
        return $this->Id;
    }

    public function GetName()
    {
        return $this->Name;
    }

    public function GetAddress1()
    {
        return $this->Address1;
    }

    public function GetAddress2()
    {
        return $this->Address2;
    }

    public function GetPostcode()
    {
        return $this->Postcode;
    }

    public function GetDeliverer()
    {
        return $this->Deliverer;
    }

    public function GetLatitude()
    {
        return $this->Latitude;
    }

    public function GetLongitude()
    {
        return $this->Longitude;
    }

    public function GetStatus()
    {
        return $this->Status;
    }

    public function GetDelPhoto()
    {
        return $this->DelPhoto;
    }

}