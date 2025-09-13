<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get a user by their email.
     *
     * @param string $email
     * @return User|null
     */
    public function getByEmail(string $email): ?User;
}
