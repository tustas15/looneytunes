<?php

include('MySqlBackup.php');

$arrayDbConf['host'] = 'localhost';
$arrayDbConf['user'] = 'root';
$arrayDbConf['pass'] = '';
$arrayDbConf['name'] = 'looneytunes';


try {

  $bck = new MySqlBackupLite($arrayDbConf);
  $bck->backUp();
  $bck->setFileDir('./backups/');
  $bck->setFileName('backupFileNae.sql');
  $bck->saveToFile();

}
catch(Exception $e) {

  echo $e;

}

?>
