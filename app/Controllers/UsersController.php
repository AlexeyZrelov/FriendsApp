<?php

namespace App\Controllers;

use App\View;

class UsersController
{
    public function index(): View
    {
        // get information from database
        // create array with Article objects

        return new View('Users/index.html', [
            'articles' => []
        ]);

    }

    public function show(array $vars): View
    {
        // $vars['id'];
        // get information from database where article ID = $vars['id']

        return new View('Users/show.html', [
            'id' => $vars['id']
        ]);
    }
}