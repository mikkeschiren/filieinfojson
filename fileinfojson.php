#!/usr/bin/php
<?php
/**
 * @file
 * Scirpt to create jsonfiles for info about db backups
 */

// Start of configuration.
// Targetdir for where the json files created should be placed
$targetdir = '/srv/www/backupinfo';

// Defined paths for which backups we should have json-files created
$paths = array(
  'lndbsolr01' => '/backup/lndbsolr01/srv',
  'node-host-6' => '/backup/node-host-6/srv',
  'orange1' => '/backup/orange1/srv',
  'orange2' => '/backup/orange2/srv',
  'orange6' => '/backup/orange6/srv',
  'orange7' => '/backup/orange7/srv',
  'orange8' => '/backup/orange8/srv',
  'orange9' => '/backup/orange9/srv',
  'orange11' => '/backup/orange11/srv',
  'orange12' => '/backup/orange12/srv',
  'orange14' => '/backup/orange14/srv',
  'orange17' => '/backup/orange17/srv',
  'orange18' => '/backup/orange18/srv',
  'orange20' => '/backup/orange20/srv',
  'orange21' => '/backup/orange21/srv',
  'pink1' => '/backup/pink1/srv',
  'pink5' => '/backup/pink5/srv',
  'unionen' => '/backup/unionen',
  'vfdb01' => '/backup/vfdb01/srv',
  'vps-56600' => '/backup/vps-56600/mysqldump'
);

// This I dont want to list
$dontlist = array(
  'performance_schema.sql',
  'all_database.sql',
  'mysql.sql',
  'information_schema.sql',
  'test.sql'
);

// End of configuration.

// Got though the array of paths
foreach ($paths as $key => $path) {
  $fileinfo = array();
  $it = new DirectoryIterator($path);
  foreach($it as $file) {
    // Renove . files
    if (!in_array($file, $dontlist)) {
      if (!$it->isDot()) {
        // Check if what is listed is a file, we dont want folders.
        if($file->isFile()) {
          // Get size of file in MB, and round it up
          $filesize = round(($file->getSize() / 1048576) / 2);
          // Get when the filed were touched the last time
          $touched = date('Y-m-d H:m', $file->getATime());
          // Construct an array of info
          $fileinfo[] = array(
            'file' => "$file",
            'filesize' => "$filesize MB",
            'created' =>  "$touched",
          );
        }
      }
    }
  }
  // Create jsonobject
  $jsonobject = array(
    array(
      'servername' => $key,
    ),
      $fileinfo,
  );
  // Now we write json to file.
  $fp = fopen("$targetdir/$key" . '.json', 'w');
  fwrite($fp, json_encode($jsonobject));
  fclose($fp);
  // Unset the arrays.
  unset($fileinfo);
  unset($fp);
}
