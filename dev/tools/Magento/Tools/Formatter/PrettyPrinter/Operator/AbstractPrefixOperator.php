<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Operator;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

abstract class AbstractPrefixOperator extends AbstractOperator
{
    /*
    protected function pPrefixOp($type, $operatorString, PHPParser_Node $node) {
        list($precedence, $associativity) = $this->precedenceMap[$type];
        return $operatorString . $this->pPrec($node, $precedence, $associativity, 1);
    }
    */
    protected function resolvePrefixOperator(TreeNode $treeNode)
    {
        /** @var Line $line */
        $line = $treeNode->getData();
        // Resolve the children according to precedence.
        $line->add($this->operator());
        $this->resolvePrecedence($this->expr(), $treeNode, 1);
    }
    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        $this->resolvePrefixOperator($treeNode);
    }
    public function expr()
    {
        return $this->node->expr;
    }
}
