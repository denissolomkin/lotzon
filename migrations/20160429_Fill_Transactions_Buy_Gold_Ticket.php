SET NAMES 'utf8' COLLATE 'utf8_general_ci';
UPDATE `Transactions` SET ObjectType='Gold' WHERE ObjectType is NULL and Description='Покупка золотого билета';
