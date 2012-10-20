MySQL Quick Database and User Creator
=====================================

This is just a simple PHP CLI script that, in just a few seconds, creates a new
MySQL database, creates a new user, and grants the user full access to the
database. It also uses the API from passwd.me to generate a random password for
the new user.

To use the script:

1. Copy the config-sample.php file to config.php and replace the
   host, username, and password values with values that match your MySQL 
   installation. You need root or another user with authority to create 
   users and databases.

2. Run this file in the CLI, and enter the database name and the user name
   you want.

       `> php mysql-quick-create.php`
