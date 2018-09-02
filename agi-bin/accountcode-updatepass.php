#!/usr/bin/env php
<?php
/*
 * followed_pound - seguido pela tecla sustenido.
 * enter_account - Digite seu número de conta.
 * enter-password  - Por favor, digite a sua senha, seguido pela tecla sustenido.
 */

include_once "phpagi.php";
include_once "sql.php";

$agi = new AGI();
$db = new AGIDB($agi);

$code = $argv[1];
$pass = $argv[2];

$sql = "SELECT `pass` FROM `accountcode` WHERE `code` = '$code' AND `active` = '1'";
$row = $db->sql($sql, 'NUM');

if (password_verify($pass, $row[0][0])) {
    $agi->exec('Read', 'PASSWD,enter-password,4,,2,4');
    $value = $agi->get_variable('PASSWD');
    $value = $value['data'];
    $pass = password_hash($value,PASSWORD_DEFAULT);
    $sql = "UPDATE `accountcode` SET `pass` = '$pass' WHERE `code` = '$code'";
    $db->sql($sql, 'NUM');
    $agi->exec('Playback', 'auth-thankyou');
} else {
    $agi->exec('Playback', 'vm-invalidpassword');
    $agi->set_variable('HANGUPCAUSE', '21');
    $agi->hangup();
}