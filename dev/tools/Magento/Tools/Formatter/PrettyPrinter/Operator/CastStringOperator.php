<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use PHPParser_Node_Expr_Cast_String;

class CastStringOperator extends AbstractPrefixOperator
{
    public function __construct(PHPParser_Node_Expr_Cast_String $node)
    {
        parent::__construct($node);
    }
    public function operator()
    {
        return '(string) ';
    }
    /* 'Expr_Cast_String'      => array( 1,  1), */
    public function associativity()
    {
        return 1;
    }

    public function precedence()
    {
        return 1;
    }
}
