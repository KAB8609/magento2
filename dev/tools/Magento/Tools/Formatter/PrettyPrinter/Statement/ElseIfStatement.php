<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_Elseif;

class ElseIfStatement extends StatementAbstract
{
    /**
     * This method constructs a new statement based on the specified elseif statement.
     * @param PHPParser_Node_Stmt_Elseif $node
     */
    public function __construct(PHPParser_Node_Stmt_Elseif $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /* Reference
        return ' elseif (' . $this->p($node->cond) . ') {'
             . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
        */
        // add the if line
        $line = new Line('} elseif (');
        // replace the statement with the line since it is resolved or at least in the process of being resolved
        $treeNode->setData($line);
        $this->resolveNode($this->node->cond, $treeNode);
        $line->add(') {')->add(new HardLineBreak());
        // processing the child nodes
        $this->processNodes($this->node->stmts, $treeNode);
    }
}
