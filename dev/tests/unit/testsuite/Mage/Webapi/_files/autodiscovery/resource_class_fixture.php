<?php
/**
 * Tests fixture for Auto Discovery functionality.
 *
 * Fake resource controller.
 *
 * @copyright {}
 */
class Vendor_Module_Webapi_ResourceController
{
    /**
     * @param Vendor_Module_Webapi_Customer_DataStructure[] $resourceData
     * @param bool $requiredField
     * @param string $optionalField
     * @param int $secondOptional
     * @return Vendor_Module_Webapi_Customer_DataStructure
     */
    public function createV1($resourceData, $requiredField, $optionalField = 'optionalField', $secondOptional = 1)
    {
        // Body is intentionally omitted
    }

    /**
     * @param int $resourceId
     * @param Vendor_Module_Webapi_Customer_DataStructure $resourceData
     * @param float $additionalRequired
     */
    public function updateV2($resourceId, $resourceData, $additionalRequired)
    {
        // Body is intentionally omitted
    }

    /**
     * @param int $resourceId
     * @return Vendor_Module_Webapi_Customer_DataStructure
     */
    public function getV2($resourceId)
    {
        // Body is intentionally omitted
    }

    /**
     * @apiDeprecated vendorModuleResource::listV3
     * @param float $additionalRequired
     * @param bool $optional
     * @return Vendor_Module_Webapi_Customer_DataStructure[]
     */
    public function listV2($additionalRequired, $optional = false)
    {
        // Body is intentionally omitted
    }

    public function listV3()
    {
        // Body is intentionally omitted
    }

    /**
     * @param int $deleteId The name of this parameter MUST be different from name used in get method.
     * @apiRemoved deleteV3
     */
    public function deleteV1($deleteId)
    {
        // Body is intentionally omitted
    }

    /**
     * @param int $deleteId The name of this parameter MUST be different from name used in get method.
     * @apiDeprecated Vendor_Module_Webapi_Resource_SubresourceController::deleteV3
     */
    public function deleteV2($deleteId)
    {
        // Body is intentionally omitted
    }

    /**
     * @apiDeprecated
     * @apiRemoved
     * @param int $deleteId The name of this parameter MUST be different from name used in get method.
     */
    public function deleteV3($deleteId)
    {
        // Body is intentionally omitted
    }

    /**
     * @apiDeprecated
     * @param int $deleteId The name of this parameter MUST be different from name used in get method.
     */
    public function deleteV4($deleteId)
    {
        // Body is intentionally omitted
    }

    /**
     * @param int $deleteId The name of this parameter MUST be different from name used in get method.
     */
    public function deleteV5($deleteId)
    {
        // Body is intentionally omitted
    }

    /**
     * @param Vendor_Module_Webapi_Customer_DataStructure[] $resourceData
     */
    public function multiUpdateV2($resourceData)
    {
        // Body is intentionally omitted
    }

    /**
     * @param int[] $idsToBeRemoved
     */
    public function multiDeleteV2($idsToBeRemoved)
    {
        // Body is intentionally omitted
    }

    /**
     * Test situation of internal method, that should not be exposed through API.
     */
    public function someMethodThatWillBeSkipped()
    {
        // Body is intentionally omitted
    }
}
