call addCol('Players','City','VARCHAR(255) NOT NULL AFTER `Country`');
call addCol('Players','Zip','CHAR(5) DEFAULT '00000' NOT NULL');
call addCol('Players','Address','VARCHAR(255) NOT NULL AFTER `Zip`');
