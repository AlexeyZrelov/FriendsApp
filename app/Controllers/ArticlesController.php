<?php

namespace App\Controllers;

use App\Models\Article;
use App\Redirect;
use App\View;
use App\Models\Dbh;
use PDO;

class ArticlesController
{

    public function index(): View
    {
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article');
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articles = [];
        foreach ($res as $item) {
            $articles[] = new Article($item['title'], $item['descriptions'], $item['id']);
        }

        return new View('Articles/index.html', [
            'articles' => $articles
        ]);
    }

    public function show(array $vars): View
    {
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$vars['id']]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $article = new Article($res[0]['title'], $res[0]['descriptions'], $res[0]['id']);

        return new View('Articles/welcome.html', [

            'article' => $article

        ]);
    }

    public function create(): View
    {
        return new View('Articles/create.html');
    }

    public function store(): Redirect
    {
        // Validate form
        $stmt = (new Dbh())->connect()->prepare('INSERT INTO article (title, descriptions) VALUES (?, ?)');
        $stmt->execute([$_POST['title'], $_POST['description']]);

        return new Redirect('/articles');
    }

    public function delete(array $vars): Redirect
    {
        $stmt = (new Dbh())->connect()->prepare('DELETE FROM article WHERE id=?');
        $stmt->execute([$vars['id']]);

        return new Redirect('/articles');
    }

    public function edit(array $vars): View
    {
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute([$vars['id']]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $article = new Article($res[0]['title'], $res[0]['descriptions'], $res[0]['id']);

        return new View('Articles/edit.html', [

            'article' => $article

        ]);

    }

    public function update(array $vars): Redirect
    {

        $stmt = (new Dbh())->connect()->prepare('UPDATE article SET title=?, descriptions=? WHERE id=?');
        $stmt->execute([$_POST['title'], $_POST['description'], $vars['id']]);

        return new Redirect('/articles/' . $vars['id'] . '/edit');
    }

}