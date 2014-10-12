<?php

class SEODBProcessor
{
    public function updateSEO($seo)
    {
        $sql = "REPLACE INTO `SEO` (`Identifier`, `Title`, `Description`, `Keywords`) VALUES (:id, :title, :desc, :kw)";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $seo['id'],
                ':title' => $seo['title'],
                ':desc'  => $seo['desc'],
                ':kw'    => $seo['kw'],
            ));
        } catch (PDOException $e) {
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
            'id'    => '',
            'title' => '',
            'desc'  => '',
            'kw'    => '',
        );
        if ($sth->rowCount()) {
            $row = $sth->fetch();

            $seo = array(
                'id'    => $row['Identifier'],
                'title' => $row['Title'],
                'desc'  => $row['Description'],
                'kw'    => $row['Keywords'],
            );
        }

        return $seo;
    }
}