<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customers collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Resource\Customer;

class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Name of collection model
     */
    const CUSTOMER_MODEL_NAME = 'Magento\Customer\Model\Customer';

    /**
     * @var \Magento\Object\Copy\Config
     */
    protected $_fieldsetConfig;

    /**
     * @var string
     */
    protected $_modelName;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Object\Copy\Config $fieldsetConfig
     * @param string $modelName
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Object\Copy\Config $fieldsetConfig,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_modelName = $modelName;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory
        );
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init($this->_modelName, 'Magento\Customer\Model\Resource\Customer');
    }

    /**
     * Group result by customer email
     *
     * @return \Magento\Customer\Model\Resource\Customer\Collection
     */
    public function groupByEmail()
    {
        $this->getSelect()
            ->from(
                array('email' => $this->getEntity()->getEntityTable()),
                array('email_count' => new \Zend_Db_Expr('COUNT(email.entity_id)'))
            )
            ->where('email.entity_id = e.entity_id')
            ->group('email.email');

        return $this;
    }

    /**
     * Add Name to select
     *
     * @return \Magento\Customer\Model\Resource\Customer\Collection
     */
    public function addNameToSelect()
    {
        $fields = array();
        $customerAccount = $this->_fieldsetConfig->getFieldset('customer_account');
        foreach ($customerAccount as $code => $field) {
            if (isset($field['name'])) {
                $fields[$code] = $code;
            }
        }

        $adapter = $this->getConnection();
        $concatenate = array();
        if (isset($fields['prefix'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{prefix}} IS NOT NULL AND {{prefix}} != \'\'',
                $adapter->getConcatSql(array('LTRIM(RTRIM({{prefix}}))', '\' \'')),
                '\'\'');
        }
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        $concatenate[] = '\' \'';
        if (isset($fields['middlename'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{middlename}} IS NOT NULL AND {{middlename}} != \'\'',
                $adapter->getConcatSql(array('LTRIM(RTRIM({{middlename}}))', '\' \'')),
                '\'\'');
        }
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';
        if (isset($fields['suffix'])) {
            $concatenate[] = $adapter
                    ->getCheckSql('{{suffix}} IS NOT NULL AND {{suffix}} != \'\'',
                $adapter->getConcatSql(array('\' \'', 'LTRIM(RTRIM({{suffix}}))')),
                '\'\'');
        }

        $nameExpr = $adapter->getConcatSql($concatenate);

        $this->addExpressionAttributeToSelect('name', $nameExpr, $fields);

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return \Magento\DB\Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->resetJoinLeft();

        return $select;
    }

    /**
     * Reset left join
     *
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::_getAllIdsSelect($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }
}
