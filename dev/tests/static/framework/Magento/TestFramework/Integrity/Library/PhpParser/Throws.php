<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Integrity\Library\PhpParser;

/**
 * Parse throws and collect dependencies for it
 *
 * @package Magento\TestFramework
 */
class Throws implements Parser, DependenciesCollector
{
    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * Collect dependencies
     *
     * @var array
     */
    protected $dependencies = array();

    /**
     * Save throw token key
     *
     * @var array
     */
    protected $throws = array();

    /**
     * @param Tokens $tokens
     */
    public function __construct(Tokens $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @inheritdoc
     */
    public function parse($token, $key)
    {
        if (is_array($token) && $token[0] == T_THROW) {
            $this->throws[] = $key;
        }
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(Uses $uses)
    {
        foreach ($this->throws as $throw) {
            $class = '';
            if ($this->tokens->getTokenCodeByKey($throw + 2) == T_NEW) {
                $step = 4;
                while ($this->tokens->getTokenCodeByKey($throw+$step) == T_STRING
                    || $this->tokens->getTokenCodeByKey($throw+$step) == T_NS_SEPARATOR
                ) {
                    $class .= trim($this->tokens->getTokenValueByKey($throw + $step));
                    $step++;
                }
                if ($uses->hasUses()) {
                    $class = $uses->getClassNameWithNamespace($class);
                }
                $this->dependencies[] = $class;
            }
        }

        return $this->dependencies;
    }
}