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
use PHPParser_Node_Param;

class ParameterReference extends AbstractVariableReference
{
    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Param $node
     */
    public function __construct(PHPParser_Node_Param $node)
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
        return ($node->type ? (is_string($node->type) ? $node->type : $this->p($node->type)) . ' ' : '')
             . ($node->byRef ? '&' : '')
             . '$' . $node->name
             . ($node->default ? ' = ' . $this->p($node->default) : '');
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // if the type is specified, add it to the line
        if ($this->node->type) {
            // if the type is a string, just add it
            if (is_string($this->node->type)) {
                $line->add($this->node->type);
            } else {
                // otherwise, assume it is a node, and resolve it
                $this->resolveNode($this->node->type, $treeNode);
            }
            $line->add(' ');
        }
        // if the parameter is by reference, so note it
        if ($this->node->byRef) {
            $line->add('&');
        }
        // add in the variable reference
        $this->addVariableReference($treeNode, $line);
    }
}