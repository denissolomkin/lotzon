<?php

require_once('init.php');

/**
 * Avatars
 */
$sql = "SELECT Id, Avatar FROM `Players`";
$sth = DB::Connect()->prepare($sql);
$sth->execute();
$avatars = $sth->fetchAll();

foreach ($avatars as $avatar) {
    $playerId  = $avatar['Id'];
    $avatarSrc = $avatar['Avatar'];
    $oldFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($playerId / 100)) . '/';
    if (!file_exists(PATH_FILESTORAGE.'users/50/'.$avatarSrc)) {
        if (file_exists($oldFolder.$avatarSrc)) {
            \Common::saveImageMultiResolution('', PATH_FILESTORAGE . 'users/', $avatarSrc, array(array(50, 'crop'), array(100, 'crop'), array(200, 'crop')), $oldFolder . $avatarSrc);
        }
    }
}

/**
 * Reviews
 */
$sql = "SELECT Image FROM `PlayerReviews`";
$sth = DB::Connect()->prepare($sql);
$sth->execute();
$reviews = $sth->fetchAll();

foreach ($reviews as $review) {
    $imgSrc = $review['Image'];
    $folder = PATH_FILESTORAGE . 'reviews/';
    if (!file_exists($folder.'600/'.$imgSrc)) {
        if (file_exists($folder . $imgSrc)) {
            \Common::saveImageMultiResolution('', $folder, $imgSrc, array(array(600)), $folder . $imgSrc);
        }
    }
}