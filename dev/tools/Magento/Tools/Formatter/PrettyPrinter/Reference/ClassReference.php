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
use PHPParser_Node_Name;

class ClassReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified name.
     * @param PHPParser_Node_Name $node
     */
    public function __construct(PHPParser_Node_Name $node)
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
        // Add the preceding \ if this is a fully qualified name
        if ($this->node->isFullyQualified()) {
            $line->add('\\');
        }
        // add the name to the end of the current line
        $line->add((string) $this->node);
    }
}