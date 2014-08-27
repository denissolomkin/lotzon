<?php

class Application 
{
    public static $_initiated = false;

    public static function init()
    {
        if (!self::$_initiated) {
            
            self::loadClasses();
            self::$_initiated = true;

            return true;
        }

        return false;
    }

    private static function loadClasses($base = PATH_APPLICATION)
    {
        $files = glob($base . "*", GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::loadClasses($file);
            } else {
                if (strrchr($file, '.') == '.php') {
                    require_once $file;
                }
            }
        }
    }
}