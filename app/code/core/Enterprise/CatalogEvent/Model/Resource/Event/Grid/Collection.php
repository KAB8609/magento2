<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event resource collection
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_CatalogEvent_Model_Resource_Event_Grid_Collection
    extends Enterprise_CatalogEvent_Model_Resource_Event_Collection
{
    /**
     * Add category data to collection select (name, position)
     *
     * @return Enterprise_CatalogEvent_Model_Resource_Event_Collection|Enterprise_CatalogEvent_Model_Resource_Event_Grid_Collection
     */
    protected function _initSelect()
  {
      parent::_initSelect();
      $this->addCategoryData();
      return $this;
  }
}

