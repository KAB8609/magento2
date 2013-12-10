<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;
use PHPParser_Node_Expr_New;

/**
 * This class represents the partial line elements, such as references to string or classes.
 * Class AbstractReference
 * @package Magento\Tools\Formatter\PrettyPrinter\Statement
 */
abstract class AbstractReference extends AbstractSyntax
{
    /**
     * This method process the list of strings and adds them to the current node.
     * @param array $encapsList List of strings to process.
     * @param string $quote String containing the enclosing quote type.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    protected function encapsList(array $encapsList, $quote, TreeNode $treeNode)
    {
        foreach ($encapsList as $element) {
            if (is_string($element)) {
                $this->addToLine($treeNode, addcslashes($element, "\n\r\t\f\v$" . $quote . "\\"));
            } else {
                $this->addToLine($treeNode, '{');
                $treeNode = $this->resolveNode($element, $treeNode);
                $this->addToLine($treeNode, '}');
            }
        }
    }

    /**
     * This method returns if the needle can be found at the end of this string.
     * @param string $haystack String to look in.
     * @param string $needle String to find.
     * @param bool $case_insensitivity If true, then comparison is case insensitive.
     * @return bool
     */
    protected function endsWith($haystack, $needle, $case_insensitivity = false)
    {
        $found = false;
        // determine lengths to make sure the haystack is longer than the needle
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);
        // only need to do the compare if the haystack can actually contain the needle
        if ($haystackLength >= $needleLength) {
            $found = substr_compare($haystack, $needle, -$needleLength, $needleLength, $case_insensitivity) === 0;
        }
        return $found;
    }

    /**
     * This method resolves the passed in node. If it is a special case of a new call, it is
     * surrounded with parenthesis.
     * @param PHPParser_Node $node Raw node being processed
     * @param TreeNode $treeNode Node containing the current statement.
     */
    protected function resolveVariable(PHPParser_Node $node, TreeNode $treeNode)
    {
        if ($node instanceof PHPParser_Node_Expr_New) {
            // enclose new reference in parens
            $this->addToLine($treeNode, '(');
            $this->resolveNode($node, $treeNode);
            $this->addToLine($treeNode, ')');
        } else {
            // otherwise, just resolve the node
            $this->resolveNode($node, $treeNode);
        }
    }
}
