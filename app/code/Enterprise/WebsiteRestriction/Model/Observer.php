<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Private sales and stubs observer
 *
 */
class Enterprise_WebsiteRestriction_Model_Observer
{
    /**
     * Implement website stub or private sales restriction
     *
     * @param Magento_Event_Observer $observer
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller Magento_Core_Controller_Front_Action */
        $controller = $observer->getEvent()->getControllerAction();

        if (!Mage::app()->getStore()->isAdmin()) {
            $dispatchResult = new Magento_Object(array('should_proceed' => true, 'customer_logged_in' => false));
            Mage::dispatchEvent('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->getIsRestrictionEnabled()) {
                return;
            }
            /* @var $request Magento_Core_Controller_Request_Http */
            $request    = $controller->getRequest();
            /* @var $response Magento_Core_Controller_Response_Http */
            $response   = $controller->getResponse();
            switch ((int)Mage::getStoreConfig(Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE)) {
                // show only landing page with 503 or 200 code
                case Enterprise_WebsiteRestriction_Model_Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
                        return;
                    }
                    $httpStatus = (int)Mage::getStoreConfig(
                        Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_HTTP_STATUS
                    );
                    if (Enterprise_WebsiteRestriction_Model_Mode::HTTP_503 === $httpStatus) {
                        $response->setHeader('HTTP/1.1','503 Service Unavailable');
                    }
                    break;

                case Enterprise_WebsiteRestriction_Model_Mode::ALLOW_REGISTER:
                    // break intentionally omitted

                // redirect to landing page/login
                case Enterprise_WebsiteRestriction_Model_Mode::ALLOW_LOGIN:
                    if (!$dispatchResult->getCustomerLoggedIn() && !Mage::helper('Mage_Customer_Helper_Data')->isLoggedIn()) {
                        // see whether redirect is required and where
                        $redirectUrl = false;
                        $allowedActionNames = array_keys(Mage::getConfig()
                            ->getNode(Enterprise_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_GENERIC)
                            ->asArray()
                        );
                        if (Mage::helper('Mage_Customer_Helper_Data')->isRegistrationAllowed()) {
                            foreach(array_keys(Mage::getConfig()
                                ->getNode(
                                    Enterprise_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_REGISTER
                                )
                                ->asArray()) as $fullActionName
                            ) {
                                $allowedActionNames[] = $fullActionName;
                            }
                        }

                        // to specified landing page
                        $restrictionRedirectCode = (int)Mage::getStoreConfig(
                            Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_HTTP_REDIRECT
                        );
                        if (Enterprise_WebsiteRestriction_Model_Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = Mage::getStoreConfig(
                                Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_LANDING_PAGE
                            );
                            // Restrict access to CMS pages too
                            if (!in_array($controller->getFullActionName(), $allowedActionNames)
                                || ($controller->getFullActionName() === $cmsPageViewAction
                                    && $request->getAlias('rewrite_request_path') !== $pageIdentifier)
                            ) {
                                $redirectUrl = Mage::getUrl('', array('_direct' => $pageIdentifier));
                            }
                        } elseif (!in_array($controller->getFullActionName(), $allowedActionNames)) {
                            // to login form
                            $redirectUrl = Mage::getUrl('customer/account/login');
                        }

                        if ($redirectUrl) {
                            $response->setRedirect($redirectUrl);
                            $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                        }
                        if (Mage::getStoreConfigFlag(
                            Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = Mage::helper('Mage_Customer_Helper_Data')->getDashboardUrl();
                        } else {
                            $afterLoginUrl = Mage::getUrl();
                        }
                        Mage::getSingleton('Magento_Core_Model_Session')->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                    } elseif (Mage::getSingleton('Magento_Core_Model_Session')->hasWebsiteRestrictionAfterLoginUrl()) {
                        $response->setRedirect(
                            Mage::getSingleton('Magento_Core_Model_Session')->getWebsiteRestrictionAfterLoginUrl(true)
                        );
                        $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    }
                    break;
            }
        }
    }

    /**
     * Attempt to disallow customers registration
     *
     * @param Magento_Event_Observer $observer
     */
    public function restrictCustomersRegistration($observer)
    {
        $result = $observer->getEvent()->getResult();
        if ((!Mage::app()->getStore()->isAdmin()) && $result->getIsAllowed()) {
            $restrictionMode = (int)Mage::getStoreConfig(
                Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE
            );
            $result->setIsAllowed((!Mage::helper('Enterprise_WebsiteRestriction_Helper_Data')->getIsRestrictionEnabled())
                || (Enterprise_WebsiteRestriction_Model_Mode::ALLOW_REGISTER === $restrictionMode)
            );
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param Magento_Event_Observer $observer
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array((int)Mage::getStoreConfig(Enterprise_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE),
            array(
                Enterprise_WebsiteRestriction_Model_Mode::ALLOW_REGISTER,
                Enterprise_WebsiteRestriction_Model_Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }
}
