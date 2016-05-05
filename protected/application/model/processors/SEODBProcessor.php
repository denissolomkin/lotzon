<?php

class SEODBProcessor
{
    public function updateSEO($seo)
    {
        $sql = "REPLACE INTO `SEO` (`Identifier`, `Title`, `Description`, `Keywords`, `Pages`, `Debug`, `WebSocketReload`, `SiteVersion`, `Multilanguage`)
                VALUES (:id, :title, :desc, :kw, :pages, :dbg, :ws, :sv, :ml)";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $seo['Identifier'],
                ':title' => $seo['Title'],
                ':desc'  => $seo['Description'],
                ':kw'    => $seo['Keywords'],
                ':pages' => $seo['Pages'],
                ':dbg'   => $seo['Debug'],
                ':ws'    => $seo['WebSocketReload'],
                ':sv'    => $seo['SiteVersion'],
                ':ml'    => $seo['Multilanguage']
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        return $seo;
    }

    public function getSEOSettings()
    {
        $sql = "SELECT * FROM `SEO` LIMIT 1";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $seo = array(
            'Identifier'      => '',
            'Title'           => '',
            'Description'     => '',
            'Keywords'        => '',
            'Pages'           => '',
            'Debug'           => '',
            'WebSocketReload' => '',
            'SiteVersion'     => '',
            'Multilanguage'   => ''
        );

        if ($sth->rowCount()) {
            $row = $sth->fetch();
            $seo = array(
                'Identifier'      => $row['Identifier'],
                'Title'           => $row['Title'],
                'Description'     => $row['Description'],
                'Keywords'        => $row['Keywords'],
                'Pages'           => $row['Pages'],
                'Debug'           => $row['Debug'],
                'WebSocketReload' => $row['WebSocketReload'],
                'SiteVersion'     => $row['SiteVersion'],
                'Multilanguage'   => $row['Multilanguage'],
            );
        }

        return $seo;
    }
}