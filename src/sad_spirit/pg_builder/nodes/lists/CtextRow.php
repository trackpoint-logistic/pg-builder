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
 * @copyright 2014 Alexey Borzov
 * @author    Alexey Borzov <avb@php.net>
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause license
 * @link      https://github.com/sad-spirit/pg-builder
 */

namespace sad_spirit\pg_builder\nodes\lists;

use sad_spirit\pg_builder\nodes\ScalarExpression,
    sad_spirit\pg_builder\nodes\SetToDefault,
    sad_spirit\pg_builder\exceptions\InvalidArgumentException,
    sad_spirit\pg_builder\TreeWalker,
    sad_spirit\pg_builder\Parseable,
    sad_spirit\pg_builder\ElementParseable,
    sad_spirit\pg_builder\Parser;

/**
 * A row expression for use in VALUES clauses
 *
 * Similar to ExpressionList and RowExpression, unlike the former can also contain SetToDefault nodes,
 * unlike the latter cannot be used in scalar context
 */
class CtextRow extends NonAssociativeList implements Parseable, ElementParseable
{
    protected function normalizeElement(&$offset, &$value)
    {
        parent::normalizeElement($offset, $value);

        if (!($value instanceof ScalarExpression) && !($value instanceof SetToDefault)) {
            throw new InvalidArgumentException(sprintf(
                '%s can contain only instances of ScalarExpression or SetToDefault, %s given',
                __CLASS__, is_object($value) ? 'object(' . get_class($value) . ')' : gettype($value)
            ));
        }
    }

    public function dispatch(TreeWalker $walker)
    {
        return $walker->walkCtextRow($this);
    }

    public function createElementFromString($sql)
    {
        if (!($parser = $this->getParser())) {
            throw new InvalidArgumentException("Passed a string as a list element without a Parser available");
        }
        return $parser->parseExpressionWithDefault($sql);
    }

    public static function createFromString(Parser $parser, $sql)
    {
        return $parser->parseCtextRow($sql);
    }
}