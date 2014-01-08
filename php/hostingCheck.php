<!DOCTYPE html>
<html>
<head>
  <title>Checking Hosting</title>
</head>
<?php

/* Script to check hosting */

// We need sqlite PDO
echo '<h2>Following array should contain suitable DB driver (sqlite, mysql, etc.)</h2>';
print_r(PDO::getAvailableDrivers());

?>
</html>