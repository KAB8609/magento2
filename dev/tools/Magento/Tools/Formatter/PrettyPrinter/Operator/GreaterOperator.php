<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Greater;

class GreaterOperator extends AbstractInfixOperator
{
    public function __construct(PHPParser_Node_Expr_Greater $node)
    {
        parent::__construct($node);
    }

    public function operator()
    {
        return '>';
    }

    /* 'Expr_Greater'          => array( 7,  0), */
    public function associativity()
    {
        0;
    }

    public function precedence()
    {
        return 7;
    }
}
