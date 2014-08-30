<?php

class Application 
{

    public static function import($path)
    {
        self::loadClasses($path);
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