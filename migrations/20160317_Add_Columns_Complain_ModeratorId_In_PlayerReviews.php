call addCol('PlayerReviews','Complain','VARCHAR(16) NULL AFTER `Status`');
call addCol('PlayerReviews','ModeratorId','INT(11) UNSIGNED NOT NULL DEFAULT "0" AFTER `Complain`');
