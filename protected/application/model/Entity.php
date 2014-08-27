<?php

abstract class Entity 
{
    public function __construct() 
    {
        throw new ApplicationException("Entity constructor must be overrided");
    }
}