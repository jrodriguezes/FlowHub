<?php

namespace App\Services;

class ConditionEvaluator
{
    public function evaluate(array $conditions, array $payload)
    {
        // if there are no conditions, anyways the workflow is going to run
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            // normalize the field: remove "trigger." if it exists to search directly in the payload
            if (str_starts_with($field, 'trigger.')) {
                $field = substr($field, 8);
            }

            $actualValue = data_get($payload, $field);

            switch ($operator) {
                case 'equals':
                    $match = ($actualValue === $value);
                    break;
                case 'not_equals':
                    $match = ($actualValue !== $value);
                    break;
                case 'contains':
                    $match = str_contains((string) $actualValue, (string) $value);
                    break;
                case 'exists':
                    $match = ($actualValue !== null);
                    break;
                default:
                    throw new \Exception('Operador desconocido: ' . $operator);
            }

            if (!$match) {
                return false;
            }
        }

        return true;
    }
}