<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests, that perform search of words, that signal of obsolete code
 */
class Legacy_WordsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Inspection_WordsFinder
     */
    protected static $_wordsFinder;

    public static function setUpBeforeClass()
    {
        self::$_wordsFinder = new Inspection_WordsFinder(
            glob(__DIR__ . '/_files/words_*.xml'),
            Utility_Files::init()->getPathToSource()
        );
    }

    /**
     * @param string $file
     * @dataProvider wordsDataProvider
     */
    public function testWords($file)
    {
        $words = self::$_wordsFinder->findWords($file);
        if ($words) {
            $this->fail('Found words: ' . implode(', ', $words));
        }
    }

    /**
     * @return array
     */
    public function wordsDataProvider()
    {
        return Utility_Files::init()->getAllFiles();
    }
}
