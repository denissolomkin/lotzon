<?php
Application::import(PATH_APPLICATION . 'model/DBProcessor.php');
Application::import(PATH_INTERFACES . 'IProcessor.php');

class ShopDBProcessor extends DBProcessor implements IProcessor
{

    public function fetch(Entity $item)
    {
        $sql = "SELECT `si`.*, `sc`.*, `si`.`Id` as `ItemId`, `sc`.`Id` AS `CategoryId`, `sc`.`Title` AS `CategoryTitle`, `si`.`Title` AS `ItemTitle` FROM `ShopItems` AS `si`
                LEFT JOIN `ShopCategories` AS `sc` ON `si`.`CategoryId` = `sc`.`Id`
                WHERE `si`.`Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'   => (int)$item->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        if ($row = $sth->fetch()) {
            $catObj = new ShopCategory();
            $catObj->setId($row['CategoryId']);
            $catObj->setName($row['CategoryTitle']);

            $item->setId($row['ItemId'])
                ->setTitle($row['ItemTitle'])
                ->setPrice($row['Price'])
                ->setQuantity($row['Quantity'])
                ->setCountries(unserialize($row['Countries']))
                ->setImage($row['Image'])
                ->setCategory($catObj);

        } else {
            throw new ModelException("ITEM_NOT_FOUND", 404);
        }
        return $item;
    }

    public function create(Entity $item)
    {
        $sql = "INSERT INTO `ShopItems` (`Title`, `Price`, `Quantity`, `Visible`, `Image`, `CategoryId`, `Countries`) VALUES (:title, :price, :quantity, :visible, :image, :category, :countries)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':title'    => $item->getTitle(),
                ':price'    => $item->getPrice(),
                ':quantity' => $item->getQuantity(),
                ':countries'=> serialize($item->getCountries()),
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

    public function update(Entity $item)
    {
        $sql = "UPDATE `ShopItems` SET `Title` = :title, `Price` = :price, `Quantity` = :quantity, `Countries` = :countries WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':title'    => $item->getTitle(),
                ':price'    => $item->getPrice(),
                ':quantity' => $item->getQuantity(),
                ':countries'=> serialize($item->getCountries()),
                ':id'       => (int)$item->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        return $item;
    }

    public function delete(Entity $item)
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
    public function createCategory(ShopCategory $category)
    {
        $sql = "INSERT INTO `ShopCategories` (`Title`,`Order`) VALUES (:title, :order)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':title' => $category->getName(),
                ':order' => $category->getOrder()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        $category->setId(DB::Connect()->lastInsertId());

        return $category;
    }

    public function updateCategory(ShopCategory $category)
    {
        $sql = "UPDATE `ShopCategories` SET `Title` = :title, `Order` = :order WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':title' => $category->getName(),
                ':order' => $category->getOrder(),
                ':id'    => $category->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $category;
    }

    public function deleteCategory(ShopCategory $category)
    {
        $queries = array(
            'DELETE FROM `ShopItems` WHERE `CategoryId` = :category',
            'DELETE FROM `ShopCategories` WHERE `Id` = :category',
        );

        try {

            foreach ($queries as $query) {
                DB::Connect()->prepare($query)->execute(array(
                    ':category' => $category->getId(),
                ));
            }

        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function loadShop()
    {
        $sql = "SELECT `si`.*, `sc`.*, `si`.`Id` as `ItemId`, `sc`.`Id` AS `CategoryId`, `sc`.`Title` AS `CategoryTitle`, `sc`.`Order` AS `CategoryOrder`, `si`.`Title` AS `ItemTitle`
                    FROM `ShopCategories` AS `sc`
                    LEFT JOIN `ShopItems` AS `si` ON `si`.`CategoryId` = `sc`.`Id`
                    ORDER BY `sc`.`Order`, `si`.`Id` DESC";

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
                $catObj->setOrder($row['CategoryOrder']);

                $categories[$row['CategoryId']] = $catObj;
            }

            $item = new ShopItem();
            $item->setId($row['ItemId'])
                 ->setTitle($row['ItemTitle'])
                 ->setPrice($row['Price'])
                 ->setQuantity($row['Quantity'])
                 ->setCountries(unserialize($row['Countries']))
                 ->setImage($row['Image'])
                 ->setCategory($categories[$row['CategoryId']]);

            if ($item->getId()) {
                $categories[$row['CategoryId']]->addItem($item);    
            }
        }

        return $categories;
    }

    public function getAllItems($excludeQuantibleItems = true)
    {   
        $items = array();
        $shop = $this->loadShop();

        foreach ($shop as $category) {
            foreach ($category->getItems() as $item) {
                if ($excludeQuantibleItems && $item->getQuantity() > 0) {
                    continue;
                }

                $items[$item->getId()] = $item;
            }
        }

        return $items;
    }
}