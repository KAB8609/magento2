<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Utility;

/**
 * Runs given callback across given array of data and collects all PhpUnit assertion results.
 * Should be used in case data provider is huge to minimize overhead.
 */
class AggregateInvoker
{
    /**
     * @var \PHPUnit_Framework_TestCase
     */
    protected $_testCase;

    /**
     * There is no PHPUnit internal API to determine whether --verbose or --debug options are passed.
     * When verbose is true, data sets are gathered for any result, includind incomplete and skipped test.
     * Only data sets for failed assertions are gathered otherwise.
     *
     * @var array
     */
    protected $_options = array(
        'verbose' => false,
    );

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array $options
     */
    public function __construct(
        \PHPUnit_Framework_TestCase $testCase,
        array $options = array()
    ) {
        $this->_testCase = $testCase;
        $this->_options = $options + $this->_options;
    }

    /**
     * Collect all failed assertions and fail test in case such list is not empty.
     * Incomplete and skipped test results are aggregated as well.
     *
     * @param callable $callback
     * @param array[] $dataSource
     */
    public function __invoke(callable $callback, array $dataSource)
    {
        $exceptionDumper = function (\Exception $exception, $dataSet) {
            $dataSet = $exception instanceof \PHPUnit_Framework_AssertionFailedError
                && !$exception instanceof \PHPUnit_Framework_IncompleteTestError
                && !$exception instanceof \PHPUnit_Framework_SkippedTestError
                || $this->_options['verbose']
                ? 'Data set: ' . var_export($dataSet, true) . PHP_EOL : '';
            return $dataSet . $exception->getMessage() . PHP_EOL
                . \PHPUnit_Util_Filter::getFilteredStacktrace($exception);
        };
        $results = [
            'PHPUnit_Framework_IncompleteTestError' => [],
            'PHPUnit_Framework_SkippedTestError' => [],
            'PHPUnit_Framework_AssertionFailedError' => [],
        ];
        $passed = 0;
        foreach ($dataSource as $dataSet) {
            try {
                call_user_func_array($callback, $dataSet);
                $passed++;
            } catch (\PHPUnit_Framework_IncompleteTestError $exception) {
                $results[get_class($exception)][] = $exceptionDumper($exception, $dataSet);
            } catch (\PHPUnit_Framework_SkippedTestError $exception) {
                $results[get_class($exception)][] = $exceptionDumper($exception, $dataSet);
            } catch (\PHPUnit_Framework_AssertionFailedError $exception) {
                $results['PHPUnit_Framework_AssertionFailedError'][] = $exceptionDumper($exception, $dataSet);
            }
        }
        $this->processResults($results, $passed);
    }

    /**
     * Analyze results of aggregated tests execution and complete test case appropriately
     *
     * @param array $results
     * @param int $passed
     */
    protected function processResults(array $results, $passed)
    {
        $totalCountsMessage = sprintf(
            'Passed: %d, Failed: %d, Incomplete: %d, Skipped: %d.',
            $passed,
            count($results['PHPUnit_Framework_AssertionFailedError']),
            count($results['PHPUnit_Framework_IncompleteTestError']),
            count($results['PHPUnit_Framework_SkippedTestError'])
        );
        if ($results['PHPUnit_Framework_AssertionFailedError']) {
            $this->_testCase->fail(
                $totalCountsMessage . PHP_EOL
                . implode(PHP_EOL, $results['PHPUnit_Framework_AssertionFailedError'])
            );
        }
        if (!$results['PHPUnit_Framework_IncompleteTestError'] && !$results['PHPUnit_Framework_SkippedTestError']) {
            return;
        }
        $message = $totalCountsMessage . PHP_EOL
            . implode(PHP_EOL, $results['PHPUnit_Framework_IncompleteTestError']) . PHP_EOL
            . implode(PHP_EOL, $results['PHPUnit_Framework_SkippedTestError']);
        if ($results['PHPUnit_Framework_IncompleteTestError']) {
            $this->_testCase->markTestIncomplete($message);
        } elseif ($results['PHPUnit_Framework_SkippedTestError']) {
            $this->_testCase->markTestSkipped($message);
        }
    }
}