call addCol('Players','City','VARCHAR(255) NOT NULL AFTER `Country`');
CALL addCol('Players','Zip','CHAR(5) NOT NULL AFTER `City`');
call addCol('Players','Address','VARCHAR(255) NOT NULL AFTER `Zip`');
