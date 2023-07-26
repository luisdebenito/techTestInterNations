<?php

namespace App\Builder;

use App\Entity\User;
use Error;

class BuilderError extends Error
{
}

class UserBuilder
{
    private array $errors  = array();
    private User $user;
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function __construct(array $request)
    {
        $this->user = new User();
        try {
          $name = $request['name'] ?? throw new BuilderError("Name is required");
          $this->user->setName($name);
        } catch (BuilderError $err) {
          $this->errors[] = $err->getMessage();
        }
    }
}
