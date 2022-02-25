<?php

namespace App\Models;

use PDO;

class Dbh
{
    public function connect()
    {
        try {
            $username = "root";
            $password = "root1234";
            $dbh = new PDO('mysql:host=localhost;dbname=FriendsApp', $username, $password);
            return $dbh;
        } catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}