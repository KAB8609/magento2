<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TestFramework\Integrity\Library\PhpParser;

/**
 * Class know how create any parser
 *
 * @package Magento\TestFramework
 */
class ParserFactory
{
    /**
     * @var Parser[]
     */
    protected $parsers = array();

    /**
     * @var Uses
     */
    protected $uses;

    /**
     * @var StaticCalls
     */
    protected $staticCalls;

    /**
     * @var Throws
     */
    protected $throws;

    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * Return all parsers
     *
     * @param Tokens $tokens
     * @return Parser[]
     */
    public function createParsers(Tokens $tokens)
    {
        if (empty($this->parsers)) {
            $this->parsers = array(
                $this->uses        = new Uses(),
                $this->staticCalls = new StaticCalls($tokens),
                $this->throws      = new Throws($tokens),
            );
        }
        return $this->parsers;
    }

    /**
     * Get uses
     *
     * @return Uses
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Get static calls
     *
     * @return StaticCalls
     */
    public function getStaticCalls()
    {
        return $this->staticCalls;
    }

    /**
     * Get throws
     *
     * @return Throws
     */
    public function getThrows()
    {
        return $this->throws;
    }
}