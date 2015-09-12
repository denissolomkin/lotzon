<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class PlayersDBProcessor implements IProcessor
{
    public function create(Entity $player)
    {
        $sql = "INSERT INTO `Players` (`Email`, `Password`, `Salt`, `Country`, `Lang`, `Visible`, `Ip`, `Hash`, `Valid`, `Name`, `Surname`, `AdditionalData`, `ReferalId`, `Agent`, `Referer`)
                VALUES (:email, :passwd, :salt, :cc, :cl, :vis, :ip, :hash, :valid, :name, :surname, :ad, :rid, :agent, :referer)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':email'    => $player->getEmail(),
                ':passwd'   => $player->getPassword(),
                ':salt'     => $player->getSalt(),
                ':cc'       => $player->getCountry(),
                ':cl'       => $player->getLang(),
                ':vis'      => 1,
                ':ip'       => $player->getIP(),
                ':hash'     => $player->getHash(),
                ':valid'    => (int)$player->getValid(),
                ':name'     => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':ad'       => is_array($player->getAdditionalData()) ? serialize($player->getAdditionalData()) : '',
                ':rid'      => $player->getReferalId(),
                ':agent'    => $player->getAgent(),
                ':referer'    => $player->getReferer(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $player->setId(DB::Connect()->lastInsertId());

        $sql = "INSERT INTO `PlayerDates` (`PlayerId`,`Login`,`Registration`) VALUES (:id, :dl, :dr)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'       => (int)$player->getId(),
                ':dl'       => (int)$player->getDates('Login'),
                ':dr'       => (int)$player->getDates('Registration'),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }


        try {
            $player->setCookieId($_COOKIE[Player::PLAYERID_COOKIE]?:$player->getId())
                ->updateCookieId($player->getCookieId())
                ->updateIp($player->getIp())
                ->setNicName('Участник ' . $player->getId());

            if(!$_COOKIE[Player::PLAYERID_COOKIE])
                setcookie(Player::PLAYERID_COOKIE, $player->getCookieId(), time() + Player::AUTOLOGIN_COOKIE_TTL, '/');

            DB::Connect()->prepare("UPDATE `Players` SET `CookieId`=:ccid, `NicName` = CONCAT('Участник ', `Id`) WHERE `Id` = :id")->execute(array(
                ':id' => $player->getId(),
                ':ccid' => $player->getCookieId(),
            ));

        } catch (PDOException $e){}

        return $player;
    }

    public function writeLog(Entity $player, $options)
    {
        $sql = "INSERT INTO `PlayerLogs` (`PlayerId`, `Action`, `Status`, `Desc`, `Time`)
                VALUES (:id, :act, :st, :dsc, :tm)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'      => $player->getId(),
                ':act'     => $options['action'],
                ':st'      => $options['status'],
                ':dsc'     => $options['desc'],
                ':tm'      => time()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function writeLogin(Entity $player)
    {
        $sql = "INSERT INTO `PlayerLogins` (`PlayerId`, `Agent`, `Ip`, `Date`)
                VALUES (:id, :agnt, :ip, :tm)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'      => $player->getId(),
                ':agnt'      => $player->getAgent(),
                ':ip'      => $player->getLastIp(),
                ':tm'      => time()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function reportTrouble(Entity $player, $trouble)
    {
        $sql = "REPLACE INTO `PlayerTroubles` (`PlayerId`, `Trouble`, `Time`)
                VALUES (:id, :trbl, :tm)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'       => $player->getId(),
                ':trbl'     => $trouble,
                ':tm'       => time()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function updateSocial(Entity $player)
    {

        if($player->getSocialId())
            try {
                $sql = "REPLACE INTO `PlayerSocials` (`PlayerId`, `SocialId`, `SocialName`, `SocialEmail`, `Enabled`)
                      VALUES (:id, :socialid, :socialname, :socialemail, :enabled)";

                DB::Connect()->prepare($sql)->execute(array(
                    ':id'           => $player->getId(),
                    ':socialemail'  => $player->getSocialEmail(),
                    ':socialid'     => $player->getSocialId(),
                    ':socialname'   => $player->getSocialName(),
                    ':enabled'      => $player->getSocialEnable(),
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }

        return $player;

    }

    public function disableSocial(Entity $player)
    {
        if($player->getSocialName())
            try {
                $sql = "UPDATE `PlayerSocials` SET `Enabled` = 0
                        WHERE `PlayerId` = :id AND `SocialName`=:socialname";

                DB::Connect()->prepare($sql)->execute(array(
                    ':id'           => $player->getId(),
                    ':socialname'   => $player->getSocialName(),
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }

        return $player;
    }

    public function isSocialUsed(Entity $player)
    {

        if($player->getSocialId())
            try {
                $sql = "SELECT `PlayerId` FROM `PlayerSocials` WHERE
                    `SocialId` = :socialid AND `SocialName` = :socialname";

                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(
                    ':socialid'     => $player->getSocialId(),
                    ':socialname'   => $player->getSocialName(),
                ));

                return $sth->fetchColumn();

            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }


    }

    public function update(Entity $player)
    {
        $sql = "UPDATE `Players` SET
                    `Country` = :cc, `Lang` = :lang, `CookieId` = :ckid,
                    `Nicname` = :nic, `Name` = :name, `Surname` = :surname, `SecondName` = :secname,
                    `Phone` = :phone, `Qiwi` = :qiwi, `YandexMoney` = :ym, `WebMoney` = :wm,
                    `Birthday` = :bd, `Avatar` = :avatar, `Visible` = :vis, `Favorite` = :fav,
                    `Valid` = :vld, `GamesPlayed` = :gp, `AdditionalData` = :ad, `Ip` = :ip, `LastIp` = :lip, `Agent` = :agent
                WHERE `Id` = :id OR `Email` = :email";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':cc'       => $player->getCountry(),
                ':lang'     => $player->getLang(),
                ':nic'      => $player->getNicname(),
                ':name'     => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':secname'  => $player->getSecondName(),
                ':phone'    => $player->getPhone(),
                ':qiwi'     => $player->getQiwi(),
                ':wm'       => $player->getWebMoney(),
                ':ym'       => $player->getYandexMoney(),
                ':bd'       => $player->getBirthday(),
                ':avatar'   => $player->getAvatar(),
                ':id'       => $player->getId(),
                ':ip'       => $player->getIp(),
                ':ckid'     => $player->getCookieId(),
                ':email'    => $player->getEmail(),
                ':vis'      => (int)$player->getVisibility(),
                ':fav'      => is_array($player->getFavoriteCombination()) ? serialize($player->getFavoriteCombination()) : '',
                ':vld'      => $player->getValid(),
                ':gp'       => $player->getGamesPlayed(),
                ':ad'       => is_array($player->getAdditionalData()) ? serialize($player->getAdditionalData()) : '',
                ':lip'      => $player->getLastIp(),
                ':agent'    => $player->getAgent(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function updateBalance(Entity $player, $currency, $quantity)
    {
        $sql = "UPDATE `Players` SET
                    `".$currency."` = `".$currency."` + :qt
                WHERE `Id` = :id OR `Email` = :email";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'       => $player->getId(),
                ':email'    => $player->getEmail(),
                ':qt'   => $quantity,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function getAvailableIds()
    {

        $sql = "SELECT (p1.Id-1) Id
                FROM `Players` p1
                LEFT JOIN `Players` p2
                ON p2.Id = p1.Id-1
                WHERE p2.Id is null AND p1.Id>1";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $ids=array();
        foreach ($sth->fetchAll() as $data) {
            $ids[] = $data['Id'];
        }
        return $ids;
    }

    public function getBalance(Entity $player, $forUpdate = false)
    {

        $sql = "SELECT `Money`, `Points`  FROM `Players`
                WHERE `Id` = :id OR `Email` = :email";
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $player->getId(),
                ':email' => $player->getEmail(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetch();

    }

    public function isExists($id){

        $sql = "SELECT Id FROM `Players`
                WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $id,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->rowCount();
    }

    public function fetch(Entity $player)
    {
        $sql = "SELECT p.*, d.* FROM `Players` p
                LEFT JOIN `PlayerSocials` s
                  ON s.`PlayerId`=p.`Id`
                LEFT JOIN `PlayerDates` d
                  ON d.`PlayerId`=p.`Id`
                WHERE p.`Id` = :id OR p.`Email` = :email
                  OR (s.`SocialId` = :socialid AND s.`SocialName` = :socialname AND s.`Enabled` = 1)
                  OR (s.`SocialEmail` = :socialemail AND s.`SocialName` = :socialname AND s.`SocialEmail` !='' AND s.`SocialEmail` IS NOT NULL AND s.`Enabled` = 1)
                GROUP BY p.Id";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $player->getId(),
                ':email' => $player->getEmail(),
                ':socialid'    => $player->getSocialId(),
                ':socialname'    => $player->getSocialName(),
                ':socialemail'    => $player->getSocialEmail()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Player not found", 404);
        } elseif($sth->rowCount()>1){

            throw new ModelException("Found more than one player", 400);
        }

        $data = $sth->fetch();

        $player->formatFrom('DB', $data);

        return $player;
    }

    public function delete(Entity $player)
    {
        $sql = "DELETE FROM `Players` WHERE `Players`.`Id` = :id;
        DELETE FROM `EmailInvites` WHERE `InviterId` = :id;
        DELETE FROM `LotteryTickets` WHERE `PlayerId` = :id;
        DELETE FROM `LotteryTicketsArchive` WHERE `PlayerId` = :id;
        DELETE FROM `ShopOrders` WHERE `PlayerId` = :id;
        DELETE FROM `MoneyOrders` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerLogs` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerLogins` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerIps` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerDates` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerTroubles` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerNotes` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerCookies` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerNotices` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerReviews` WHERE `PlayerId` = :id;
        DELETE FROM `PlayerSocials` WHERE `PlayerId` = :id;
        DELETE FROM `Transactions` WHERE `PlayerId` = :id;";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $player->getId(),
            ));

            while ($sth->nextRowset());

            if ($player->getAvatar()) {
                @unlink( PATH_FILESTORAGE . 'avatars/' . (ceil($player->getId() / 100)) . '/' . $player->getAvatar());
            };


        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
        }

        return true;
    }


    public function ban(Entity $player, $status) {
        $sql = "UPDATE `Players` SET `Ban` = :st WHERE `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid'  => $player->getId(),
                ':st'  => $status,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function getPlayersStats()
    {
        // return array('Points'=>0,'Money'=>0,'Online'=>0,'Tickets'=>0);

        $sql = "SELECT
                SUM(Money / IFNULL((SELECT `Coefficient` FROM `MUICountries` cn LEFT JOIN `MUICurrency` c ON c.Id=cn.Currency WHERE cn.`Code`=`Players`.`Country` LIMIT 1),1)) Money,
                SUM(Points) Points,
                (SELECT COUNT( * ) FROM (SELECT 1 FROM `PlayerDates` WHERE Ping > ".(time()-(SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT')?:300)).") o) Online,
                (SELECT COUNT( * ) FROM (SELECT 1 FROM `LotteryTickets` WHERE LotteryId =0 GROUP BY PlayerId) t ) Tickets
                FROM `Players`
                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
            return $sth->fetch();
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }
    }

    public function getMaxId()
    {
        $sql = "SELECT MAX(Id) FROM `Players`";

        try {
            $res = DB::Connect()->query($sql);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $res->fetchColumn(0);
    }

    public function initCounters(Player $player)
    {

        $sql = "SELECT COUNT(Id) FROM `Players` WHERE (LastIp=:lip AND LastIp!='') OR (Ip=:lip AND Ip!='') OR (LastIp=:ip AND LastIp!='') OR (Ip=:ip AND Ip!='')";

        $sql = "SELECT
                count(DISTINCT(c.PlayerId)) CookieId,
                (SELECT COUNT(Id) FROM `Players` WHERE (LastIp=:lip AND LastIp!='') OR (Ip=:lip AND Ip!='') OR (LastIp=:ip AND LastIp!='') OR (Ip=:ip AND Ip!='')) AS Ip,
                (SELECT COUNT(Id) FROM `PlayerNotes`    WHERE `PlayerId` = `Players`.`Id`) Note,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id`) Notice,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id` AND Type='AdBlock') AdBlock,
                (SELECT COUNT(Id) FROM `PlayerLogs`     WHERE `PlayerId` = `Players`.`Id`) Log,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`Id`) MyReferal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`ReferalId`) Referal,
                (SELECT COUNT(Id) FROM `ShopOrders`     WHERE `PlayerId` = `Players`.`Id`) ShopOrder,
                (SELECT COUNT(Id) FROM `MoneyOrders`    WHERE `PlayerId` = `Players`.`Id` AND `Type`!='points') MoneyOrder,
                (SELECT COUNT(Id) FROM `PlayerReviews`  WHERE `PlayerId` = `Players`.`Id` ) Review
                FROM `Players`
                    LEFT JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId=`Players`.Id
                    INNER JOIN `PlayerCookies`c ON c.CookieId=`PlayerCookies`.CookieId
                WHERE `Players`.Id = :id";

        $sql = "SELECT
                (SELECT COUNT(Id) FROM `Players` WHERE (LastIp=:lip AND LastIp!='') OR (Ip=:lip AND Ip!='') OR (LastIp=:ip AND LastIp!='') OR (Ip=:ip AND Ip!='')) AS CounterIp,
                (SELECT COUNT(Id) FROM `PlayerNotes`    WHERE `PlayerId` = `Players`.`Id`) Note,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id`) Notice,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id` AND Type='AdBlock') CounterAdBlock,
                (SELECT COUNT(Id) FROM `PlayerLogs`     WHERE `PlayerId` = `Players`.`Id`) Log,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`Id`) MyInviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`InviterId` AND p.`InviterId`>0) Inviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`Id`) MyReferal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`ReferalId`) Referal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`CookieId` = `Players`.`CookieId` AND `Players`.`CookieId`>0) CounterCookieId,
                (SELECT count(Id)  FROM `Players` p      WHERE p.`Phone` = `Players`.`Phone` OR p.Qiwi=`Players`.Qiwi OR `p`.WebMoney = `Players`.WebMoney OR `p`.YandexMoney= `Players`.YandexMoney) as Mult,
                (SELECT COUNT(Id) FROM `ShopOrders`     WHERE `PlayerId` = `Players`.`Id`) ShopOrder,
                (SELECT COUNT(Id) FROM `MoneyOrders`    WHERE `PlayerId` = `Players`.`Id` AND `Type`!='points') MoneyOrder,
                (SELECT COUNT(Id) FROM `PlayerReviews`  WHERE `PlayerId` = `Players`.`Id` ) Review,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=1 AND Price>0) WhoMore,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=2 AND Price>0) SeaBattle
                FROM `Players`
                LEFT JOIN `PlayerIps` ON `PlayerIps`.PlayerId = Id
                LEFT JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId = Id
                WHERE `Players`.Id = :id
                GROUP BY `Players`.Id";

        $sql = "SELECT `Players`.*,
                (SELECT COUNT(Id) FROM `PlayerNotes`    WHERE `PlayerId` = `Players`.`Id`) Note,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id`) Notice,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id` AND Type='AdBlock') AdBlock,
                (SELECT COUNT(DISTINCT(i.PlayerId))) CounterIp,
                (SELECT COUNT(DISTINCT(c.PlayerId))) CounterCookieId,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`Id`) MyInviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`InviterId` AND p.`InviterId`>0) Inviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`Id`) MyReferal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`ReferalId` AND p.`ReferalId`>0) Referal,
                (SELECT COUNT(Id) FROM `PlayerLogs`     WHERE `PlayerId` = `Players`.`Id`) Log,
                (SELECT COUNT(Id) FROM `ShopOrders`     WHERE `PlayerId` = `Players`.`Id`) ShopOrder,
                (SELECT COUNT(Id) FROM `MoneyOrders`    WHERE `PlayerId` = `Players`.`Id` AND `Type`!='points') MoneyOrder,
                (SELECT COUNT(Id) FROM `PlayerReviews`  WHERE `PlayerId` = `Players`.`Id` ) Review,
                (SELECT count(*)  FROM `Players` p      WHERE p.`Phone` = `Players`.`Phone` OR p.Qiwi=`Players`.Qiwi OR `p`.WebMoney = `Players`.WebMoney OR `p`.YandexMoney= `Players`.YandexMoney) as Mult,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=1 AND Price>0) WhoMore,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=2 AND Price>0) SeaBattle,
                (SELECT COUNT(Id) FROM `LotteryTickets` WHERE `LotteryId` = 0 AND `PlayerId` = `Players`.`Id`) AS TicketsFilled
                FROM `Players`
                LEFT JOIN `PlayerDates` ON `PlayerDates` . `PlayerId`=`Id`
                LEFT JOIN `PlayerIps` ON `PlayerIps`.PlayerId =  `Players`.Id
                LEFT JOIN `PlayerIps` i ON i.Ip IN (`PlayerIps`.Ip)
                LEFT JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId = Id
                LEFT JOIN `PlayerCookies` c ON c.CookieId IN (`PlayerCookies`.CookieId)
                WHERE `Players`.Id = :id
                GROUP BY `Players`.`Id`";


        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $player->getId(),
            ));
            return $sth->fetch();
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

    }

    public function initDates(Player $player)
    {

        $sql = "SELECT * FROM `PlayerDates` WHERE PlayerId = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $player->getId(),
            ));
            return $sth->fetch();
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }
    }

    public function getPlayersCount($search=null)
    {
        if (is_array($search) AND $search['query']) {
            if($search['where']) {
                if ($search['where'] == 'Id')
                    $search = ' WHERE `Players`.Id = ' . $search['query'];
                elseif ($search['where'] == 'CookieId')
                    //$search = ' WHERE `PlayerCookies`.CookieId = ' . $search['query'];
                    $search  = 'JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId = `Players`.Id AND `PlayerCookies`.CookieId IN(SELECT CookieId FROM PlayerCookies WHERE PlayerId='.$search['query'].')';
                elseif ($search['where'] == 'ReferalId')
                    $search = ' WHERE `Players`.ReferalId = ' . $search['query'];
                elseif ($search['where'] == 'InviterId')
                    $search = ' WHERE `Players`.InviterId = ' . $search['query'];
                elseif ($search['where'] == 'Ping')
                    $search = ' WHERE `PlayerDates`.Ping > ' . (time() - (SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT') ?: 300));
                elseif ($search['where'] == 'Ip')
                    $search  = 'JOIN `PlayerIps` ON `PlayerIps`.PlayerId = `Players`.Id AND `PlayerIps`.Ip IN(SELECT Ip FROM PlayerIps WHERE PlayerId='.$search['query'].')';
                //$search= ' WHERE LastIp IN ("'.(str_replace(",",'","',$search['query'])).'") OR Ip IN ("'.(str_replace(",",'","',$search['query'])).'")';
                else
                    $search = ' WHERE ' . $search['where'] . ' LIKE "%' . $search['query'] . '%"';
            } else
                $search= ' WHERE '.(is_numeric($search['query'])?'`Players`.`Id`='.$search['query'].' OR ':'').'CONCAT(`Surname`, `Name`) LIKE "%'.$search['query'].'%" OR `NicName` LIKE "%'.$search['query'].'%" OR `Email` LIKE "%' . $search['query'].'%"';
        }

        $sql = "SELECT COUNT(DISTINCT(Id)) as `counter`
                FROM `Players` LEFT JOIN PlayerDates ON PlayerDates.PlayerId = Id
                {$search}";


        try {
            $res = DB::Connect()->query($sql);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $res->fetchColumn(0);
    }

    public function getList($limit = 0, $offset = 0, array $sort, $search=null)
    {

        if (is_array($search) AND $search['query']) {
            if($search['where']) {
                if ($search['where'] == 'Id')
                    $search = ' WHERE `Players`.Id = ' . $search['query'];
                elseif ($search['where'] == 'CookieId')
                    //$search = ' WHERE `PlayerCookies`.CookieId = ' . $search['query'];
                    $search  = 'JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId = `Players`.Id AND `PlayerCookies`.CookieId IN(SELECT CookieId FROM PlayerCookies WHERE PlayerId='.$search['query'].')';
                elseif ($search['where'] == 'ReferalId')
                    $search = ' WHERE `Players`.ReferalId = ' . $search['query'];
                elseif ($search['where'] == 'InviterId')
                    $search = ' WHERE `Players`.InviterId = ' . $search['query'];
                elseif ($search['where'] == 'Ping')
                    $search = ' WHERE `PlayerDates`.Ping > ' . (time() - (SettingsModel::instance()->getSettings('counters')->getValue('PLAYER_TIMEOUT') ?: 300));
                elseif ($search['where'] == 'Ip')
                    $search  = 'JOIN `PlayerIps` ON `PlayerIps`.PlayerId = `Players`.Id AND `PlayerIps`.Ip IN(SELECT Ip FROM PlayerIps WHERE PlayerId='.$search['query'].')';
                //$search= ' WHERE LastIp IN ("'.(str_replace(",",'","',$search['query'])).'") OR Ip IN ("'.(str_replace(",",'","',$search['query'])).'")';
                else
                    $search = ' WHERE ' . $search['where'] . ' LIKE "%' . $search['query'] . '%"';
            } else
                $search= ' WHERE '.(is_numeric($search['query'])?'`Players`.`Id`='.$search['query'].' OR ':'').'CONCAT(`Surname`, `Name`) LIKE "%'.$search['query'].'%" OR `NicName` LIKE "%'.$search['query'].'%" OR `Email` LIKE "%' . $search['query'].'%"';
        }


        $sql = "SELECT `Players`.*,
                group_concat(DISTINCT(`PlayerCookies`.CookieId)) CookieId,
                count(DISTINCT(c.PlayerId)) CountCookieId,
                (SELECT COUNT(Id) FROM `Players` p WHERE (p.LastIp=`Players` . `LastIp` AND p.LastIp!='') OR (p.Ip=`Players` . `LastIp` AND p.Ip!='') OR (p.LastIp=`Players` . `Ip` AND p.LastIp!='') OR (p.Ip=`Players` . `Ip` AND p.Ip!='')) AS CountIp,
                (SELECT COUNT(Id) FROM `PlayerNotes`    WHERE `PlayerId` = `Players`.`Id`) CountNote,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id`) CountNotice,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id` AND Type='AdBlock') CountAdBlock,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`Id`) CountMyReferal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`ReferalId` AND p.`ReferalId`>0) CountReferal,
                (SELECT COUNT(Id) FROM `PlayerLogs`     WHERE `PlayerId` = `Players`.`Id`) CountLog,
                (SELECT COUNT(Id) FROM `ShopOrders`     WHERE `PlayerId` = `Players`.`Id`) CountShopOrder,
                (SELECT COUNT(Id) FROM `MoneyOrders`    WHERE `PlayerId` = `Players`.`Id` AND `Type`!='points') CountMoneyOrder,
                (SELECT COUNT(Id) FROM `PlayerReviews`  WHERE `PlayerId` = `Players`.`Id` ) CountReview,
                (SELECT COUNT(Id) FROM `LotteryTickets` WHERE `LotteryId` = 0 AND `PlayerId` = `Players`.`Id`) AS TicketsFilled
                FROM `Players`
                    LEFT JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId=`Players`.Id
                    INNER JOIN `PlayerCookies`c ON `PlayerCookies`.CookieId=c.CookieId
                {$search}
                GROUP BY `Players`.Id";

        $sql = "SELECT `Players`.*,
                (SELECT COUNT(Id) FROM `Players` p WHERE (p.LastIp=`Players` . `LastIp` AND p.LastIp!='') OR (p.Ip=`Players` . `LastIp` AND p.Ip!='') OR (p.LastIp=`Players` . `Ip` AND p.LastIp!='') OR (p.Ip=`Players` . `Ip` AND p.Ip!='')) AS CountIp,
                COUNT(DISTINCT (PlayerNotes .Id)) CountNote,
                COUNT(DISTINCT (PlayerNotices.Id)) CountNotice,
                COUNT(DISTINCT (`PlayerLogs`.`Id`)) CountLog,
                COUNT(DISTINCT (`ShopOrders`.`Id`)) CountShopOrder,
                COUNT(DISTINCT (`MoneyOrders`.`Id`)) CountMoneyOrder,
                SUM(IF(PlayerNotices .Type='AdBlock',1,0)) CountAdBlock,
                (SELECT COUNT(Id) FROM `PlayerReviews` WHERE `PlayerId` = `Players`.`Id` ) AS CountReview,
                (SELECT COUNT(Id) FROM `LotteryTickets` WHERE `LotteryId` = 0 AND `PlayerId` = `Players`.`Id`) AS TicketsFilled
                FROM `Players`
                LEFT JOIN `PlayerNotices` ON `PlayerNotices` . `PlayerId` = `Players`.`Id`
                LEFT JOIN `PlayerNotes` ON `PlayerNotes` . `PlayerId` = `Players`.`Id`
                LEFT JOIN `PlayerLogs` ON `PlayerLogs` . `PlayerId` = `Players`.`Id`
                LEFT JOIN `MoneyOrders` ON `MoneyOrders` . `PlayerId` = `Players`.`Id`
                LEFT JOIN `ShopOrders` ON `ShopOrders` . `PlayerId` = `Players`.`Id`
                LEFT JOIN `PlayerDates` ON `PlayerDates` . `PlayerId`=`Players`.`Id`
                {$search}
                GROUP BY `Players`.`Id`";


        $sql = "SELECT Id From Players
                Left join PlayerDates ON PlayerDates.PlayerId=Id
                {$search}
                GROUP BY Id";

        if (count($sort)) {
            if (in_array(strtolower($sort['direction']), array('asc', 'desc'))) {
                $sql .= ' ORDER BY `' . $sort['field'] . '` ' . $sort['direction'];
            }

        }

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        if ($offset) {
            $sql .= ' OFFSET ' . (int)$offset;
        }

        $sql ="SELECT GROUP_CONCAT(Id) FROM ({$sql}) p";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }
        $ids = $res->fetchColumn(0)?:0;


        $sql = "SELECT `Players`.*,`PlayerDates`.*,

                (SELECT COUNT(Id) FROM `PlayerNotes`    WHERE `PlayerId` = `Players`.`Id`) Note,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id`) Notice,
                (SELECT COUNT(Id) FROM `PlayerNotices`  WHERE `PlayerId` = `Players`.`Id` AND Type='AdBlock') AdBlock,
                (SELECT COUNT(DISTINCT(i.PlayerId))) CounterIp,
                (SELECT COUNT(DISTINCT(c.PlayerId))) CounterCookieId,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`Id`) MyInviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`InviterId` = `Players`.`InviterId` AND p.`InviterId`>0) Inviter,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`Id`) MyReferal,
                (SELECT COUNT(Id) FROM `Players` p      WHERE  p.`ReferalId` = `Players`.`ReferalId` AND p.`ReferalId`>0) Referal,
                (SELECT COUNT(Id) FROM `PlayerLogs`     WHERE `PlayerId` = `Players`.`Id`) Log,
                (SELECT COUNT(Id) FROM `ShopOrders`     WHERE `PlayerId` = `Players`.`Id`) ShopOrder,
                (SELECT COUNT(Id) FROM `MoneyOrders`    WHERE `PlayerId` = `Players`.`Id` AND `Type`!='points') MoneyOrder,
                (SELECT COUNT(Id) FROM `PlayerReviews`  WHERE `PlayerId` = `Players`.`Id` ) Review,
                (SELECT count(*)  FROM `Players` p      WHERE p.`Phone` = `Players`.`Phone` OR p.Qiwi=`Players`.Qiwi OR `p`.WebMoney = `Players`.WebMoney OR `p`.YandexMoney= `Players`.YandexMoney) as Mult,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=1 AND Price>0) WhoMore,
                (SELECT AVG(Win)  FROM PlayerGames      WHERE PlayerId=`Players`.`Id` AND GameId=2 AND Price>0) SeaBattle,
                (SELECT COUNT(Id) FROM `LotteryTickets` WHERE `LotteryId` = 0 AND `PlayerId` = `Players`.`Id`) AS TicketsFilled
                FROM `Players`
                LEFT JOIN `PlayerDates` ON `PlayerDates` . `PlayerId`=`Id`
                LEFT JOIN `PlayerIps` ON `PlayerIps`.PlayerId =  `Players`.Id
                LEFT JOIN `PlayerIps` i ON i.Ip IN (`PlayerIps`.Ip)
                LEFT JOIN `PlayerCookies` ON `PlayerCookies`.PlayerId = Id
                LEFT JOIN `PlayerCookies` c ON c.CookieId IN (`PlayerCookies`.CookieId)
                WHERE Id IN ({$ids})
                GROUP BY `Players`.`Id`";


        if (count($sort)) {
            if (in_array(strtolower($sort['direction']), array('asc', 'desc'))) {
                $sql .= ' ORDER BY `' . $sort['field'] . '` ' . $sort['direction'];
            }

        }

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        $players = array();
        foreach ($res->fetchAll() as $playerData) {
            $player = new Player();
            $player->formatFrom('DB', $playerData);

            $players[] = $player;
        }

        return $players;
    }

    public function getMults($playerId)
    {
        $sql = "SELECT p.Id, p.Nicname, p.Phone, p.Qiwi, p.WebMoney, p.YandexMoney
                FROM `Players`
                LEFT JOIN `Players` p ON p.`Phone` = `Players`.`Phone` OR p.Qiwi=`Players`.Qiwi OR `p`.WebMoney = `Players`.WebMoney OR `p`.YandexMoney= `Players`.YandexMoney
                WHERE `Players`.Id = :pid
                group by p.Id";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute(array(
                ':pid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $mults = array();
        foreach ($res->fetchAll() as $mult) {
            $mults[] = $mult;
        }

        return $mults;
    }

    public function getLogins($playerId)
    {
        $sql = "SELECT * FROM `PlayerLogins` WHERE `PlayerId` = :pid ORDER BY `Id` DESC";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute(array(
                ':pid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $logins = array();
        foreach ($res->fetchAll() as $loginData) {
            $loginData['Date']=date('d.m.Y H:i:s', $loginData['Date']);
            $logins[] = $loginData;
        }

        return $logins;
    }

    public function getReviews($playerId)
    {
        $sql = "SELECT * FROM `PlayerReviews` WHERE `PlayerId` = :pid ORDER BY `Id` DESC";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute(array(
                ':pid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $reviews = array();
        foreach ($res->fetchAll() as $reviewData) {
            $reviewData['Date']=date('d.m.Y H:i:s', $reviewData['Date']);
            $reviews[] = $reviewData;
        }

        return $reviews;
    }

    public function getTickets($playerId)
    {
        $sql = "SELECT `Lotteries`.`Date`, `Lotteries`.`Combination` WinCombination,
              `LotteryTicketsArchive`.`TicketWinCurrency`, `LotteryTicketsArchive`.`TicketWin`, `LotteryTicketsArchive`.`TicketWin`,
              `LotteryTicketsArchive`.`TicketNum`, `LotteryTicketsArchive`.`Combination`, `LotteryTicketsArchive`.`PlayerId`,
              `LotteryTicketsArchive`.`LotteryId`, `LotteryTicketsArchive`.`Id`, `LotteryTicketsArchive`.`DateCreated`
              FROM `LotteryTicketsArchive`
              LEFT JOIN `Lotteries` ON `LotteryTickets`.`LotteryId`=`Lotteries`.`Id`
              WHERE `PlayerId` = :pid ORDER BY `Id` DESC";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute(array(
                ':pid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $tickets = array();
        foreach ($res->fetchAll() as $ticketData) {
            $ticketData['DateCreated']=date('d.m.Y H:i:s', $ticketData['DateCreated']);
            $ticketData['Date']=date('d.m', $ticketData['Date']);
            $ticketData['Combination']=unserialize($ticketData['Combination']);
            $ticketData['WinCombination']=unserialize($ticketData['WinCombination']);
            $tickets[] = $ticketData;
        }

        return $tickets;
    }

    public function getLog($playerId, $action)
    {
        $sql = "SELECT * FROM `PlayerLogs` WHERE `PlayerId` = :pid ".($action?'AND Action=:action ':'')."ORDER BY `Id` DESC";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute($action?array(':pid' => $playerId,':action' => $action):array(':pid' => $playerId)
            );
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $logs = array();
        foreach ($res->fetchAll() as $logData) {
            $logData['Date']=date('d.m.Y H:i:s', $logData['Time']);
            $logs[] = $logData;
        }

        return $logs;
    }

    public function checkNickname(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `Nicname` = :nic AND `Id` != :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':nic'  => $player->getNicname(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('NICKNAME_BUSY', 403);
        }

        return true;
    }

    public function countMults (Entity $player)
    {
        $sql = "SELECT count(*) FROM `Players` WHERE (`Phone` = :p OR Qiwi=:q OR WebMoney = :w OR YandexMoney= :y) AND `Id` != :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':p'  => $player->getPhone(),
                ':q'  => $player->getQiwi(),
                ':w'  => $player->getWebMoney(),
                ':y'  => $player->getYandexMoney(),
                ':id' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('PHONE_BUSY', 403);
        }

        return true;
    }

    public function checkPhone(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `Phone` = :uid AND `Id` != :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid'  => $player->getPhone(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('PHONE_BUSY', 403);
        }

        return true;
    }

    public function checkQiwi(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `Qiwi` = :uid AND `Id` != :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid'  => $player->getQiwi(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('QIWI_BUSY', 403);
        }

        return true;
    }

    public function checkWebMoney(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `WebMoney` = :uid AND `Id` != :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid'  => $player->getWebMoney(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('WEBMONEY_BUSY', 403);
        }

        return true;
    }

    public function checkYandexMoney(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `YandexMoney` = :uid AND `Id` != :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid'  => $player->getYandexMoney(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if ($sth->rowCount()) {
            throw new ModelException('YANDEXMONEY_BUSY', 403);
        }

        return true;
    }

    public function saveAvatar(Entity $player)
    {
        $sql = "UPDATE `Players` SET `Avatar` = :av WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':av'  => $player->getAvatar(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return true;
    }

    public function changePassword(Entity $player)
    {
        $sql = "UPDATE `Players` SET `Password` = :pw, `Salt` = :salt WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pw'  => $player->getPassword(),
                ':salt'  => $player->getSalt(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function getInvitesCount(Entity $player)
    {
        $sql = "SELECT COUNT(*) FROM `EmailInvites` WHERE `InviterId` = :plid AND `Date` > :date";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid' => $player->getId(),
                ':date' => strtotime('-7 days')
            ));

            return $sth->fetchColumn();

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
    }

    /**
     * Сохранение в базу массива счётчиков остатка оплачиваемых реф.постов в соц.сетях
     *
     * @author subsan <subsan@online.ua>
     *
     * @param  object $player
     * @return object
     */
    public function decrementSocialPostsCount(Entity $player)
    {
        $sql = "UPDATE `Players` SET `SocialPostsCount` = :ic WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':ic'   => is_array($player->getSocialPostsCount()) ? serialize($player->getSocialPostsCount()) : '',
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function updateCookieId(Entity $player, $cookie)
    {
        $sql = "REPLACE INTO `PlayerCookies` (`PlayerId`, `CookieId`, `Time`) VALUES (:id, :cookie, :tm)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'       => $player->getId(),
                ':cookie'     => $cookie,
                ':tm'       => time()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;
    }

    public function updateIp(Entity $player, $ip) {

        $sql = "REPLACE INTO `PlayerIps` (`PlayerId`,`Ip`,`Time`) VALUES (:plid,:ip,:tm)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid'  => $player->getId(),
                ':ip'  => $ip,
                ':tm'  => time(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function checkDate($key, Entity $player)
    {
        $sql = "UPDATE `PlayerDates` SET `{$key}` = :date WHERE `PlayerId` = :id AND `{$key}` < :min";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':date'  => time(),
                ':min'  => time() - \SettingsModel::instance()->getSettings('counters')->getValue($key),
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->rowCount();
    }

    public function checkLastGame($key, Entity $player)
    {
        $sql = "UPDATE `PlayerDates` SET `{$key}` = :date WHERE `PlayerId` = :id AND `{$key}` < :min";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':date'  => time(),
                ':min'  => time() - GameSettingsModel::instance()->getSettings($key)->getOption('min')*60,
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->rowCount();
    }

    public function updateLogin(Entity $player)
    {
        $sql = "UPDATE `PlayerDates` SET `Login` = :date WHERE  `PlayerId` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':date'  => $player->getDates('Login'),
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function updateNotice(Entity $player)
    {

        $sql = "UPDATE `PlayerDates` SET `Notice` = :date WHERE  `PlayerId` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':date'  => $player->getDates('Notice'),
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function markOnline(Entity $player)
    {

        $sql = "UPDATE `PlayerDates`
                SET `AdBlockLast` = :adbl,
                    `AdBlocked` = :adb,
                    `WSocket` = :ws,
                    `Ping` = :onl
                WHERE `PlayerId` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':onl'   => (int)$player->getDates('Ping'),
                ':adbl'  => (int)$player->getAdBlock(),
                ':adb'   => (int)$player->getDateAdBlocked(),
                ':ws'    => ($player->getWebSocket()?time():0),
                ':plid'  => $player->getId(),
            ));

        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function validateHash($hash)
    {
        $sql = "UPDATE `Players` SET `Valid` = 1 WHERE `Hash` = :hash";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':hash'  => $hash,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

    }

    public function updateInvite(Entity $player) {
        $sql = "UPDATE `Players` SET `ReferalPaid` = :rfpd, `InviterId` = :invid WHERE `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':rfpd'  => $player->isReferalPaid(),
                ':invid'  => $player->getInviterId(),
                ':plid'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function markReferalPaid(Entity $player) {
        $sql = "UPDATE `Players` SET `ReferalPaid` = 1 WHERE `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }
}
