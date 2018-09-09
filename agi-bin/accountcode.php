#!/usr/bin/env php
<?php

include_once "phpagi.php";
include_once "sql.php";

$agi = new AGI();
$db = new AGIDB($agi);

$value = $argv[1];
$value = explode('*', $value);
$account = $value[0];
$pass = $value[1];
$rule = $argv[3];

$sql = "SELECT `pass`, `rules` FROM `accountcode` WHERE `account` = '$account' AND `active` = '1'";
$row = $db->sql($sql, 'NUM');

if (password_verify($pass, $row[0][0])) {
   $rules = explode(',',$row[0][1]);
   if (!in_array($rule, $rules)) {
       $agi->exec('Playback',"access-denied");
       $agi->set_variable('HANGUPCAUSE','21');
       $agi->hangup();
   } else {
       $agi->exec('Playback', 'one-moment-please');
       $agi->set_variable('CHANNEL(accountcode)', $account);
   }
} else {
    $agi->exec('Playback', 'vm-invalidpassword');
    $agi->set_variable('HANGUPCAUSE','21');
    $agi->hangup();
}