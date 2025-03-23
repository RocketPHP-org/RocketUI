<?php

namespace RocketPhp\RocketUI\Views\Service;

class ValueParser
{
    public static function NestedValue(object $data, ?string $path): mixed
    {
        if (!$path) {
            return null;
        }

        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (is_object($current) && isset($current->$segment)) {
                $current = $current->$segment;
            } else {
                return null;
            }
        }

        return $current;
    }

}