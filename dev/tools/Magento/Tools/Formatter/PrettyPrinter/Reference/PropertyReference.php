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
use PHPParser_Node_Stmt_PropertyProperty;

class PropertyReference extends AbstractVariableReference
{
    /**
     * This method constructs a new reference based on the specified property.
     * @param PHPParser_Node_Stmt_PropertyProperty $node
     */
    public function __construct(PHPParser_Node_Stmt_PropertyProperty $node)
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
        // add in the variable reference
        $this->addVariableReference($treeNode, $line);
    }
}