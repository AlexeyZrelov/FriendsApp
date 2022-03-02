<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Redirect;
use App\Validation\Errors;
use App\Validation\FormValidator;
use App\View;
use App\Models\Dbh;
use PDO;

class RegistrController
{

    public function index(): View
    {
        return new View('Registration/index.html');
    }

    public function signup()
    {

        $stmt = (new Dbh())->connect()->prepare('INSERT INTO users (uid, email, password, created_at) VALUES (?, ?, ?, NOW())');

        if (!empty($_POST['uid']) && !empty($_POST['email']) && !empty($_POST['pwd'])) {

            $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
            $stmt->execute([$_POST['uid'], $_POST['email'], $pwd]);

            $stmt1 = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE uid = ? AND password = ?');
            $stmt1->execute([$_POST['uid'], $pwd]);
            $user = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION['user_id'] = $user[0]['id'];
            $_SESSION['log_name'] = $user[0]['uid'];
            $_SESSION['email'] = $user[0]['email'];
            $_SESSION['password'] = $user[0]['password'];

            return new View('Registration/main.html', [
//
//                'uid' => $_POST['uid'],
//                'email' => $_POST['email']

                'uid' => $_SESSION['log_name'],
                'email' => $_SESSION['email']

            ]);

        }

        return new Redirect('/registration');

    }

    public function continue(): View
    {

        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE uid = ? AND password = ?');
        $stmt->execute(array($_SESSION['log_name'], $_SESSION['password']));
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE uid = ?');
        $stmt2->execute([$user[0]['id']]);
        $article = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // select query for article likes
        $articleId = $_SESSION['article_id'] ?? null;
        $stmt3 = (new Dbh())->connect()->prepare('SELECT count(*) FROM article_likes WHERE article_id = ?');
        $stmt3->execute([$articleId]);
        $articleLikes = $stmt3->fetchColumn();

//        var_dump($articleLikes);die;
        /**
         *  TO DO $articleLikes (show all counts ?);
         */
        return new View('Registration/show.html', [

            'id' => $user[0]['id'],
            'uid' => $user[0]['uid'],
            'email' => $user[0]['email'],
            'date' => $user[0]['created_at'],
            'articles' => $article,
            'articleLikes' => (int) $articleLikes

        ]);

    }

    public function login()
    {
        $stmt = (new Dbh())->connect()->prepare('SELECT password FROM users WHERE uid = ?');

        $stmt->execute(array($_POST['uid']));
        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($_POST['pwd'], $pwdHashed[0]["password"]);

        if ($checkPwd == false) {
            $stmt = null;
            return new Redirect('/registration');

        } elseif ($checkPwd == true) {
            $stmt = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE uid = ? OR password = ?');

            if (!$stmt->execute(array($_POST['uid'], $_POST['pwd']))) {
                $stmt = null;
            }

            if ($stmt->rowCount() == 0) {
                $stmt = null;
            }

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt2 = (new Dbh())->connect()->prepare('SELECT * FROM article WHERE uid = ?');
            $stmt2->execute([$user[0]['id']]);
            $article = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user[0]['id'];
            $_SESSION['log_name'] = $user[0]['uid'];
            $_SESSION['email'] = $user[0]['email'];
            $_SESSION['password'] = $user[0]['password'];
            $_SESSION['created_at'] = $user[0]['created_at'];

            return new View('Registration/show.html', [

//                'id' => $user[0]['id'],
//                'uid' => $user[0]['uid'],
//                'email' => $user[0]['email'],
//                'date' => $user[0]['created_at'],
//                'articles' => $article

                'id' =>  $_SESSION['user_id'],
                'uid' => $_SESSION['log_name'],
                'email' => $_SESSION['email'],
                'date' => $_SESSION['created_at'],
                'articles' => $article

            ]);

        }

    }

    public function create(array $vars): View
    {

        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute(array($vars['id']));
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return new View('Registration/create.html', [

            'id' => $vars['id'],
            'uid' => $user[0]['uid'],
            'email' => $user[0]['email'],
            'date' => $user[0]['created_at']
//            'errors' => Errors::getAll()

        ]);
    }

    public function article(array $vars)
    {
        session_start();

//        echo "<pre>";
//        print_r($_POST);die;

        if (!empty($_POST['title']) && !empty($_POST['description'])) {
            $stmt = (new Dbh())->connect()->prepare('INSERT INTO article (title, descriptions, uid, date) VALUES (?, ?, ?, ?)');
            $stmt->execute([$_POST['title'], $_POST['description'], $_POST['uid'], $_POST['date']]);

        }

        return new Redirect('/registration/continue');

    }

    public function delete(array $vars): Redirect
    {

//        var_dump($_POST['uid']);die;
//        var_dump($_POST);die;

        $stmt = (new Dbh())->connect()->prepare('DELETE FROM article WHERE id=?');
        $stmt->execute([$vars['id']]);

        return new Redirect('/registration/continue');
    }

    public function like(array $vars): Redirect
    {
//        session_start();
        $_SESSION['article_id'] = $vars['id'];

        $stmt = (new Dbh())->connect()->prepare('INSERT INTO article_likes (uid, article_id) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $vars['id']]);

        return new Redirect('/registration/continue');
    }

}