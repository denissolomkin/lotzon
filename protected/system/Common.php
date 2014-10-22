<?php

class Common
{
    public static function sendEmail($to, $subject, $template, $data = array(), $headers = array())
    {
        $headers[] = "From: Lotzon.com <" . Config::instance()->defaultSenderEmail .">";
        $headers[] = "Content-type: text/html; charset=utf-8" . Config::instance()->defaultSenderEmail;
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
}