<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Const;

class ClassConstant extends AbstractReference
{
    /**
     * This method constructs a new reference based on the specified constant.
     * @param PHPParser_Node_Const $node
     */
    public function __construct(PHPParser_Node_Const $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the name to the end of the current line
        $line->add($this->node->name)->add(' = ');
        // add in the value as a node
        $this->resolveNode($this->node->value, $treeNode);
    }
}
