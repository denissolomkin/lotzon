<?php

class Common
{
    /**
     * Save image in many resolutions
     *
     * @param      $imgPostName     string
     * @param      $path            string
     * @param      $new_name        string
     * @param      $resolutions     array
     * @param null $imgFile         string
     */
    public static function saveImageMultiResolution($imgPostName, $path, $new_name, $resolutions, $imgFile = NULL)
    {
        foreach ($resolutions as $res) {
            if (is_array($res)) {
                if (isset($res[0]) && isset($res[1])) {
                    $width  = $res[0];
                    $height = $res[1];
                } else {
                    $width  = $res[0];
                    $height = NULL;
                }
            } else {
                continue;
            }
            if ($imgFile) {
                $img = \WideImage::load($imgfile);
            } else {
                $img = \WideImage::loadFromUpload($imgPostName);
            }
            $img->resize($width, $height)->saveToFile($path . $width . "/" . $new_name);
        }
    }

    public static function removeImageMultiResolution($path, $filename, $resolutions)
    {
        foreach ($resolutions as $res) {
            if (is_array($res)) {
                $width  = $res[0];
            } else {
                continue;
            }
            @unlink($path . $width . "/" . $filename);
        }
    }

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
    public static function getHTTPHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function viewNumberFormat($number) {
        $is_float = false;
        if ((int)$number != $number) {
            $is_float = true;
        }

        return number_format($number, $is_float ? 2 : 0,'.', ' ');
    }
}