<?php
/**
 * Limitation specification chain
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Limitation_Specification_Chain implements Saas_Saas_Model_Limitation_SpecificationInterface
{
    /**
     * @var array
     */
    protected $_specifications;

    /**
     * @param Saas_Saas_Model_Limitation_Specification_Factory $specificationFactory
     * @param array $specificationNames
     */
    public function __construct(
        Saas_Saas_Model_Limitation_Specification_Factory $specificationFactory,
        array $specificationNames = array()
    ) {
        foreach ($specificationNames as $specificationName) {
            $this->_specifications[] = $specificationFactory->create($specificationName);
        }
    }

    /**
     * Check is allowed functionality for the module
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isAllowed(Mage_Core_Controller_Request_Http $request)
    {
        /** @var $specification Saas_Saas_Model_Limitation_SpecificationInterface */
        foreach ($this->_specifications as $specification) {
            if (!$specification->isAllowed($request)) {
                return false;
            }
        }
        return true;
    }
}
