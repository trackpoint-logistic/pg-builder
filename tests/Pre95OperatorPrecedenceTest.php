<?php
/**
 * Query builder for PostgreSQL backed by a query parser
 *
 * LICENSE
 *
 * This source file is subject to BSD 2-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.githubusercontent.com/sad-spirit/pg-builder/master/LICENSE
 *
 * @package   sad_spirit\pg_builder
 * @copyright 2014-2017 Alexey Borzov
 * @author    Alexey Borzov <avb@php.net>
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause license
 * @link      https://github.com/sad-spirit/pg-builder
 */

namespace sad_spirit\pg_builder\tests;

use sad_spirit\pg_builder\Parser;

/**
 * Tests for parser using pre-Postgres 9.5 operator precedence
 */
class Pre95OperatorPrecedenceTest extends OperatorPrecedenceTest
{
    public function setUp()
    {
        parent::setUp();
        $this->parser->setOperatorPrecedence(Parser::OPERATOR_PRECEDENCE_PRE_9_5);
    }

    /**
     * @dataProvider associativeEqualityProvider
     * @param $expression
     * @param $parsed
     */
    public function testAssociativeEquality($expression, $parsed)
    {
        $this->assertEquals($parsed, $this->parser->parseExpression($expression));
    }

    /**
     * @dataProvider inequalityPrecedenceProvider
     * @param $expression
     * @param $parsed
     */
    public function testInequalityPrecedence($expression, $parsed)
    {
        if (is_string($parsed)) {
            $this->setExpectedException(
                'sad_spirit\pg_builder\exceptions\SyntaxException', $parsed
            );
            $this->parser->parseExpression($expression);
        } else {
            $this->assertEquals($parsed, $this->parser->parseExpression($expression));
        }
    }
}