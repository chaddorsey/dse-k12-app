<?php

$instance_host = getenv('INSTANCE_HOST');
$db_port = getenv('DB_PORT');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_root_cert = getenv('DB_ROOT_CERT');
$db_cert = getenv('DB_CERT');
$db_key = getenv('DB_KEY');
$private_ip = getenv('PRIVATE_IP');

if ($private_ip === 'TRUE') {
  $dbh = mysqli_init();
  $dbh->ssl_set(
      $db_key, // The path name to the key file
      $db_cert, // The path name to the certificate file
      $db_root_cert, // The path name to the certificate authority file
      NULL, // The pathname to a directory that contains trusted SSL CA certificates in PEM format
      NULL  // A list of allowable ciphers to use for SSL encryption
  );
  $success = $dbh->real_connect($instance_host, $db_user, $db_pass, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL);
  
  if (!$success) {
      die('Connection failed: ' . $dbh->connect_error);
  }
} else {
  // Fallback to non-SSL connection if PRIVATE_IP is not 'TRUE'
  $dbh = new mysqli($instance_host, $db_user, $db_pass, $db_name, $db_port);
  
  if ($dbh->connect_error) {
      die('Connection failed: ' . $dbh->connect_error);
  }
}

?>
