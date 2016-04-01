call addCol('Players','Complete','INT(1) DEFAULT 0 NOT NULL AFTER `Valid`');
UPDATE Players SET Complete = 1 WHERE 1;