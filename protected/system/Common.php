<?php

class Common
{
    public static function sendEmail($to, $subject, $template, $data = array(), $headers = array())
    {
        $headers[] = "From: Lotzon.com <" . Config::instance()->defaultSenderEmail .">";
        $headers[] = "Content-type: text/html; charset=utf-8";
        $hs = join("\n", $headers);

        if ($template) {
            if (file_exists(PATH_TEMPLATES . 'emails/' . $template . '.php')) {
                ob_start();
                include(PATH_TEMPLATES . 'emails/' . $template . '.php');
                $template = ob_get_clean();
            }
        } 
        return mail($to, $subject, $template, $hs);
    }

    public static function getUserIp() 
    {
        // check for proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        // return real ip
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getRefererHost()
    {
        return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    }
}