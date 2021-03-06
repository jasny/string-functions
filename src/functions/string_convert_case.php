<?php

declare(strict_types=1);

namespace Improved;

/**
 * Convert the alphabetic characters in a string to lowercase or uppercase.
 *
 * @param string $subject
 * @param int    $flags
 * @return string
 */
function string_convert_case(string $subject, int $flags): string
{
    return $flags & STRING_BINARY === 0
        ? _string_convert_case_mb($subject, $flags)
        : _string_convert_case_binary($subject, $flags);
}

/**
 * @internal
 */
function _string_convert_case_binary(string $subject, int $flags): string
{
    switch ($flags & (STRING_UPPERCASE | STRING_LOWERCASE | STRING_TITLE | STRING_SIDE_LEFT | STRING_SIDE_RIGHT)) {
        case STRING_UPPERCASE:
        case STRING_UPPERCASE | STRING_TITLE:
            return \strtoupper($subject);
        case STRING_LOWERCASE:
            return \strtolower($subject);
        case STRING_TITLE:
            return \ucwords($subject);
        case STRING_LOWERCASE | STRING_TITLE:
            return \ucwords(\strtolower($subject));

        case STRING_SIDE_LEFT | STRING_UPPERCASE:
        case STRING_SIDE_LEFT | STRING_TITLE:
        case STRING_SIDE_LEFT | STRING_UPPERCASE | STRING_TITLE:
            return \ucfirst($subject);
        case STRING_SIDE_LEFT | STRING_LOWERCASE:
            return \lcfirst($subject);

        case STRING_SIDE_RIGHT | STRING_UPPERCASE:
        case STRING_SIDE_RIGHT | STRING_TITLE:
        case STRING_SIDE_RIGHT | STRING_UPPERCASE | STRING_TITLE:
            return \strrev(\ucfirst(\strrev($subject)));
        case STRING_SIDE_RIGHT | STRING_LOWERCASE:
            return \strrev(\lcfirst(\strrev($subject)));

        default:
            return $subject;
    }
}

/**
 * @internal
 */
function _string_convert_case_mb(string $subject, int $flags): string
{
    $mode = ($flags & (STRING_UPPERCASE | STRING_LOWERCASE | STRING_TITLE)) >> 9;

    switch ($flags & (STRING_SIDE_LEFT | STRING_SIDE_RIGHT)) {
        case STRING_SIDE_LEFT:
            $length = \mb_strlen($subject);
            $firstChar = \mb_substr($subject, 0, 1);
            $rest = \mb_substr($subject, 1, $length - 1);

            return _mb_convert_case_all($firstChar, $mode) . $rest;

        case STRING_SIDE_RIGHT:
            $lastChar = \mb_substr($subject, -1, 1);
            $rest = \mb_substr($subject, 0, -1);

            return $rest . _mb_convert_case_all($lastChar, $mode);

        default:
            return _mb_convert_case_all($subject, $mode);
    }
}

/**
 * @internal
 */
function _mb_convert_case_all(string $subject, int $mode): string
{
    $converted = \mb_convert_case($subject, $mode, 'UTF-8');

    return \strtr($converted, ['ß' => 'ẞ']);
}
