<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Emulation model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_App_Emulation extends Varien_Object
{
    /**
     * Start environment emulation of the specified store
     *
     * Function returns information about initial store environment and emulates environment of another store
     *
     * @param integer $storeId
     * @param string $area
     * @param bool $emulateStoreInlineTranslation emulate inline translation of the specified store or just disable it
     *
     * @return Varien_Object information about environment of the initial store
     */
    public function startEnvironmentEmulation($storeId, $area = Mage_Core_Model_App_Area::AREA_FRONTEND,
        $emulateStoreInlineTranslation = false
    ) {
        if ($area === null) {
            $area = Mage_Core_Model_App_Area::AREA_FRONTEND;
        }
        $initialTranslateInline = $emulateStoreInlineTranslation
            ? $this->_emulateInlineTranslation($storeId, $area)
            : $this->_emulateInlineTranslation();
        $initialDesign = $this->_emulateDesign($storeId, $area);
        // Current store needs to be changed right before locale change and after design change
        Mage::getObjectManager()->get('Mage_Core_Model_StoreManager')->setCurrentStore($storeId);
        $initialLocaleCode = $this->_emulateLocale($storeId, $area);

        $initialEnvironmentInfo = new Varien_Object();
        $initialEnvironmentInfo->setInitialTranslateInline($initialTranslateInline)
            ->setInitialDesign($initialDesign)
            ->setInitialLocaleCode($initialLocaleCode);

        return $initialEnvironmentInfo;
    }

    /**
     * Stop enviromment emulation
     *
     * Function restores initial store environment
     *
     * @param Varien_Object $initialEnvironmentInfo information about environment of the initial store
     *
     * @return Mage_Core_Model_App_Emulation
     */
    public function stopEnvironmentEmulation(Varien_Object $initialEnvironmentInfo)
    {
        $this->_restoreInitialInlineTranslation($initialEnvironmentInfo->getInitialTranslateInline());
        $initialDesign = $initialEnvironmentInfo->getInitialDesign();
        $this->_restoreInitialDesign($initialDesign);
        // Current store needs to be changed right before locale change and after design change
        Mage::getObjectManager()->get('Mage_Core_Model_StoreManager')->setCurrentStore($initialDesign['store']);
        $this->_restoreInitialLocale($initialEnvironmentInfo->getInitialLocaleCode(), $initialDesign['area']);
        return $this;
    }

    /**
     * Emulate inline translation of the specified store
     *
     * Function disables inline translation if $storeId is null
     *
     * @param integer|null $storeId
     * @param string $area
     *
     * @return boolean initial inline translation state
     */
    protected function _emulateInlineTranslation($storeId = null, $area = Mage_Core_Model_App_Area::AREA_FRONTEND)
    {
        if (is_null($storeId)) {
            $newTranslateInline = false;
        } else {
            if ($area == Mage_Core_Model_App_Area::AREA_ADMIN) {
                $newTranslateInline = Mage::getStoreConfigFlag('dev/translate_inline/active_admin', $storeId);
            } else {
                $newTranslateInline = Mage::getStoreConfigFlag('dev/translate_inline/active', $storeId);
            }
        }
        /** @var $translateModel Mage_Core_Model_Translate */
        $translateModel = Mage::getObjectManager()->get('Mage_Core_Model_Translate');
        $initialTranslateInline = $translateModel->getTranslateInline();
        $translateModel->setTranslateInline($newTranslateInline);
        return $initialTranslateInline;
    }

    /**
     * Apply design of the specified store
     *
     * @param integer $storeId
     * @param string $area
     *
     * @return array initial design parameters(package, store, area)
     */
    protected function _emulateDesign($storeId, $area = Mage_Core_Model_App_Area::AREA_FRONTEND)
    {
        /** @var $objectManager Magento_ObjectManager */
        $objectManager = Mage::getObjectManager();

        /** @var $store Mage_Core_Model_StoreManager */
        $store = $objectManager->get('Mage_Core_Model_StoreManager')->getStore();

        $design = Mage::getDesign();
        $initialDesign = array(
            'area' => $design->getArea(),
            'theme' => $design->getDesignTheme(),
            'store' => $store
        );

        $storeTheme = $design->getConfigurationDesignTheme($area, array('store' => $storeId));
        $design->setDesignTheme($storeTheme, $area);

        if ($area == Mage_Core_Model_App_Area::AREA_FRONTEND) {
            $designChange = $objectManager->get('Mage_Core_Model_Design')->loadChange($storeId);
            if ($designChange->getData()) {
                $design->setDesignTheme($designChange->getDesign(), $area);
            }
        }

        return $initialDesign;
    }

    /**
     * Apply locale of the specified store
     *
     * @param integer $storeId
     * @param string $area
     *
     * @return string initial locale code
     */
    protected function _emulateLocale($storeId, $area = Mage_Core_Model_App_Area::AREA_FRONTEND)
    {
        /** @var $app Mage_Core_Model_App */
        $app = Mage::getObjectManager()->get('Mage_Core_Model_App');

        $initialLocaleCode = $app->getLocale()->getLocaleCode();
        $newLocaleCode = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
        $app->getLocale()->setLocaleCode($newLocaleCode);

        Mage::getObjectManager()->get('Mage_Core_Helper_Translate')
            ->initTranslate($newLocaleCode, $app->getArea($area), true)->init();
        return $initialLocaleCode;
    }

    /**
     * Restore initial inline translation state
     *
     * @param boolean $initialTranslateInline
     *
     * @return Mage_Core_Model_App_Emulation
     */
    protected function _restoreInitialInlineTranslation($initialTranslateInline)
    {
        $translateModel = Mage::getObjectManager()->get('Mage_Core_Model_Translate');
        $translateModel->setTranslateInline($initialTranslateInline);
        return $this;
    }

    /**
     * Restore design of the initial store
     *
     * @param array $initialDesign
     *
     * @return Mage_Core_Model_App_Emulation
     */
    protected function _restoreInitialDesign(array $initialDesign)
    {
        Mage::getDesign()->setDesignTheme($initialDesign['theme'], $initialDesign['area']);
        return $this;
    }

    /**
     * Restore locale of the initial store
     *
     * @param string $initialLocaleCode
     * @param string $initialArea
     *
     * @return Mage_Core_Model_App_Emulation
     */
    protected function _restoreInitialLocale($initialLocaleCode, $initialArea = Mage_Core_Model_App_Area::AREA_ADMIN)
    {
        /** @var $app Mage_Core_Model_App */
        $app = Mage::getObjectManager()->get('Mage_Core_Model_App');

        $app->getLocale()->setLocaleCode($initialLocaleCode);
        Mage::getObjectManager()->get('Mage_Core_Helper_Translate')
            ->initTranslate($initialLocaleCode, $app->getArea($initialArea), true)->init();
        return $this;
    }
}
