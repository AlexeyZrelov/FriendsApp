<?php

namespace App\Controllers;

use App\Redirect;
use App\View;
use App\Models\Dbh;
use PDO;

class FriendInviteController
{
    public function index(): View
    {

        $stmt = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE id <> ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $invite = $_SESSION['friend_id'] ?? null;

        return new View('Invite/index.html', [

            'users' => $user,
            'uid'   => $_SESSION['log_name'],
            'email' => $_SESSION['email'],
            'invite' => $invite

        ]);
    }

    public function create(array $vars): View
    {
        $stmt = (new Dbh())->connect()->prepare('INSERT INTO friends (user_id, friend_id) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $vars['id']]);

        // user
        $stmt1 = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt1->execute([$_SESSION['user_id']]);
        $user = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // check friend invite
        $stmt2 = (new Dbh())->connect()->prepare('SELECT * FROM friends WHERE user_id = ? AND user_id NOT IN (SELECT friend_id from friends WHERE friend_id = ?)');
        $stmt2->execute([$user[0]['id'], $vars['id']]);
        $friends = $stmt2->fetch();

        // friend_id
        $stmt3 = (new Dbh())->connect()->prepare('SELECT friend_id FROM friends WHERE user_id = ?');
        $stmt3->execute([$_SESSION['user_id']]);
        $friendsId = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // friends
        $inviteFriend = [];
        $stmt4 = (new Dbh())->connect()->prepare('SELECT * FROM users WHERE id = ?');

        foreach ($friendsId as $val) {
            $stmt4->execute([$val['friend_id']]);
            $inviteFriend[] = $stmt4->fetchAll(PDO::FETCH_ASSOC)[0];
        }

        return new View('Invite/friends.html', [

            'users' => $user,
            'uid'   => $user[0]['uid'],
            'email' => $user[0]['email'],
            'friends' => $inviteFriend

        ]);

//        return new Redirect('/invite');
    }

    public function delete(array $vars): Redirect
    {

        $stmt = (new Dbh())->connect()->prepare('DELETE FROM friends WHERE friend_id=?');
        $stmt->execute([$vars['id']]);

        return new Redirect('/invite');
    }

}