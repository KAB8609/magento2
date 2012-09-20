<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice Webapi renderer factory model.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// TODO: Check this class implementation (it was copied from Magento 1 implementation)
abstract class Mage_Webapi_Controller_Response_Rest_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @param array|string $acceptTypes
     * @throws Mage_Webapi_Exception
     * @throws Exception
     * @return Mage_Webapi_Controller_Response_Rest_Renderer_Interface
     */
    public static function factory($acceptTypes)
    {
        /** @var $helper Mage_Webapi_Helper_Rest */
        $helper = Mage::helper('Mage_Webapi_Helper_Rest');
        $adapters = $helper->getResponseRenderAdapters();

        if (!is_array($acceptTypes)) {
            $acceptTypes = array($acceptTypes);
        }

        $type = null;
        $adapterPath = null;
        foreach ($acceptTypes as $type) {
            foreach ($adapters as $item) {
                $itemType = (string)$item->type;
                if ($type == $itemType
                    || $type == current(explode('/', $itemType)) . '/*' || $type == '*/*'
                ) {
                    $adapterPath = (string)$item->model;
                    break 2;
                }
            }
        }

        //if server can't respond in any of accepted types it SHOULD send 406(not acceptable)
        if (null === $adapterPath) {
            throw new Mage_Webapi_Exception(
                'Server can not understand Accept HTTP header media type.',
                Mage_Webapi_Controller_Front_Rest::HTTP_NOT_ACCEPTABLE
            );
        }

        $adapter = Mage::getModel($adapterPath);
        if (!$adapter) {
            throw new Exception(sprintf('Response renderer adapter for content type "%s" not found.', $type));
        }

        return $adapter;
    }
}
