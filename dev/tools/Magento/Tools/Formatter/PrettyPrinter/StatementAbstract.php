<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use PHPParser_Node;
use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is the base class for all printer statements.
 */
abstract class StatementAbstract
{
    const ATTRIBUTE_COMMENTS = 'comments';

    /**
     * This member holds the current node.
     * @var \PHPParser_NodeAbstract
     */
    protected $node;

    /**
     * This method constructs a new statement based on the specify node.
     * @param \PHPParser_NodeAbstract $node
     */
    public function __construct(\PHPParser_NodeAbstract $node)
    {
        $this->node = $node;
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public abstract function resolve(TreeNode $treeNode);

    /**
     * This method adds any comments in the current node to the passed in tree.
     * @param Tree $tree
     */
    protected function addComments(Tree $tree)
    {
        /* Reference
           $comments = $this->pComments($node->getAttribute('comments', array()));
        */
        // only attempt to add comments if they are present
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            // add individual lines of the comments to the tree
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
            foreach ($comments as $comment) {
                // split the lines so that they can be indented correctly
                $commentLines = explode(HardLineBreak::EOL, $comment->getReformattedText());
                foreach ($commentLines as $commentLine) {
                    // add the line individually to the tree so that they can be indented correctly
                    $tree->addSibling(new TreeNode((new Line($commentLine))->add(new HardLineBreak())));
                }
            }
        }
    }

    /**
     * This method adds any comments in the current node as prior siblings to the current node.
     * @param TreeNode $treeNode Node representing the current node.
     */
    protected function addCommentsBefore($treeNode) {
        // only attempt to add comments if they are present
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            // add individual lines of the comments to the tree
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
            foreach ($comments as $comment) {
                // split the lines so that they can be indented correctly
                $commentLines = explode(HardLineBreak::EOL, $comment->getReformattedText());
                foreach ($commentLines as $commentLine) {
                    // add the line individually to the tree so that they can be indented correctly
                    $treeNode->addSibling(new TreeNode((new Line($commentLine))->add(new HardLineBreak())), false);
                }
            }
        }
    }

    /**
     * This method adds modifiers to the the line based on the bit map passed in.
     * @param mixed $modifiers Bit map containing the markers for the various modifiers.
     * @param Line $line Instance of line to add modifier.
     */
    protected function addModifier($modifiers, Line $line)
    {
        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) {
            $line->add('abstract ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL) {
            $line->add('final ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) {
            $line->add('public ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) {
            $line->add('protected ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) {
            $line->add('private ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC) {
            $line->add('static ');
        }
    }

    /**
     * This method adds the arguments to the current line
     * @param array $arguments
     * @param TreeNode $treeNode
     * @param Line $line
     */
    protected function processArgumentList(array $arguments, TreeNode $treeNode, Line $line)
    {
        foreach ($arguments as $index => $argument) {
            $line->add(new ConditionalLineBreak(' '));
            // process the argument itself
            $this->resolveNode($argument, $treeNode);
            // in not the last on, separate with a comma
            if ($index < sizeof($arguments) - 1) {
                $line->add(',');
            }
        }
    }

    /**
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total) {
        // default action is to do nothing, since it is up to the derived node to determine exactly
        // what needs to be done with the newly added node
        return $originatingNode;
    }

    /**
     * This method parses the given nodes and places them in the tree by calling processNode. This
     * allows the derived class a chance to insert the new node into the appropriate location.
     * @param mixed $node Array or single nocde
     * @param TreeNode $originatingNode Node where new nodes are originating from
     */
    protected function processNodes($nodes, TreeNode $originatingNode)
    {
        if (is_array($nodes)) {
            $total = count($nodes);
            foreach ($nodes as $index => $node) {
                $statement = StatementFactory::getInstance()->getStatement($node);
                $originatingNode = $this->processNode($originatingNode, new TreeNode($statement), $index, $total);
            }
        } else {
            $statement = StatementFactory::getInstance()->getStatement($nodes);
            $originatingNode = $this->processNode($originatingNode, new TreeNode($statement), 0, 1);
        }
        // return the last node that was added (or whatever was returned from the last node processing)
        return $originatingNode;
    }

    /**
     * This method resolves the node immediately.
     * @param PHPParser_Node $node
     * @param Tree $tree Tree representation of the resulting code
     */
    protected function resolveNode(PHPParser_Node $node, TreeNode $treeNode)
    {
        $statement = StatementFactory::getInstance()->getStatement($node);
        $statement->resolve($treeNode);
    }

    /**
     * This method returns the full name of the class.
     *
     * @return string Full name of the class is called through.
     */
    public static function getType()
    {
        return get_called_class();
    }
}
