<?php

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Calculator;

class CalculatorTest extends TestCase {
    public function testBuildOperationsWithAdditionAndSubtraction() {
        $calc = new Calculator();
        $result = $calc->buildOperations("3 + 5 - 2");
        $this->assertEquals(['3', '+5', '-2'], $result);
    }

    public function testBuildOperationsWithAllOperations() {
        $calc = new Calculator();
        $result = $calc->buildOperations("10 - 5 + 3 x 2 / 4");
        $this->assertEquals(['10', '-5', '+3', 'x2', '/4'], $result);
    }

    public function testFollowsPemdasOrdering() {
        $calc = new Calculator();
        $operations = ['10', '-5', '+3', 'x2', '/4'];
        $pemdasOperations = $calc->pemdas($operations);
        $this->assertEquals(['10', '-5', '+1.5'], $pemdasOperations);
    }

    public function testEvaluateWithAdditionAndSubtraction() {
        $calc = new Calculator();
        $result = $calc->evaluate("3 + 5 - 2");
        $this->assertEquals(6.0, $result);
    }

    public function testEvaluatesWithMultiplicationAndDivision() {
        $calc = new Calculator();
        $result = $calc->evaluate("10 x 2 / 4");
        $this->assertEquals(5.0, $result);
    }

    public function testEvaluateWithAllOperations() {
        $calc = new Calculator();
        $result = $calc->evaluate("10 - 5 + 3 x 2 / 4");
        $this->assertEquals(6.5, $result);
    }
}
