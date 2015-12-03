<?php

class Common
{
    /**
     * Сохранение картинки сразу в нескольких разрешениях
     *
     * @param      $imgPostName     string      имя параметра картинки из $_FILES
     * @param      $path            string      путь сохранения к которому прибавится $width/
     * @param      $new_name        string      имя файла
     * @param      $resolutions     array       массив разрешений array(array(width,[height]))
     * @param null $imgFile         string      прямое имя файла для загрузки картинки (если брать не из $_FILES)
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