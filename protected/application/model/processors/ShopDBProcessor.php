<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class ShopDBProcessor
{
    public function createCategory(ShopCategory $category)
    {
        $sql = "INSERT INTO `ShopCategories` (`Title`) VALUES (:title)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':title' => $category->getName(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        $category->setId(DB::Connect()->lastInsertId());

        return $category;
    }   

    public function deleteCategory(ShopCategory $category)
    {
        $queries = array(
            'DELETE FROM `ShopItems` WHERE `CategoryId` = :category',
            'DELETE FROM `ShopCategories` WHERE `Id` = :category',
        );

        DB::Connect()->beginTransaction();
        try {

            foreach ($queries as $query) {
                DB::Connect()->prepare($query)->execute(array(
                    ':category' => $category->getId(),
                ));
            }

            DB::Connect()->commit();
        } catch (PDOException $e) {
            DB::Connect()->rollback();
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        return true;
    }

    public function createItem(ShopItem $item) 
    {
        $sql = "INSERT INTO `ShopItems` (`Title`, `Price`, `Quantity`, `Visible`, `Image`, `CategoryId`) VALUES (:title, :price, :quantity, :visible, :image, :category)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':title'    => $item->getTitle(),
                ':price'    => $item->getPrice(),
                ':quantity' => $item->getQuantity(),
                ':visible'  => $item->isVisible(),
                ':image'    => $item->getImage(),  
                ':category' => $item->getCategory()->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);      
        }

        $item->setId(DB::Connect()->lastInsertId());

        return $item;
    }

    public function updateItem(ShopItem $item) 
    {
        $sql = "UPDATE `ShopItems` SET `Title` = :title, `Price` = :price, `Quantity` = :quantity, `Visible` = :visible WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':title'    => $item->getTitle(),
                ':price'    => $item->getPrice(),
                ':quantity' => $item->getQuantity(),
                ':visible'  => $item->isVisible(),
                ':id'       => (int)$item->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        return $item;
    }

    public function deleteItem(ShopItem $item) 
    {
        $sql = "DELETE FROM `ShopItems` WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id' => (int)$item->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);       
        }

        return true;
    }

    public function loadShop()
    {
        $sql = "SELECT `si`.*, `sc`.*, `si`.`Id` as `ItemId`, `sc`.`Id` AS `CategoryId`, `sc`.`Title` AS `CategoryTitle`, `si`.`Title` AS `ItemTitle`
                    FROM `ShopCategories` AS `sc`
                    LEFT JOIN `ShopItems` AS `si` ON `si`.`CategoryId` = `sc`.`Id`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(

            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);    
        }

        $rows = $sth->fetchAll();
        $categories = array();

        foreach ($rows as $row) {
            if (!isset($categories[$row['CategoryId']])) {
                $catObj = new ShopCategory();
                $catObj->setId($row['CategoryId']);
                $catObj->setName($row['CategoryTitle']);

                $categories[$row['CategoryId']] = $catObj;
            }

            $item = new ShopItem();
            $item->setId($row['ItemId'])
                 ->setTitle($row['ItemTitle'])
                 ->setPrice($row['Price'])
                 ->setQuantity($row['Quantity'])
                 ->setImage($row['Image'])
                 ->setCategory($categories[$row['CategoryId']]);

            if ($item->getId()) {
                $categories[$row['CategoryId']]->addItem($item);    
            }
        }

        return $categories;
    }
}