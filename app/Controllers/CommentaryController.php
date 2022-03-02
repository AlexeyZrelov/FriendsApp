<?php

namespace App\Controllers;

use App\Redirect;
use App\Models\Dbh;
use App\View;
use PDO;

class CommentaryController
{
    public function comments(array $vars): View
    {
        // article id
        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE id = ?');
        $stmt->execute(array($vars['id']));
        $article = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // comments
        $stmt1 = (new Dbh())->connect()->prepare('SELECT * FROM comments');
        $stmt1->execute();
        $comment = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        return new View('Comments/main.html', [
            'articles' => $article,
            'uid'      => $_SESSION['log_name'],
            'email'    => $_SESSION['email'],
            'id'       => $vars['id'],
            'comments' => $comment
        ]);
    }

    public function delete(array $vars): Redirect
    {
        $stmt = (new Dbh())->connect()->prepare('DELETE FROM article WHERE id=?');
        $stmt->execute([$vars['id']]);

        return new Redirect('/registration/continue');
    }

    public function create(array $vars): Redirect
    {

//        echo "<pre>";
//        print_r($_POST);die;

        if (!empty($_POST['author']) && !empty($_POST['message'])) {

            $stmt = (new Dbh())->connect()->prepare('INSERT INTO comments (author, message, date, uid) VALUES (?, ?, NOW(), ?)');
            $stmt->execute([$_POST['author'], $_POST['message'], $vars['id']]);

        }

        return new Redirect('/comments/' . $vars['id'] . '/comments');
    }

    public function deletecomment(array $vars): Redirect
    {

//        echo "<pre>";
//        print_r($_POST);die;

        $stmt = (new Dbh())->connect()->prepare('DELETE FROM comments WHERE id=?');
        $stmt->execute([$vars['id']]);

        return new Redirect('/comments/' . $_POST['uid'] . '/comments');
    }

}