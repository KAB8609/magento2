<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class LineBreakCondition
{
    protected $condition;

    public function __construct($condition = null)
    {
        $this->condition = $condition;
    }

    public function process(&$tokens, $nextToken)
    {
        $token = $nextToken;
        // since condition is a string, only need to check if incoming token is a string
        if (is_string($nextToken)) {
            // if the next token starts with condition, then get rid of last token and accept his.
            if (strncmp($nextToken, $this->condition, strlen($this->condition)) === 0) {
                array_pop($tokens);
            }
        }
        return $token;
    }
}
