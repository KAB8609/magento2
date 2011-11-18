<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Catalog_Model_Convert_Adapter_Catalog
    extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Convert');
        }
        return $this->_resource;
    }

    public function load()
    {
        $res = $this->getResource();

        $this->setData(array(
            'Products' => $res->exportProducts(),
            'Categories' => $res->exportCategories(),
            'Image Gallery' => $res->exportImageGallery(),
            'Product Links' => $res->exportProductLinks(),
            'Products in Categories' => $res->exportProductsInCategories(),
            'Products in Stores' => $res->exportProductsInStores(),
            'Attributes' => $res->exportAttributes(),
            'Attribute Sets' => $res->exportAttributeSets(),
            'Attribute Options' => $res->exportAttributeOptions(),
        ));

        return $this;
    }

    public function save()
    {
        /*
        $res = $this->getResource();

        foreach (array('Attributes', 'Attribute Sets', 'Attribute Options', 'Products', 'Categories', ''))

        $this->setData

        echo "<pre>".print_r($this->getData(),1)."</pre>";

        */
        return $this;
    }
}
