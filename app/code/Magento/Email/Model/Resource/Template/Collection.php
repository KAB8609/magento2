<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Templates collection
 *
 * @category    Magento
 * @package     Magento_Email
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Email\Model\Resource\Template;

class Collection extends  \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;

    /**
     * Define resource table
     *
     */
    public function _construct()
    {
        $this->_init('Magento\Email\Model\Template', 'Magento\Email\Model\Resource\Template');
        $this->_templateTable = $this->getMainTable();
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
