<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Popular Tags Details grid collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Detail_Collection
    extends Mage_Tag_Model_Resource_Reports_Customer_Collection
{
    /**
     * @var Mage_Tag_Model_Tag
     */
    protected $_model;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Mage_Tag_Model_Tag $tagModel
     * @param Mage_Core_Controller_Request_Http $request
     * @param null $resource
     */
    public function __construct(
        Mage_Tag_Model_Tag $tagModel,
        Mage_Core_Controller_Request_Http $request,
        $resource = null)
    {
        $this->_model = $tagModel;
        $this->_request = $request;
        parent::__construct($resource);
    }

    /**
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }
    /**
     * @return Mage_Tag_Model_Resource_Customer_Collection|Mage_Tag_Model_Resource_Reports_Customer_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addStatusFilter($this->_model->getApprovedStatus())
            ->addTagFilter($this->getRequest()->getParam('id'))
            ->addProductToSelect();
        return $this;
    }
}
