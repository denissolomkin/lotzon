drop procedure if exists addCol;
create procedure addCol(IN tableName tinytext,IN fieldName tinytext,IN fieldDef text) begin IF NOT EXISTS ( SELECT * FROM information_schema.COLUMNS WHERE column_name=fieldName and table_name=tableName and table_schema=DATABASE() ) THEN set @ddl=CONCAT('ALTER TABLE ',tableName, ' ADD COLUMN ',fieldName,' ',fieldDef);
prepare stmt from @ddl; 
execute stmt; 
END IF; 
end;
drop procedure if exists dropCol;
create procedure dropCol(IN tableName tinytext,IN fieldName tinytext) begin IF EXISTS ( SELECT * FROM information_schema.COLUMNS WHERE column_name=fieldName and table_name=tableName and table_schema=DATABASE() ) THEN set @ddl=CONCAT('ALTER TABLE ',tableName, ' DROP ',fieldName);
prepare stmt from @ddl; 
execute stmt; 
END IF; 
end;
drop procedure if exists isCol;
create procedure isCol(IN tableName tinytext,IN fieldName tinytext,IN queryTrue text,IN queryFalse text) 
begin 
IF EXISTS ( SELECT * FROM information_schema.COLUMNS WHERE column_name=fieldName and table_name=tableName and table_schema=DATABASE() ) 
THEN 
set @ddl=queryTrue;
prepare stmt from @ddl; 
execute stmt; 
ELSE
set @ddl=queryFalse;
prepare stmt from @ddl; 
execute stmt; 
END IF; 
end;
drop procedure if exists dropTbl;
create procedure dropTbl (IN tableName tinytext) begin IF EXISTS ( SELECT * FROM information_schema.TABLES WHERE table_name=tableName and table_schema=DATABASE() ) THEN set @ddl=CONCAT('DROP TABLE ',tableName);
prepare stmt from @ddl; 
execute stmt; 
END IF; 
end;