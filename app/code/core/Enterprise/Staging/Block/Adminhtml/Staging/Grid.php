<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging Manage Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingManageGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setColumnRenderers(
            array(
                'action' => 'Enterprise_Staging_Block_Adminhtml_Widget_Grid_Column_Renderer_Action'
        ));
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Collection')
            ->addWebsiteName()
            ->addLastLogComment();

        $this->setCollection($collection);

        parent::_prepareCollection();

        foreach($collection AS $staging) {
            $defaultStore = $staging->getStagingWebsite()->getDefaultStore();
            if ($defaultStore) {
                if ($defaultStore->isFrontUrlSecure()) {
                    $baseUrl = $defaultStore->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true);
                } else {
                    $baseUrl = $defaultStore->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                }
            } else {
                $baseUrl = '';
            }

            $collection->getItemById($staging->getId())
                ->setData("base_url", $baseUrl);
        }

        return $this;
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Website Name'),
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('base_url', array(
            'width'     => 250,
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('URL'),
            'index'     => 'base_url',
            'title'     => 'base_url',
            'length'    => '40',
            'type'      => 'action',
            'link_type' => 'url',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('status', array(
            'width'     => 100,
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('BugsCoverage'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array('started' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Processing'), 'completed' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Ready'))
        ));

        $this->addColumn('last_event', array(
            'width'     => 250,
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Latest Event'),
            'index'     => 'action',
            'sortable' => false,
            'filter'   => false,
            'renderer' => 'Enterprise_Staging_Block_Adminhtml_Staging_Grid_Renderer_Event',
            'options'   => Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getActionLabelsArray()
        ));

        $this->addColumn('created_at', array(
            'width'     => 100,
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Created At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'width'     => 100,
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Updated At'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'width'     => 80,
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'type',
            'link_type' => 'actions',
            'actions'   => array(
                //array(
                //    'url'       => $this->getUrl('*/*/edit', array('id' => '$staging_id')),
                //    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Edit')
                //),
                array(
                    'url'       => $this->getUrl('*/*/merge', array('id' => '$staging_id')),
                    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Merge'),
                    'validate'  => array(
                        '__method_callback' => array(
                            'method' => 'canMerge'
                    ))
                ),
                array(
                    'url'       => $this->getUrl('*/*/unschedule', array('id' => '$staging_id')),
                    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Unschedule'),
                    'validate'  => array(
                        '__method_callback' => array(
                            'method' => 'canUnschedule'
                    ))
                ),
                array(
                    'url'       => $this->getUrl('*/*/resetStatus', array('id' => '$staging_id')),
                    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Reset BugsCoverage'),
                    'validate'  => array(
                        '__method_callback' => array(
                            'method' => 'canResetStatus'
                    ))
                )
            )
        ));

        return $this;
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Return Row Url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId())
        );
    }
}
