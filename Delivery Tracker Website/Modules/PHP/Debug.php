<?php

class Debug
{
    public function __construct()
    {

    }

    // MISC
    public function Log($String)
    {
       echo "<script>console.log('$String');</script>";
    }
}