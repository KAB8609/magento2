<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\LineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Arg;
use PHPParser_Node_Expr_Closure;
use PHPParser_Node_Expr_FuncCall;

class AbstractFunctionReference extends AbstractReference
{
    protected function processArgsList(array $arguments, TreeNode $treeNode, Line $line, LineBreak $lineBreak)
    {
        $lastProcessedNode = null;
        // search for a closure as one of the arguments
        if ($this->hasClosure($arguments)) {
            // force the multi-line argument list
            $line->add('(')->add(new HardLineBreak());
            foreach ($arguments as $index => $argument) {
                // create a new child for each argument
                $line = new Line();
                $lastProcessedNode = $treeNode->addChild(AbstractSyntax::getNodeLine($line));
                // process the argument itself
                $lastProcessedNode = $this->resolveNode($argument, $lastProcessedNode);
                /** @var Line $line */
                if (null !== $lastProcessedNode) {
                    $line = $lastProcessedNode->getData()->line;
                }
                // if not the last one, separate with a comma
                if ($index < sizeof($arguments) - 1) {
                    $line->add(',');
                }
                // each argument will have a hard line break
                $line->add(new HardLineBreak());
            }
            // add the closing on a new line
            $lastProcessedNode = $treeNode->addSibling(AbstractSyntax::getNodeLine(new Line(')')));
        } else {
            // just process as normal
            $line->add('(');
            $lastProcessedNode = $this->processArgumentList($arguments, $treeNode, $line, $lineBreak);
            if (null !== $lastProcessedNode) {
                $line = $lastProcessedNode->getData()->line;
            }
            $line->add(')');
        }
        return $lastProcessedNode;
    }

    /**
     * This method searches for a closure node in the arguments.
     * @param array $arguments
     */
    protected function hasClosure(array $arguments)
    {
        $closure = false;
        // only need to look if something was specified
        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                if (
                    $argument instanceof PHPParser_Node_Arg && $argument->value instanceof PHPParser_Node_Expr_Closure
                ) {
                    $closure = true;
                    break;
                } elseif (
                    $argument instanceof PHPParser_Node_Arg && $argument->value instanceof PHPParser_Node_Expr_FuncCall
                ) {
                    $closure = $this->hasClosure($argument->value->args);
                    if ($closure === true) {
                        break;
                    }
                }
            }
        }
        return $closure;
    }
}
