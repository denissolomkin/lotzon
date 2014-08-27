<?php

abstract class Model
{
    public function __construct() 
    {
        throw new ApplicationException("Model constructor must be overrided");
    }
}