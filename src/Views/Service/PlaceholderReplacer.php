<?php

namespace RocketPhp\RocketUI\Views\Service;

class PlaceholderReplacer
{
    public static function replace(string $text, mixed $data): string
    {
        return preg_replace_callback(
            '/@@Rocket\/([a-zA-Z_][a-zA-Z0-9_.]*)/',
            function ($matches) use ($data) {
                $fieldPath = $matches[1];
                $value = self::getFieldValue($data, $fieldPath);
                return $value !== null ? (string) $value : $matches[0];
            },
            $text
        );
    }

    private static function getFieldValue(mixed $data, string $fieldPath): mixed
    {
        if (!$data) {
            return null;
        }

        if (is_object($data)) {
            return ValueParser::NestedValue($data, $fieldPath);
        }

        if (is_array($data)) {
            $segments = explode('.', $fieldPath);
            $current = $data;

            foreach ($segments as $segment) {
                if (isset($current[$segment])) {
                    $current = $current[$segment];
                } else {
                    return null;
                }
            }

            return $current;
        }

        return null;
    }

    public static function replaceInArray(array $array, mixed $data): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $result[$key] = self::replace($value, $data);
            } elseif (is_array($value)) {
                $result[$key] = self::replaceInArray($value, $data);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}