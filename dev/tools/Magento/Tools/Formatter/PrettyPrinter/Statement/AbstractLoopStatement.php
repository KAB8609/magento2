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

abstract class AbstractLoopStatement extends AbstractStatement
{
    /**
     * This method adds in the closing element and the body statements of a loop.
     * @param TreeNode $treeNode
     */
    protected function addBody(TreeNode $treeNode)
    {
        // add in the terminating paren and opening brace
        $this->addToLine($treeNode, ') {')->add(new HardLineBreak());
        // add in the children nodes
        $this->processNodes($this->node->stmts, $treeNode);
        // add the closing brace on a new line
        return $treeNode->addSibling(AbstractSyntax::getNodeLine((new Line('}'))->add(new HardLineBreak())));
    }
}
