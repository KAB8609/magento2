<?php
/**
 * Catalog product link resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Link extends Mage_Core_Model_Mysql4_Abstract
{
	protected function  _construct() 
	{
		$this->_init('catalog/product_link', 'link_id');
	}
	
	
}// Class Mage_Catalog_Model_Entity_Product_Link END