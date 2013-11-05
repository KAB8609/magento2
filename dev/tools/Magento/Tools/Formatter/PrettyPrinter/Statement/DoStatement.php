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
use PHPParser_Node_Stmt_Do;

class DoStatement extends AbstractLoopStatement
{
    /**
     * This method constructs a new statement based on the specified for statement.
     * @param PHPParser_Node_Stmt_Do $node
     */
    public function __construct(PHPParser_Node_Stmt_Do $node)
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
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the namespace line
        $line->add('do {')->add(new HardLineBreak());
        // add in the children nodes
        $this->processNodes($this->node->stmts, $treeNode);
        // add the closing bracket and condition
        $line = new Line('} while (');
        // add the new line to get it below the body statements
        $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine($line));
        // resolve the condition
        $this->resolveNode($this->node->cond, $treeNode);
        // add the terminating line
        $line->add(');')->add(new HardLineBreak());
    }
}
