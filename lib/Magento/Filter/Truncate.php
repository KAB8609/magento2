<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

/**
 * Truncate filter
 *
 * Truncate a string to a certain length if necessary, appending the $etc string.
 * $remainder will contain the string that has been replaced with $etc.
 */
class Truncate implements \Zend_Filter_Interface
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $etc;

    /**
     * @var string
     */
    protected $remainder;

    /**
     * @var bool
     */
    protected $breakWords;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Stdlib\String $string
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     */
    public function __construct(
        \Magento\Stdlib\String $string,
        $length = 80,
        $etc = '...',
        &$remainder = '',
        $breakWords = true
    ) {
        $this->string = $string;
        $this->length = $length;
        $this->etc = $etc;
        $this->remainder = &$remainder;
        $this->breakWords = $breakWords;
    }

    /**
     * Filter value
     *
     * @param string $string
     * @return string
     */
    public function filter($string)
    {
        $length = $this->length;
        $this->remainder = '';
        if (0 == $length) {
            return '';
        }

        $originalLength = $this->string->strlen($string);
        if ($originalLength > $length) {
            $length -= $this->string->strlen($this->etc);
            if ($length <= 0) {
                return '';
            }
            $preparedString = $string;
            $preparedLength = $length;
            if (!$this->breakWords) {
                $preparedString = preg_replace(
                    '/\s+?(\S+)?$/u', '', $this->string->substr($string, 0, $length + 1)
                );
                $preparedLength = $this->string->strlen($preparedString);
            }
            $this->remainder = $this->string->substr($string, $preparedLength, $originalLength);
            return $this->string->substr($preparedString, 0, $length) . $this->etc;
        }

        return $string;
    }
}