<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body style="margin:0;padding:0;">
    <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
        <tr>
            <td height="70" style="padding-left:40px;" bgcolor="#ffe400">
                <img src="http://lotzon.com/tpl/img/mail-logo.png" width="123" height="23" alt="">
            </td>
        </tr>
        <tr>
            <td height="40"></td>
        </tr>
        <tr>
            <td style="padding-left:40px;padding-right:40px;font: 12px/17px Arial;color:#000;">
                <p style="margin:0 0 20px 0;">Привет!</p>
                <p style="margin:0 0 20px 0;">Присоеденяйся ко мне и давай вместе играть и выигрывать деньги и призы. Посмотрим кто первый сорвет Jackpot!</p>
            </td>
        </tr>
        <tr>
            <td style="padding-left:40px;"><a href="http://lotzon.com/?ivh=<?=$data['ivh']?>" style="display:inline-block;padding:0 28px;background-color:#ffe400;color:#000;text-decoration:none;font:12px/40px Arial;white-space:nowrap;">Принять приглашение</a></td>
        </tr>
        <tr>
            <td height="200px"></td>
        </tr>
        <tr>
            <td style="padding-left:40px;font: 12px/17px Arial;color:#000;">
               <? if ($data['inviter']->getName() && $data['inviter']->getSurname()) { ?>
                    <?=$data['inviter']->getName()?> <?=$data['inviter']->getSurname()?>
               <? } else {?>
                    <?=$data['inviter']->getEmail()?>
               <? } ?>
            </td>
        </tr>
        <tr>
            <td height="40"></td>
        </tr>
    </table>
</body>
</html>