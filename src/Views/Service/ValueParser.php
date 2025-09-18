<?php

namespace RocketPhp\RocketUI\Views\Service;

class ValueParser
{
    public static function NestedValue(mixed $data, ?string $path): mixed
    {
        if (!$path) {
            return null;
        }

        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_object($current)) {
                if (self::objectHasProperty($current, $segment)) {
                    $current = $current->$segment;
                    continue;
                }

                $accessor = self::resolveAccessor($current, $segment);
                if ($accessor !== null) {
                    $current = $current->{$accessor}();
                    continue;
                }

                if ($current instanceof \ArrayAccess && $current->offsetExists($segment)) {
                    $current = $current[$segment];
                    continue;
                }
            }

            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
                continue;
            }

            return null;
        }

        return $current;
    }

    private static function objectHasProperty(object $object, string $property): bool
    {
        return array_key_exists($property, get_object_vars($object));
    }

    private static function resolveAccessor(object $object, string $segment): ?string
    {
        $candidates = [
            $segment,
            'get' . self::normalizeMethodSuffix($segment),
            'is' . self::normalizeMethodSuffix($segment),
            'has' . self::normalizeMethodSuffix($segment),
        ];

        foreach ($candidates as $method) {
            if (method_exists($object, $method)) {
                return $method;
            }
        }

        return null;
    }

    private static function normalizeMethodSuffix(string $segment): string
    {
        $segment = str_replace(['_', '-'], ' ', $segment);
        $segment = ucwords($segment);

        return str_replace(' ', '', $segment);
    }
}
