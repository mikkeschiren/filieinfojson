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
  'name1' => '/backup/mysite/mysql',
  'name2' => '/backup/othersite/db',
);

// This I dont want to list
$dontlist = array(
  'performance_schema.sql',
  'all_database.sql',
  'mysql.sql',
  'information_schema.sql',
  'test.sql',
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
          $touched = date('Y-m-d H:m', $file->getMTime());
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
