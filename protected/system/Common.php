<?php
use GeoIp2\Database\Reader;

class Common
{
    /**
     * Save image in many resolutions
     *
     * @param      $imgPostName     string
     * @param      $path            string
     * @param      $new_name        string
     * @param      $resolutions     array
     *                              array(array(width,[height]),...)
     *                              if height not set  - height proportional to width
     *                              if height=='crop'  - crop image to width x width dimension
     * @param null $imgFile         string
     */
    public static function saveImageMultiResolution($imgPostName, $path, $new_name, $resolutions = array(1), $imgFile = NULL)
    {
        foreach ($resolutions as $res) {
            $crop   = false;
            $resize = true;
            if (is_array($res)) {
                if (isset($res[0]) && isset($res[1])) {
                    $width  = $res[0];
                    if ($res[1]=='crop') {
                        $height = $res[0];
                        $crop   = true;
                    } else {
                        $height = $res[1];
                    }
                } else {
                    $width  = $res[0];
                    $height = NULL;
                }
            } else {
                $resize = false;
            }
            if ($imgFile) {
                $img = \WideImage::load($imgFile);
            } else {
                $img = \WideImage::loadFromUpload($imgPostName);
            }
            if ($crop) {
                $min_dimension = min($img->getWidth(),$img->getHeight());
                $img = $img->crop("center","center",$min_dimension,$min_dimension);
            }
            if ($resize) {
                $img->resize($width, $height)->saveToFile($path . $width . "/" . $new_name);
            } else {
                $img->saveToFile($path . $new_name);
            }
        }
    }

    public static function removeImageMultiResolution($path, $filename, $resolutions = array(1))
    {
        foreach ($resolutions as $res) {
            if (is_array($res)) {
                @unlink($path . $res[0] . "/" . $filename);
            } else {
                @unlink($path . $filename);
            }
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

    public static function getUserIpCountry()
    {
        try {
            $geoReader = new Reader(PATH_MMDB_FILE);
            $country   = $geoReader->country(Common::getUserIp())->country->isoCode;
        } catch (\Exception $e) {
            $country = CountriesModel::instance()->defaultCountry();
        }

        return $country;
    }

    public static function getUserIpLang()
    {
        //return $this->getUserIpCountry();
        return 'RU';
    }
}
