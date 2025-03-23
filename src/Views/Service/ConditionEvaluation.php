<?php

namespace RocketPhp\RocketUI\Views\Service;

use RocketPhp\RocketRule\Condition\ConditionBuilder;

class ConditionEvaluation
{
    public static function evaluateCondition(mixed $data, ?string $dsl): bool
    {
        if (!empty($dsl)) {
            if ($dsl === 'true') {
                return true;
            }
            if ($dsl === 'false') {
                return false;
            }
            if ($dsl === 'is_new()') {
                return empty($data);
            }
            if ($dsl === '!is_new()') {
                return !empty($data);
            }

            if (is_object($data) && preg_match('/^(\!?)([a-zA-Z_][a-zA-Z0-9_]*)\(\)$/', $dsl, $matches)) {
                $negate = $matches[1] === '!';
                $method = $matches[2];

                if (method_exists($data, $method)) {
                    $result = $data->$method();
                    return $negate ? !$result : $result;
                }
            }

            $condition = ConditionBuilder::build($dsl);
            return $condition->isSatisfiedBy($data);
        }

        return false;
    }

}