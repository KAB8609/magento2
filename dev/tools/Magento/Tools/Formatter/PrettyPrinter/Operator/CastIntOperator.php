<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_Int;

class CastIntOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_Int $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(int) ';
    }
    /* 'Expr_Cast_Int'         => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }
}
