<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\SimpleListLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Echo;

class EchoStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified echo node
     * @param PHPParser_Node_Stmt_Echo $node
     */
    public function __construct(PHPParser_Node_Stmt_Echo $node)
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
        // add the class line
        $this->addToLine($treeNode, 'echo ');
        // add the arguments
        $treeNode = $this->processArgumentList($this->node->exprs, $treeNode, new SimpleListLineBreak());
        // add in the terminator
        $this->addToLine($treeNode, ';')->add(new HardLineBreak());
        return $treeNode;
    }
}
