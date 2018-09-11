#!/usr/bin/env php
<?php

include_once "phpagi.php";
include_once "sql.php";

$agi = new AGI();
$db = new AGIDB($agi);

$account = $argv[1];
$pass = $argv[2];

$sql = "SELECT `pass` FROM `accountcode` WHERE `account` = '$account' AND `active` = '1'";
$row = $db->sql($sql, 'NUM');

if (password_verify($pass, $row[0][0])) {
    // new password
    $agi->exec('Read', 'PASSWD1,vm-newpassword,4,,2,4');
    $passwd1 = $agi->get_variable('PASSWD1');
    $passwd1 = $passwd1['data'];
    // re enter password
    $agi->exec('Read', 'PASSWD2,vm-reenterpassword,4,,2,4');
    $passwd2 = $agi->get_variable('PASSWD2');
    $passwd2 = $passwd2['data'];
    // password match
    if ($passwd1 == $passwd2) {
        $pass = password_hash($passwd1,PASSWORD_DEFAULT);
        $sql = "UPDATE `accountcode` SET `pass` = '$pass' WHERE `account` = '$account'";
        $db->sql($sql, 'NUM');
        $agi->exec('Playback', 'vm-passchanged');
        $agi->hangup();
    } else {
        $agi->exec('Playback', 'vm-mismatch');
        $agi->set_variable('HANGUPCAUSE', '21');
        $agi->hangup();
    }

} else {
    $agi->exec('Playback', 'vm-invalidpassword');
    $agi->set_variable('HANGUPCAUSE', '21');
    $agi->hangup();
}
