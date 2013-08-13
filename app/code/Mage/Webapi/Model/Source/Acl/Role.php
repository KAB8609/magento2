<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Role source model.
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Source_Acl_Role implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role
     */
    protected $_resource = null;

    /**
     * Prepare required models.
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (isset($data['resource'])) {
            $this->_resource = $data['resource'];
        } else {
            $this->_resource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role');
        }
    }

    /**
     * Retrieve option hash of Web API Roles.
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionHash($addEmpty = true)
    {
        $options = $this->_getResourceModel()->getRolesList();
        if ($addEmpty) {
            $options = array('' => '') + $options;
        }
        return $options;
    }

    /**
     * Get roles resource model.
     *
     * @return Mage_Webapi_Model_Resource_Acl_Role
     */
    protected function _getResourceModel()
    {
        return $this->_resource;
    }

    /**
     * Return option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_getResourceModel()->getRolesList();
        return $options;
    }
}
