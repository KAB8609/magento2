<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance
 */
namespace Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer;

class FinanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Remove test website
     */
    protected function tearDown()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $testWebsite Magento_Core_Model_Website */
        $testWebsite = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('Magento\ScheduledImportExport\Model\Website');
        if ($testWebsite) {
            // Clear test website info from application cache.
            \Mage::app()->clearWebsiteCache($testWebsite->getId());
        }
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     * @magentoDataFixture Magento/ScheduledImportExport/_files/website.php
     *
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_importData
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_updateRewardPoints
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_updateCustomerBalance
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_getComment
     */
    public function testImportData()
    {
        /**
         * Try to get test website instance,
         * in this case test website will be added into protected property of Application instance class.
         */
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var $testWebsite Magento_Core_Model_Website */
        $testWebsite = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('Magento\ScheduledImportExport\Model\Website');
        \Mage::app()->getWebsite($testWebsite->getId());

        // load websites to have ability get website code by id.
        $websiteCodes = array();
        /** @var $website \Magento\Core\Model\Website */
        foreach (\Mage::app()->getWebsites() as $website) {
            $websiteCodes[$website->getId()] = $website->getCode();
        }

        $userName = 'TestAdmin';
        $user = new \Magento\Object(array(
            'username' => $userName
        ));
        /** @var $session \Magento\Backend\Model\Auth\Session */
        $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);

        $pathToCsvFile = __DIR__ . '/../_files/customer_finance.csv';
        $expectedFinanceData = $this->_csvToArray(file_get_contents($pathToCsvFile));

        $source = new \Magento\ImportExport\Model\Import\Source\Csv($pathToCsvFile);
        $model = \Mage::getModel('Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance');
        $model->setParameters(
            array('behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE)
        );
        $model->setSource($source);
        $model->validateData();
        $model->importData();

        $rewardPointsKey =
            \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection::
                COLUMN_REWARD_POINTS;
        $customerBalanceKey =
            \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection::
                COLUMN_CUSTOMER_BALANCE;

        $customerCollection = \Mage::getResourceModel('Magento\Customer\Model\Resource\Customer\Collection');
        /** @var $customer \Magento\Customer\Model\Customer */
        foreach ($customerCollection as $customer) {
            $rewardCollection = \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward\Collection');
            $rewardCollection->addFieldToFilter('customer_id', $customer->getId());
            /** @var $rewardPoints \Magento\Reward\Model\Reward */
            foreach ($rewardCollection as $rewardPoints) {
                $websiteCode = $websiteCodes[$rewardPoints->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode][$rewardPointsKey];
                if ($expected < 0) {
                    $expected = 0;
                }
                $this->assertEquals(
                    $expected,
                    $rewardPoints->getPointsBalance(),
                    'Reward points value was not updated'
                );
            }

            $customerBalance = \Mage::getResourceModel('Magento\CustomerBalance\Model\Resource\Balance\Collection');
            $customerBalance->addFieldToFilter('customer_id', $customer->getId());
            /** @var $balance \Magento\CustomerBalance\Model\Balance */
            foreach ($customerBalance as $balance) {
                $websiteCode = $websiteCodes[$balance->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode][$customerBalanceKey];
                if ($expected < 0) {
                    $expected = 0;
                }
                $this->assertEquals(
                    $expected,
                    $balance->getAmount(),
                    'Customer balance value was not updated'
                );
            }
        }
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customers_for_finance_import_delete.php
     *
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_importData
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_deleteRewardPoints
     * @covers \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::_deleteCustomerBalance
     */
    public function testImportDataDelete()
    {
        /* clean up the database from prior tests before importing */
        $rewards  = \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward\Collection');
        foreach ($rewards as $reward) {
            $reward->delete();
        }

        $source = new \Magento\ImportExport\Model\Import\Source\Csv(
            __DIR__ . '/../_files/customer_finance_delete.csv'
        );
        $model = \Mage::getModel('Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance');
        $model->setParameters(
            array('behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE)
        );
        $model->setSource($source);
        $model->validateData();
        $model->importData();

        $rewards  = \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward\Collection');
        $balances = \Mage::getResourceModel('Magento\CustomerBalance\Model\Resource\Balance\Collection');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $expectedRewards = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ScheduledImportExport\Customers\ExpectedRewards');
        /** @var $reward Magento_Reward_Model_Reward */
        foreach ($rewards as $reward) {
            $this->assertEquals(
                $reward->getPointsBalance(),
                $expectedRewards[$reward->getCustomerId()][$reward->getWebsiteId()]
            );
        }

        $expectedBalances = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ScheduledImportExport\Customers\ExpectedBalances');
        /** @var $balance \Magento\CustomerBalance\Model\Balance */
        foreach ($balances as $balance) {
            $this->assertEquals(
                $balance->getAmount(),
                $expectedBalances[$balance->getCustomerId()][$balance->getWebsiteId()]
            );
        }
    }

    /**
     * Export CSV finance data to array
     *
     * @param string $content
     * @return array
     */
    protected function _csvToArray($content)
    {
        $emailKey = \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::COLUMN_EMAIL;
        $websiteKey = \Magento\ScheduledImportExport\Model\Import\Entity\Eav\Customer\Finance::COLUMN_FINANCE_WEBSITE;

        $header = array();
        $data = array();
        $lines = str_getcsv($content, "\n");
        foreach ($lines as $index => $line) {
            if ($index == 0) {
                $header = str_getcsv($line);
            } else {
                $row = array_combine($header, str_getcsv($line));
                if (!isset($data[$row[$emailKey]])) {
                    $data[$row[$emailKey]] = array();
                }
                $data[$row[$emailKey]][$row[$websiteKey]] = $row;
            }
        }
        return $data;
    }
}
