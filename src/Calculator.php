<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2;

// Calculator Class

class Calculator {
    public function add(float $a, float $b) {
        return $a + $b;
    }

    public function subtract(float $a, float $b) {
        return $a - $b;
    }

    public function multiply(float $a, float $b) {
        return $a * $b;
    }

    public function divide(float $a, float $b) {
        return $a / $b;
    }

    // Evaluate a string of expressions
    public function evaluate(string $expression) {
        $operations = $this->pemdas($this->buildOperations($expression));
        $result = floatval($operations[0]);
        for ($i = 1; $i < count($operations); $i++) {
            $result = $this->evaluateOperation($result, $operations[$i]);
        }
        return $result;
    }

    // Build operation strings from a single expression string
    public function buildOperations(string $expression) {
        preg_match_all('/[+\-x%\/]?\s*\d+(?:\.\d+)?/', $expression, $matches);
        $expressions = $matches[0];
        return array_map(function ($expr) {
            return preg_replace('/\s+/', '', $expr);
        }, $expressions);
    }

    // Reorder operations according to PEMDAS
    public function pemdas(array $operations): array {
        foreach (['x', '/'] as $symbol) {
            $i = 0;

            while ($i < count($operations)) {
                $token = $operations[$i];

                // Only process x or / tokens
                if (str_starts_with($token, $symbol)) {
                    $leftToken = $operations[$i - 1];

                    // Extract left numeric value
                    if ($leftToken[0] === '+' || $leftToken[0] === '-') {
                        $leftValue = floatval(substr($leftToken, 1));
                        $leftSign  = $leftToken[0];
                    } else {
                        $leftValue = floatval($leftToken);
                        $leftSign  = ''; // no sign means absolute number
                    }

                    // Compute result of leftValue (x or /) rightValue
                    $result = $this->evaluateOperation($leftValue, $token);

                    // Format result token:
                    // - If result is negative, prefix "-"
                    // - If result is positive and left had a sign, prefix "+"
                    // - If result is positive and left had no sign, no prefix
                    if ($result < 0) {
                        $formatted = (string)$result; // already has "-"
                    } else {
                        $formatted = ($leftSign === '-' ? '-' : ($leftSign === '+' ? '+' : '')) . $result;
                    }

                    // Replace left + operator with the new combined token
                    array_splice($operations, $i - 1, 2, [$formatted]);

                    // Move pointer back one step to re-evaluate after splice
                    $i--;
                    continue;
                }

                $i++;
            }
        }

        return $operations;
    }

    // Evaluate a single operation
    private function evaluateOperation(float $result, string $operation): float {
        $symbol = $operation[0];
        switch ($symbol) {
            case '+':
                $result = $this->add($result, floatval(substr($operation, 1)));
                break;
            case '-':
                $result = $this->subtract($result, floatval(substr($operation, 1)));
                break;
            case 'x':
                $result = $this->multiply($result, floatval(substr($operation, 1)));
                break;
            case '/':
                $result = $this->divide($result, floatval(substr($operation, 1)));
                break;
            default:
                $result = floatval($operation);
                break;
        }
        return $result;
    }
}
?>
