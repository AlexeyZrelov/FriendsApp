<?php

namespace App\Models;

class User
{
    private int $id;
    private string $uid;
    private string $email;
    private string $password;
    private string $created_at;

    public function __construct(int $id, string $uid, string $email, string $password, string $created_at)
    {
        $this->id = $id;
        $this->uid = $uid;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
}