<?php

namespace App\Controllers;

use App\View;
use App\Models\Dbh;
use PDO;

class UsersController
{
    public function index(): View
    {
        // get information from database
        // create array with Article objects
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article');
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

//        echo "<pre>";
//        print_r($res);

        return new View('Users/index.html', [
            'articles' => $res
        ]);
    }

    public function show(array $vars): View
    {
        // $vars['id'];
        // get information from database where article ID = $vars['id']
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$vars['id']]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

//        echo "<pre>";
//        var_dump($res[0]);

        return new View('Users/show.html', [
            'id' => $res[0]['id'],
            'title' => $res[0]['title'],
            'descriptions' => $res[0]['descriptions']
        ]);
    }
}