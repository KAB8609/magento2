<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */


namespace Magento\Tools\Formatter\Tree;


interface Node
{
    /**
     * This method adds the named child to the end of the children nodes
     * @param TreeNode $newChild Child node to be added
     * @param TreeNode $adjacentNode Optional child node to place new node next to
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     */
    public function addChild(TreeNode $newChild, TreeNode $adjacentNode = null, $after = true);

    /**
     * This method adds a sibling node to the current node by adding the new sibling as a child of this nodes parent.
     * @param TreeNode $newSibling Sibling node to be added
     * @param bool $after Flag indicating that the sibling should be added after this node. If false, the sibling is
     * added prior to this node.
     */
    public function addSibling(TreeNode $newSibling, $after = true);

    /**
     * This method returns the array of children.
     * @return array
     */
    public function getChildren();

    /**
     * This method removes the specified child from the child list.
     * @param TreeNode $existingChild Node representing an existing child.
     */
    public function removeChild(TreeNode $existingChild);
}