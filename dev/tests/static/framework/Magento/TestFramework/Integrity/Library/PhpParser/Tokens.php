<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Integrity\Library\PhpParser;

/**
 * Parse php code and found dependencies
 *
 * @package Magento\TestFramework
 */
class Tokens
{
    /**
     * Collect all tokens
     *
     * @var array
     */
    protected $tokens = array();

    /**
     * Collect dependencies
     *
     * @var array
     */
    protected $dependencies = array();

    /**
     * Collect all parsers
     *
     * @var Parser[]
     */
    protected $parsers = array();

    /**
     * Parser factory for creating parsers
     *
     * @var ParserFactory
     */
    protected $parserFactory;

    /**
     * @param string $content
     * @param ParserFactory $parserFactory
     */
    public function __construct($content, ParserFactory $parserFactory)
    {
        $this->tokens = token_get_all($content);
        $this->parserFactory = $parserFactory;
    }

    /**
     * Parse content
     */
    public function parseContent()
    {
        foreach ($this->tokens as $k => $token) {
            foreach ($this->getParsers() as $parser) {
                $parser->parse($token, $k);
            }
        }
    }

    /**
     * Get all parsers
     *
     * @return Parser[]
     */
    protected function getParsers()
    {
        return $this->parserFactory->createParsers($this);
    }

    /**
     * Get parsed dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return array_merge(
            $this->parserFactory->getStaticCalls()->getDependencies($this->parserFactory->getUses()),
            $this->parserFactory->getThrows()->getDependencies($this->parserFactory->getUses())
        );
    }

    /**
     * Return previous token
     *
     * @param int $key
     * @param int $step
     * @return array
     */
    public function getPreviousToken($key, $step = 1)
    {
        return $this->tokens[$key - $step];
    }

    /**
     * Return token code by key
     *
     * @param $key
     * @return null|int
     */
    public function getTokenCodeByKey($key)
    {
        return is_array($this->tokens[$key]) ? $this->tokens[$key][0] : null;
    }

    /**
     * Return token value by key
     *
     * @param $key
     * @return string
     */
    public function getTokenValueByKey($key)
    {
        return is_array($this->tokens[$key]) ? $this->tokens[$key][1] : $this->tokens[$key];
    }
}