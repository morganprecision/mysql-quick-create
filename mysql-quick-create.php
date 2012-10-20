<?php
/**
 * Script for quickly generating a new MySQL database and a new user
 *
 * How to use:
 *
 * 1. Copy the config-sample.php file to config.php and replace the
 *    host, username, and password values with values that match your MySQL 
 *    installation. You need root or another user with authority to create 
 *    users and databases.
 *
 * 2. Run this file in the CLI, and enter the database name and the user name
 *    you want.
 *
 *    > php mysql-quick-create.php
 *
 * @author Eugene Morgan <em@morganprecision.com>
 */

// Require config file for MySQL variables
require dirname(__FILE__) . '/config.php';

// Options for password generator
// See passwd.me/developer for options
define('PWD_CHARSET', 'MIXEDCASEALPHANUMERIC');
define('PWD_LENGTH', 24);
define('PWD_TYPE', 'random');


// ----------------------------------------------------------------------------
// Prompt user for database and username to create
// ----------------------------------------------------------------------------
echo 'Name of database to create? ';
$dbname = trim(fgets(STDIN));

echo 'Username to be created? ';
$username = trim(fgets(STDIN));


// ----------------------------------------------------------------------------
// Generate a random password
// ----------------------------------------------------------------------------
echo "Generating a random password ...\n";

// Build URL for REST call
$url = 'https://passwd.me/api/1.0/get_password.json'
    . '?type=' . PWD_TYPE
    . '&length=' . PWD_LENGTH
    . '&charset=' . PWD_CHARSET;

// Use CURL to get password from passwd.me API
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
$result = curl_exec($ch);
$decoded = json_decode($result);
$password = $decoded->password;


// ----------------------------------------------------------------------------
// Create database and user, and grant permission
// ----------------------------------------------------------------------------
echo "Creating database ...\n";

// Connect to database
try {
    $pdo = new PDO('mysql:host=' . DB_HOST, DB_MASTER_USER, DB_MASTER_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error connecting to database: ' . $e->getMessage());
}

$sql = 'CREATE DATABASE ' . $dbname
     . ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci';
db_exec($pdo, $sql);


// ----------------------------------------------------------------------------
echo "Creating user and granting permission ...\n";

$sql = "CREATE USER '" . $username . "'@'localhost' "
     . "IDENTIFIED BY '" . $password . "'";
db_exec($pdo, $sql);

$sql = "GRANT ALL ON " . $dbname . ".* TO '" . $username . "'@'localhost'";
db_exec($pdo, $sql);


// ----------------------------------------------------------------------------
echo "Database and user created successfully!\n",
     "Database: {$dbname}\n",
     "Username: {$username}\n",
     "Password: {$password}\n";

/**
 * Function for simple database query
 *
 * @param PDO $pdo PDO object
 * @param string $sql The SQL to execute
 */
function db_exec($pdo, $sql)
{
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}
