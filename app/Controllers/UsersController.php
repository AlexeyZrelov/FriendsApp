<?php

namespace App\Controllers;

use App\Models\Article;
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

        $articles = [];
        foreach ($res as $item) {
            $articles[] = new Article($item['title'], $item['descriptions'], $item['id']);
        }

        return new View('Users/index.html', [
//            'articles' => $res
            'articles' => $articles
        ]);
    }

    public function show(array $vars): View
    {
        // $vars['id'];
        // get information from database where article ID = $vars['id']
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$vars['id']]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $article = new Article($res[0]['title'], $res[0]['descriptions'], $res[0]['id']);

        return new View('Users/show.html', [
//            'id' => $res[0]['id'],
//            'title' => $res[0]['title'],
//            'descriptions' => $res[0]['descriptions']

            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'descriptions' => $article->getDescription()

        ]);
    }
}