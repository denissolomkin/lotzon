<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class BlogsDBProcessor implements IProcessor
{
    public function create(Entity $blog)
    {
        $sql = "INSERT INTO `Blog` (`Id`, `Title`, `Img`, `Text`, `Lang`, `DateCreated`, `DateModify`, `Enable`) VALUES (:id, :title, :img, :text, :lang, :datecreated, :datemodify, :enable)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'          => $blog->getId(),
                ':title'       => $blog->getTitle(),
                ':img'         => $blog->getImg() ?: null,
                ':text'        => $blog->getText() ?: null,
                ':lang'        => $blog->getLang(),
                ':datecreated' => time(),
                ':datemodify'  => time(),
                ':enable'      => $blog->getEnable(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $blog;
    }

    public function update(Entity $blog)
    {
        $sql = "UPDATE `Blog` SET `Title` = :title, `Text` = :text, `Img` = :img, `Lang` = :lang, `DateModify` = :datemodify, `Enable` = :enable WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'          => $blog->getId(),
                ':title'       => $blog->getTitle(),
                ':img'         => $blog->getImg() ?: null,
                ':text'        => $blog->getText() ?: null,
                ':lang'        => $blog->getLang(),
                ':datemodify'  => time(),
                ':enable'      => $blog->getEnable(),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $blog;
    }

    public function delete(Entity $blog)
    {
        $sql = "DELETE FROM `Blog` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $blog->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $blog)
    {
        $sql = "SELECT *
                FROM `Blog`
                WHERE
                    `Id` = :id
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $blog->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Blog not found", 404);
        }

        $data = $sth->fetch();
        $blog->formatFrom('DB', $data);

        return $blog;
    }

    public function getList($lang, $count, $beforeId = NULL, $afterId = NULL, $enable = 1)
    {
        $sql = "SELECT
                    *
                FROM `Blog`
                WHERE
                    `Enable` = :enable
                AND
                    `Lang` = :lang"
                . (($beforeId === NULL) ? "" : " AND (`Id` < $beforeId)")
                . (($afterId === NULL) ? "" : " AND (`Id` > $afterId)")
                . "
                ORDER BY `DateCreated` DESC
                LIMIT " . (int)$count;
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':lang'   => $lang,
                ':enable' => $enable,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $blogs = array();
        foreach ($sth->fetchAll() as $blogData) {
            $blog = new \Blog;
            $blogs[$blogData['Id']] = $blog->formatFrom('DB',$blogData);
        }

        return $blogs;
    }

    public function getSimilarList($blogId, $lang, $count, $beforeId = NULL, $afterId = NULL, $enable = 1)
    {
        $sql = "SELECT
                    `Blog`.`Id`,
                    `Blog`.`Title`
                FROM `Blog`
                JOIN
                    `BlogSimilar`
                ON
                    `BlogSimilar`.`SimilarBlogId` = `Blog`.`Id`
                WHERE
                    `BlogSimilar`.`BlogId` = :blogid
                AND
                    `Blog`.`Enable` = :enable
                AND
                    `Blog`.`Lang` = :lang"
            . (($beforeId === NULL) ? "" : " AND (`Id` < $beforeId)")
            . (($afterId === NULL) ? "" : " AND (`Id` > $afterId)")
            . "
                ORDER BY `DateCreated` DESC";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':lang'   => $lang,
                ':blogid'   => $blogId,
                ':enable' => $enable,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $blogs = array();
        foreach ($sth->fetchAll() as $blogData) {
            $blog = new \Blog;
            $blogs[$blogData['Id']] = $blog->formatFrom('DB',$blogData);
        }

        return $blogs;
    }

}
