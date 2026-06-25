<?php

class PasswordHasher
{
    public function hash(string $password): string
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            throw new RuntimeException('Argon2id n\'est pas disponible sur cette installation PHP.');
        }

        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return defined('PASSWORD_ARGON2ID') && password_needs_rehash($hash, PASSWORD_ARGON2ID);
    }
}
