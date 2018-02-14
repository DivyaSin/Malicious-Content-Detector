<?php
/**
 * Created by PhpStorm.
 * User: divyasingh
 * Date: 12/3/17
 * Time: 4:07 PM
 */

    // create a database MaliciousDb
    // create a table Users in it containing usernames and passwords
    $conn = new mysqli('localhost', 'root', '');
    if ($conn->connect_error) die($conn->connect_error);
    else {
        $query[] = "CREATE DATABASE IF NOT EXISTS MaliciousDb;";
        
        //use Database MaliciousDb
        $query[] = "use MaliciousDb";
        $query[] = "DROP TABLE IF EXISTS Users;";

        // create table and store usernames and hashed passwords, admin (0 or 1) in database
        $query[] = "CREATE TABLE Users(username varchar(20) NOT NULL, password varchar(1200) NOT NULL,
                    ID int(11) NOT NULL AUTO_INCREMENT, admin int(2) NOT NULL, PRIMARY KEY (ID)) ;";

        // insert an admin into table Users in secured way (by hashing passwords)
        $username = "admin";
        $password = "Pass4admin";
        $salt1 = "qm&h*";
        $salt2 = "pg!@";
        $token = hash('ripemd128', "$salt1$password$salt2");
        $query[] = "INSERT INTO Users(username, password, admin) VALUES('$username', '$token', '1');";

        // insert a user (not admin) into table Users
        $username1 = "username";
        $password1 = "Pass4user";
        $token1 = hash('ripemd128', "$salt1$password1$salt2");
        $query[] = "INSERT INTO Users(username, password, admin) VALUES('$username1', '$token1', '0');";

        // create a table of malware
        $query[] = "DROP TABLE IF EXISTS Malware;";
        $query[] = "CREATE TABLE Malware(name varchar(20) NOT NULL, bytesequence varchar(120) NOT NULL) ;";

    }
    foreach($query as $que){
        //echo "in for";
        if($conn->query($que)) {
            echo "Query successfully executed";
            echo "<br>";
        }
        else{
            echo "Query cannot be executed: ".$conn->error;
            echo "<br>";
            echo "<br>";
        }
    }
?>