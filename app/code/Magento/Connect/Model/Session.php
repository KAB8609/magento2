<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Auth session model
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * Connect data
     *
     * @var \Magento\Connect\Helper\Data
     */
    protected $_connectData;

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Zend\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Connect\Helper\Data $connectData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Zend\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Connect\Helper\Data $connectData,
        array $data = array()
    ) {
        $this->_connectData = $connectData;
        parent::__construct($context, $sidResolver, $sessionConfig, $data);
        $this->init('adminhtml');
    }

    /**
    * Retrieve parameters of extension from session.
    * Compatible with old version extension info file.
    *
    * @return array
    */
    public function getCustomExtensionPackageFormData()
    {
        $data = $this->getData('custom_extension_package_form_data');
        /* convert Maintainers to Authors */
        if (!isset($data['authors']) || count($data['authors']) == 0) {
            if (isset($data['maintainers'])) {
                $data['authors']['name'] = array();
                $data['authors']['user'] = array();
                $data['authors']['email'] = array();
                foreach ($data['maintainers']['name'] as $i => $name) {
                    if (!$data['maintainers']['name'][$i]
                        && !$data['maintainers']['handle'][$i]
                        && !$data['maintainers']['email'][$i]
                    ) {
                        continue;
                    }
                    array_push($data['authors']['name'], $data['maintainers']['name'][$i]);
                    array_push($data['authors']['user'], $data['maintainers']['handle'][$i]);
                    array_push($data['authors']['email'], $data['maintainers']['email'][$i]);
                }
                // Convert channel from previous version for entire package
                $helper = $this->_connectData;
                if (isset($data['channel'])) {
                    $data['channel'] = $helper->convertChannelFromV1x($data['channel']);
                }
                // Convert channel from previous version for each required package
                $nRequiredPackages = count($data['depends']['package']['channel']);
                for ($i = 0; $i < $nRequiredPackages; $i++) {
                    $channel = $data['depends']['package']['channel'][$i];
                    if ($channel) {
                        $data['depends']['package']['channel'][$i] = $helper->convertChannelFromV1x($channel);
                    }
                }
            }
        }

        /* convert Release version to Version */
        if (!isset($data['version'])) {
            if (isset($data['release_version'])) {
                $data['version'] = $data['release_version'];
            }
        }
        /* convert Release stability to Stability */
        if (!isset($data['stability'])) {
            if (isset($data['release_stability'])) {
                $data['stability'] = $data['release_stability'];
            }
        }
        /* convert contents */
        if (!isset($data['contents']['target'])) {
            $data['contents']['target'] = $data['contents']['role'];
        }
        return $data;
    }

}
