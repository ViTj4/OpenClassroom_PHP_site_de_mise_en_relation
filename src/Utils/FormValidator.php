<?php

class FormValidator
{
    public const PSEUDO_MIN_LENGTH = 2;
    public const PSEUDO_MAX_LENGTH = 30;
    public const BOOK_TITLE_MAX_LENGTH = 190;
    public const BOOK_AUTHOR_MAX_LENGTH = 190;
    public const BOOK_DESCRIPTION_MAX_LENGTH = 2000;

    private const EMAIL_PATTERN = '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/';
    private const PSEUDO_PATTERN = '/^[a-zA-Z0-9_-]+$/';

    public static function isValidEmail(string $email): bool
    {
        return preg_match(self::EMAIL_PATTERN, $email) === 1;
    }

    public static function isValidPseudo(string $pseudo): bool
    {
        return self::hasLengthBetween($pseudo, self::PSEUDO_MIN_LENGTH, self::PSEUDO_MAX_LENGTH)
            && preg_match(self::PSEUDO_PATTERN, $pseudo) === 1;
    }

    public static function hasLengthBetween(string $value, int $min, int $max): bool
    {
        $length = self::length($value);

        return $length >= $min && $length <= $max;
    }

    public static function hasMaxLength(string $value, int $max): bool
    {
        return self::length($value) <= $max;
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
