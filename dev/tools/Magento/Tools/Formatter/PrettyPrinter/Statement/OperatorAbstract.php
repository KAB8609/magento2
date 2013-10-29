<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;

abstract class OperatorAbstract extends AbstractSyntax
{
    protected $terminate = false;
    abstract public function operator();
    abstract public function associativity();
    abstract public function precedence();
    /**
     * Prints an expression node with the least amount of parentheses necessary to preserve the meaning.
     *
     * @param PHPParser_Node $node                Node to pretty print
     * @param int            $parentPrecedence    Precedence of the parent operator
     * @param int            $parentAssociativity Associativity of parent operator
     *                                            (-1 is left, 0 is nonassoc, 1 is right)
     * @param int            $childPosition       Position of the node relative to the operator
     *                                            (-1 is left, 1 is right)
     *
     * @return string The pretty printed node
     */
    /*
    protected function pPrec(PHPParser_Node $node, $parentPrecedence, $parentAssociativity, $childPosition) {
        $type = $node->getType();
        if (isset($this->precedenceMap[$type])) {
            $childPrecedence = $this->precedenceMap[$type][0];
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                return '(' . $this->{'p' . $type}($node) . ')';
            }
        }

        return $this->{'p' . $type}($node);
    }
    */
    protected function resolvePrecedence(PHPParser_Node $node, TreeNode $treeNode, $childPosition)
    {
        /** @var AbstractSyntax $statement */
        $child = StatementFactory::getInstance()->getStatement($node);
        if ($child instanceof OperatorAbstract) {
            $childPrecedence = $child->precedence();
            $parentPrecedence = $this->precedence();
            $parentAssociativity = $this->associativity();
            if ($childPrecedence > $parentPrecedence
                || ($parentPrecedence == $childPrecedence && $parentAssociativity != $childPosition)
            ) {
                $treeNode->getData()->add('(');
                $child->resolve($treeNode);
                $treeNode->getData()->add(')');
            } else {
                $child->resolve($treeNode);
            }
        } else {
            $child->resolve($treeNode);
        }
        if ($childPosition === 1 && $this->terminate) {
            $treeNode->getData()->add(';')->add(new HardLineBreak());
        }
    }
    public function resolve(TreeNode $treeNode)
    {
        if ($treeNode->getData() instanceof OperatorAbstract && $treeNode->getData() === $this) {
            $treeNode->setData(new Line());
            $this->terminate = true;
        }
    }
}
