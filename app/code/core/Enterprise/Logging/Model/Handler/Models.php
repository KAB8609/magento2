<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom handlers for models logging
 *
 */
class Enterprise_Logging_Model_Handler_Models
{
    /**
     * SaveAfter handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes or false if model wasn't modified
     */
    public function modelSaveAfter($model, $processor)
    {
        $data = $processor->cleanupData($model->getData());
        $origData = $processor->cleanupData($model->getOrigData());
        $isDiff = false;
        foreach ($data as $key=>$value){
            switch (true){
                case (isset($origData[$key]) && $value == $origData[$key]):
                    unset($data[$key]);
                    unset($origData[$key]);
                    break;
                case (isset($origData[$key]) && $value != $origData[$key]):
                case (!isset($origData[$key])):
                default:
                    $isDiff = true;
                    break;
            }
        }
        if ($isDiff){
            $processor->collectId($model);
            return Mage::getModel('enterprise_logging/event_changes')->setData(
                array(
                    'original_data' => $origData,
                    'result_data'   => $data,
                ));
        } else {
            return false;
        }
    }

    /**
     * Delete after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes
     */
    public function modelDeleteAfter($model, $processor)
    {
        $processor->collectId($model);
        $origData = $processor->cleanupData($model->getOrigData());
        return Mage::getModel('enterprise_logging/event_changes')
                    ->setData(array('original_data'=>$origData, 'result_data'=>null));
    }

    /**
     * MassUpdate after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return object Enterprise_Logging_Event_Changes
     */
    public function modelMassUpdateAfter($model, $processor)
    {
        return $this->modelSaveAfter($model, $processor);
    }

    /**
     * Load after handler
     *
     * @param object Mage_Core_Model_Abstract $model
     * @return Enterprise_Logging_Model_Event_Changes
     */
    public function modelViewAfter($model, $processor)
    {
        $processor->collectId($model);
        return Mage::getModel('enterprise_logging/event_changes')
            ->setData(array('original_data' => array(), 'result_data' => array()));
    }
}
