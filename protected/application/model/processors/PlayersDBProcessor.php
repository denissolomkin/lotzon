<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class PlayersDBProcessor implements IProcessor
{
    public function create(Entity $player)
    {
        $sql = "INSERT INTO `Players` (`Email`, `Password`, `Salt`, `DateRegistered`, `DateLogined`, `Country`, `Visible`, `Ip`, `Hash`, `Valid`, `Name`, `Surname`, `AdditionalData`, `ReferalId`) 
                VALUES (:email, :passwd, :salt, :dr, :dl, :cc, :vis, :ip, :hash, :valid, :name, :surname, :ad, :rid)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':email'    => $player->getEmail(),
                ':passwd'   => $player->getPassword(),
                ':salt'     => $player->getSalt(),
                ':dr'       => time(),
                ':dl'       => time(),
                ':cc'       => $player->getCountry(),
                ':vis'      => 1,
                ':ip'       => $player->getIP(),
                ':hash'     => $player->getHash(),
                ':valid'    => $player->getValid(),
                ':name'     => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':ad'       => is_array($player->getAdditionalData()) ? serialize($player->getAdditionalData()) : '',
                ':rid'      => $player->getReferalId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $player->setId(DB::Connect()->lastInsertId());

        try {
            DB::Connect()->prepare("UPDATE `Players` SET `NicName` = CONCAT('Участник ', `Id`) WHERE `Id` = :id")->execute(array(
                ':id' => $player->getId(),
            ));
            $player->setNicname('Участник ' . $player->getId());
        } catch (PDOException $e){}

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


    }

    public function existsSocial(Entity $player)
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

    public function update(Entity $player)
    {
        $sql = "UPDATE `Players` SET
                    `DateLogined` = :dl, `Country` = :cc, 
                    `Nicname` = :nic, `Name` = :name, `Surname` = :surname, `SecondName` = :secname, 
                    `Phone` = :phone, `Birthday` = :bd, `Avatar` = :avatar, `Visible` = :vis, `Favorite` = :fav,
                    `Money` = :money, `Points`  = :points, `GamesPlayed` = :gp, `AdditionalData` = :ad
                WHERE `Id` = :id OR `Email` = :email";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':dl'       => $player->getDateLastLogin(),
                ':cc'       => $player->getCountry(),
                ':nic'      => $player->getNicname(),
                ':name'     => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':secname'  => $player->getSecondName(),
                ':phone'    => $player->getPhone(),
                ':bd'       => $player->getBirthday(),
                ':avatar'   => $player->getAvatar(),
                ':id'       => $player->getId(),
                ':email'    => $player->getEmail(),
                ':vis'      => (int)$player->getVisibility(),
                ':fav'      => is_array($player->getFavoriteCombination()) ? serialize($player->getFavoriteCombination()) : '',
                ':money'    => $player->getMoney(),
                ':points'   => $player->getPoints(),
                ':gp'       => $player->getGamesPlayed(),
                ':ad'       => is_array($player->getAdditionalData()) ? serialize($player->getAdditionalData()) : ''
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);   
        }

        return $player;
    }

    public function fetch(Entity $player)
    {
        $sql = "SELECT p.* FROM `Players` p
                LEFT JOIN `PlayerSocials` s
                  ON s.`PlayerId`=p.`Id`
                WHERE p.`Id` = :id OR p.`Email` = :email
                  OR (s.`SocialId` = :socialid AND s.`SocialName` = :socialname AND s.`Enabled` = 1)
                  OR (s.`SocialEmail` = :socialemail AND s.`SocialName` = :socialname AND s.`SocialEmail` IS NOT NULL AND s.`Enabled` = 1)
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
        return true;
    }

    public function getPlayersCount($search=null)
    {
        $sql = "SELECT COUNT(*) as `counter`  FROM `Players`";

        if (is_array($search) AND $search['query']) {
            if($search['where'] AND $search['where']=='Id')
                $sql .= ' WHERE Id = '.$search['query'];
            elseif($search['where'])
                $sql .= ' WHERE '.$search['where'].' LIKE "%'.$search['query'].'%"';
            else
                $sql .= ' WHERE '.(is_numeric($search['query'])?'`Id`='.$search['query'].' OR ':'').'CONCAT(`Surname`, `Name`) LIKE "%'.$search['query'].'%" OR `NicName` LIKE "%'.$search['query'].'%" OR `Email` LIKE "%' . $search['query'].'%"';
        }

        try {
            $res = DB::Connect()->query($sql);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $res->fetchColumn(0);
    }

    public function getList($limit = 0, $offset = 0, array $sort, $search=null)
    {
        $sql = "SELECT *, (SELECT count(p.Id ) FROM `Players` p WHERE p. `Ip` =`Players` . `Ip` ) AS CountIp, (SELECT 1 FROM `LotteryTickets` WHERE `LotteryId` = 0 AND `PlayerId` = `Players`.`Id` LIMIT 1) AS TicketsFilled FROM `Players`";

        if (is_array($search) AND $search['query']) {
            if($search['where'] AND $search['where']=='Id')
                $sql .= ' WHERE Id = '.$search['query'];
            elseif($search['where'])
                $sql .= ' WHERE '.$search['where'].' LIKE "%'.$search['query'].'%"';
            else
                $sql .= ' WHERE '.(is_numeric($search['query'])?'`Id`='.$search['query'].' OR ':'').'CONCAT(`Surname`, `Name`) LIKE "%'.$search['query'].'%" OR `NicName` LIKE "%'.$search['query'].'%" OR `Email` LIKE "%' . $search['query'].'%"';
        }

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

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
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

    public function decrementInvitesCount(Entity $player)
    {
        $sql = "UPDATE `Players` SET `InvitesCount` = :ic WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':ic'  => $player->getInvitesCount(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);   
        }

        return $player;   
    }

    public function decrementSocialPostsCount(Entity $player)
    {
        $sql = "UPDATE `Players` SET `SocialPostsCount` = :ic WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':ic'  => $player->getSocialPostsCount(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);   
        }

        return $player;   
    }

    public function updateLastNotice(Entity $player)
    {
        $sql = "UPDATE `Players` SET `DateNoticed` = :date WHERE  `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':date'  => $player->getDateLastNotice(),
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;
    }

    public function markOnline(Entity $player)
    {
        $sql = "UPDATE `Players` SET `Online` = :onl, `OnlineTime` = :onlt WHERE  `Id` = :plid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':onl'  => (int)$player->isOnline(),
                ':onlt'  =>  (int)$player->getOnlineTime(),
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
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