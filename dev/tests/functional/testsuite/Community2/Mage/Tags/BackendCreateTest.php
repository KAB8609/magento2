<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'TagsFixtureAbstract.php';
/**
 * Tag creation tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Tags_BackendCreateTest extends Community2_Mage_Tags_TagsFixtureAbstract
{
    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        return parent::_preconditionsForAllTagsTests();
    }

    /**
     * Pending status for new customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @author roman.grebenchuk
     * @dataProvider tagNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2327
     */
    public function addFromFrontendTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $tags = $this->tagsHelper()->_convertTagsStringToArray($tags);
        $this->loginAdminUser();
        if ($status != 'Pending') {
            $this->navigate('pending_tags');
            foreach ($tags as $tag) {
                $this->tagsHelper()->changeTagsStatus(array(array('tag_name' => $tag)), $status);
            }
        }
    }

    public function tagNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved'),
            array($this->generate('string', 4, ':alpha:'), 'Disabled'),
            array($this->generate('string', 4, ':alpha:'), 'Pending')
        );
    }

    /**
     * Edit pending customer's tag
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @author roman.grebenchuk
     * @dataProvider tagEditDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2344
     */
    public function editTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->tagsHelper()->openTag(array('tag_name' => $tags));
        $this->fillDropdown('tag_status', $status);
        $this->saveForm('save_tag');
        $this->assertMessagePresent('success', 'success_saved_tag');
        $this->assertNull($this->search(array('tag_name' => $tags), 'tags_grid'),
            "Edit tags and change status {$tags} work incorrect");
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
    }

    public function tagEditDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Approved')
        );
    }

    /**
     * Search customers tags in All Tags and Pending Tags grid
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @author roman.grebenchuk
     * @dataProvider tagSearchSelectedDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2365
     */
    public function searchSelectedTags($tags, $status, $testData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
        $this->productHelper()->frontOpenProduct($testData['simple']);
        //Steps
        $this->tagsHelper()->frontendAddTag($tags);
        //Verification
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        $this->loginAdminUser();
        $areas = array('pending_tags', 'all_tags');
        foreach ($areas as $area) {
            $this->navigate($area);
            $this->searchAndChoose(array('tag_name' => $tags), 'tags_grid');
            $this->fillDropdown('filter_massaction', $status);
            $this->clickButton('search', false);
            $this->pleaseWait();
            $trLocator = $this->formSearchXpath(array('tag_name' => $tags));
            if ($status == 'No') {
                $this->assertFalse($this->elementIsPresent($trLocator), "Filter {$status} works incorrect");
            } else {
                $this->assertTrue((bool)$this->elementIsPresent($trLocator), "Filter {$status} works incorrect");
            }
        }
    }

    public function tagSearchSelectedDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), 'Any'),
            array($this->generate('string', 4, ':alpha:'), 'No'),
            array($this->generate('string', 4, ':alpha:'), 'Yes')
        );
    }

    /**
     * Search customers tags in All Tags and Pending Tags grid
     *
     * @param string $tags
     * @param string $status
     * @param array $testData
     *
     * @test
     * @author roman.grebenchuk
     * @dataProvider tagSearchNameDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2365
     */
    public function searchNameTags($tags, $status, $testData)
    {
        if ($status) {
            //Setup
            $this->customerHelper()->frontLoginCustomer($testData['user'][1]);
            $this->productHelper()->frontOpenProduct($testData['simple']);
            //Steps
            $this->tagsHelper()->frontendAddTag($tags);
            //Verification
            $this->assertMessagePresent('success', 'tag_accepted_success');
            $this->tagsHelper()->frontendTagVerification($tags, $testData['simple']);
        }
        $this->loginAdminUser();
        $areas = array('pending_tags', 'all_tags');
        foreach ($areas as $area) {
            $this->navigate($area);
            $this->clickButton('reset_filter', false);
            $this->pleaseWait();
            $this->fillField('tag_name', $tags);
            $this->clickButton('search', false);
            $this->pleaseWait();
            $trLocator = $this->formSearchXpath(array('tag_name' => $tags));
            $this->assertTrue((bool)$this->elementIsPresent($trLocator), "Filter by Name {$tags} works incorrect");
        }
    }

    public function tagSearchNameDataProvider()
    {
        return array(
            array($this->generate('string', 4, ':alpha:'), true),
            array($this->generate('string', 4, ':alpha:'), false)
        );
    }
}