<?php
/*
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Bundle extends Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract {

    private $_filteredOptions = array();
    private $_productCounts = array();
    private $_optionCollections = array();

    public function prepareRequestForCart($_product)
    {
        $this->_product = $_product;
        $typeInstance = $this->_product->getTypeInstance();
	//var_dump($this->_product->getId());
	if($this->_product->hasData('_cache_instance_options_collection')) {
	    $this->_product->unsetData('_cache_instance_options_collection');
//	    print_r(count($this->_product->getData('_cache_instance_options_collection')));
	}
	$productId = $this->_product->getId();
//	echo "start\n";
	Magento_Profiler::start("option::collection::init");
	if(!isset($this->_optionCollections[$productId])) {
	    Magento_Profiler::start("option::collection::insider");
        $this->_optionCollections[$productId] = $typeInstance->getOptionsCollection($this->_product);
	    Magento_Profiler::stop("option::collection::insider");
	}
	Magento_Profiler::stop("option::collection::init");

	Magento_Profiler::start("selection::collection::init");
	if(!isset($this->_filteredOptions[$productId])) {
//	    echo "selectioninit\n";
//	    echo "load\n";

	    /*if (!count($optionCollection->getItems())) {
		return false;
	    }*/

	    $requiredOptionIds = array();
        $selectionCollection = $typeInstance->getSelectionsCollection(
            $this->_optionCollections[$productId]->getAllIds(),
            $this->_product
        );
	    $this->_filteredOptions[$productId] = array();
	    $productCounts = array();
	    foreach($selectionCollection->getItems() as $selection)
	    {
		if(!isset($this->_filteredOptions[$productId][$selection->getOptionId()]))
		    $this->_filteredOptions[$productId][$selection->getOptionId()] = array();
		$this->_filteredOptions[$productId][$selection->getOptionId()][] = $selection->getSelectionId();

		if(!isset($this->_productCounts[$selection->getSelectionId()])) {
		    $product = Mage::getModel('Mage_Catalog_Model_Product')->load($selection->getProductId());
		    $this->_productCounts[$selection->getSelectionId()] = $product->getStockItem()->getQty();
		}
	    }
	}
	Magento_Profiler::stop("selection::collection::init");
//	echo "finish\n";
	$request = array();
	if(!$typeInstance->isSalable($this->_product)) {
        return $request;
    }
	$request['product'] = $productId;
	$request['qty'] = 1;
	$request['bundle_option'] = array();
	$request['related_product'] = '';

	Magento_Profiler::start("filterd::options::foreach");
	foreach($this->_filteredOptions[$productId] as $optionId => $selectionIds)
	{
	    $option = $this->_optionCollections[$productId]->getItemById($optionId);
	    if($option->getRequired())
		$ifFill = 1;
	    else
		$ifFill = rand(0, 1);
	    if($ifFill)
	    {
		$countSelectionsToAdd = 1;
		if($option->isMultiSelection())
		    $countSelectionsToAdd =  rand(1, count($selectionIds));
		$selected = array_rand($selectionIds, $countSelectionsToAdd);
		if(!is_array($selected))
		    $selected = array($selected);
		foreach($selected as $id) {
		    if($option->isMultiSelection()) {
			if(!isset($request['bundle_option'][$optionId]))
			    $request['bundle_option'][$optionId] = array();
			    $request['bundle_option'][$optionId][] = $selectionIds[$id];
		    } else {
			$request['bundle_option'][$optionId] = $selectionIds[$id];
			$max = $this->_productCounts[$selectionIds[$id]];
			if(!isset($request['bundle_option_qty']))
			    $request['bundle_option_qty'] = array();
			$request['bundle_option_qty'][$optionId] = rand(1, min(10, $max));
		    }
		}
	    }
	}
	Magento_Profiler::stop("filterd::options::foreach");

	return new Varien_Object($request);
    }

}

