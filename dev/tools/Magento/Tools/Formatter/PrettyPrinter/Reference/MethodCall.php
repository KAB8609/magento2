<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\CallLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardIndentLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr;
use PHPParser_Node_Expr_MethodCall;

class MethodCall extends AbstractFunctionReference
{
    /**
     * This method constructs a new statement based on the specified expression.
     * @param PHPParser_Node_Expr_MethodCall $node
     */
    public function __construct(PHPParser_Node_Expr_MethodCall $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // add the variable
        $this->resolveVariable($this->node->var, $treeNode);
        // add in the dereference
        $this->addToLine(
            $treeNode,
            new ConditionalLineBreak(array(array(''), array('', new HardIndentLineBreak())))
        )->add('->');
        // if the name is an expression, then use the framework to resolve
        if ($this->node->name instanceof PHPParser_Node_Expr) {
            $this->addToLine($treeNode, '{');
            $treeNode = $this->resolveNode($this->node->name, $treeNode);
            $this->addToLine($treeNode, '}');
        } else {
            // otherwise, just use the name
            $this->addToLine($treeNode, $this->node->name);
        }
        // add in the argument call
        return $this->processArgsList($this->node->args, $treeNode, new CallLineBreak());
    }
}
