<?php

include('MySqlBackup.php');

$arrayDbConf['host'] = 'localhost';
$arrayDbConf['user'] = 'root';
$arrayDbConf['pass'] = '';
$arrayDbConf['name'] = 'looneytunes';


try {

  $bck = new MySqlBackupLite($arrayDbConf);
  $bck->backUp();
  $bck->downloadFile();

}
catch(Exception $e) {

  echo $e;

}

?>
