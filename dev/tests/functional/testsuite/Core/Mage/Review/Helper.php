<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Magento_Review_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Creates review
     *
     * @param array|string $reviewData
     */
    public function createReview($reviewData)
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        $this->clickButton('add_new_review');
        $product = (isset($reviewData['product_to_review'])) ? $reviewData['product_to_review'] : array();
        if (!$product) {
            $this->fail('Data for selecting product for review is not set');
        }
        $this->searchAndOpen($product, 'select_product_grid', false);
        $this->pleaseWait();
        $this->validatePage();
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Edit existing review
     *
     * @param array $reviewData
     * @param array $searchData
     */
    public function editReview(array $reviewData, array $searchData)
    {
        $this->openReview($searchData);
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Opens review
     *
     * @param array $reviewSearch
     */
    public function openReview(array $reviewSearch)
    {
        if (isset($reviewSearch['filter_websites']) && !$this->controlIsVisible('dropdown', 'filter_websites')) {
            unset($reviewSearch['filter_websites']);
        }
        $this->searchAndOpen($reviewSearch, 'all_reviews_grid');
    }

    /**
     * Fills tabs in new/edit review
     *
     * @param string|array $reviewData
     */
    public function fillInfo($reviewData)
    {
        if (isset($reviewData['visible_in'])) {
            if ($this->controlIsVisible('multiselect', 'visible_in')) {
                $this->fillMultiselect('visible_in', $reviewData['visible_in']);
                $this->execute(array('script' => "review.updateRating()", 'args' => array()));
                $this->pleaseWait();
            }
            unset($reviewData['visible_in']);
        }
        if (isset($reviewData['product_rating'])) {
            $this->fillRatings($reviewData['product_rating']);
        }
        $this->fillForm($reviewData);
    }

    /**
     * Fills ratings
     *
     * @param array $detailedRatings
     */
    public function fillRatings(array $detailedRatings)
    {
        if ($this->controlIsPresent('message', 'not_available_rating')) {
            $this->fail('Rating is not available for this store view');
        }
        foreach ($detailedRatings as $value) {
            if (!isset($value['rating_name']) || !isset($value['stars'])) {
                $this->fail('Incorrect data to fill');
            }
            $this->addParameter('ratingName', $value['rating_name']);
            $this->addParameter('stars', $value['stars']);
            $this->fillRadiobutton('detailed_rating', 'yes');
        }
    }

    /**
     * Open Review and delete
     *
     * @param array $searchData
     */
    public function deleteReview(array $searchData)
    {
        $this->openReview($searchData);
        $this->clickButtonAndConfirm('delete_review', 'confirmation_for_delete_single_review');
    }

    /**
     * Verify Review
     *
     * @param array|string $reviewData
     * @param array $skipFields
     */
    public function verifyReviewData($reviewData, $skipFields = array())
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        if (isset($reviewData['visible_in']) && !$this->controlIsVisible('multiselect', 'visible_in')) {
            $skipFields[] = 'visible_in';
        }
        $ratings = (isset($reviewData['product_rating'])) ? $reviewData['product_rating'] : array();
        $this->verifyForm($reviewData, '', $skipFields);
        foreach ($ratings as $ratingData) {
            $this->addParameter('ratingName', $ratingData['rating_name']);
            $this->addParameter('stars', $ratingData['stars']);
            $this->verifyForm(array('detailed_rating' => 'Yes'));
        }
        $this->assertEmptyVerificationErrors();
    }

    #********************************************
    #           Frontend Methods                *
    #********************************************

    /**
     * <p>Create Review</p>
     *
     * @param array|string $reviewData
     * @param bool $validateRating      In case $validateRating == TRUE - rating filling will be mandatory
     */
    public function frontendAddReview($reviewData, $validateRating = true)
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        $linkName = ($this->controlIsPresent('link', 'add_your_review')) ? 'add_your_review' : 'first_review';
        $this->defineCorrectParam($linkName);
        $this->clickControl('link', $linkName);
        $this->fillForm($reviewData);
        if (isset($reviewData['ratings'])) {
            $this->frontendAddRating($reviewData['ratings'], $validateRating);
        }
        $this->saveForm('submit_review');
    }

    /**
     * Filling In Rating
     *
     * @param array|string $ratingData
     * @param bool $validateRating
     */
    public function frontendAddRating($ratingData, $validateRating = true)
    {
        $ratingData = $this->fixtureDataToArray($ratingData);
        foreach ($ratingData as $value) {
            $this->addParameter('rateName', $value['rating_name']);
            $this->addParameter('rateId', $value['stars']);
            if ($this->controlIsPresent('radiobutton', 'select_rate')) {
                $this->fillRadiobutton('select_rate', 'Yes');
            } else {
                $this->addVerificationMessage('Rating with name ' . $value['rating_name'] . ' is not on the page');
            }
        }
        if ($validateRating) {
            $this->assertEmptyVerificationErrors();
        }
    }

    /**
     * Review verification after approve
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $verifyData
     * @param string $productName
     */
    public function frontVerifyReviewDisplaying(array $verifyData, $productName)
    {
        $this->addParameter('productName', $productName);
        $review = (isset($verifyData['review'])) ? $verifyData['review'] : '';
        $nickname = (isset($verifyData['nickname'])) ? $verifyData['nickname'] : '';
        $summary = (isset($verifyData['summary_of_review'])) ? $verifyData['summary_of_review'] : '';
        $rating = (isset($verifyData['product_rating'])) ? $verifyData['product_rating'] : array();
        $ratingNames = array();
        $actualRatings = array();
        foreach ($rating as $value) {
            $ratingNames[] = $value['rating_name'];
        }
        $this->assertTrue($this->controlIsVisible('link', 'reviews'), 'Product does not have approved review(s)');
        //Open reviews
        $this->defineCorrectParam('reviews');
        $this->clickControl('link', 'reviews');
        $this->addParameter('reviewerName', $nickname);
        $this->assertTrue($this->controlIsVisible('pageelement', 'review_reviewer_name'),
            'Customer with nickname \'' . $nickname . '\' does not added approved review');
        //Define actual review summary
        $actualSummary = $this->getControlAttribute('link', 'review_summary', 'text');
        //Define actual review text and rating names
        $text = preg_quote($this->getControlAttribute('pageelement', 'review_post_date', 'text'));
        $actualReview = $this->getControlAttribute('pageelement', 'review_details', 'text');
        $actualReview = trim(preg_replace('#' . $text . '#', '', $actualReview));
        if ($this->controlIsVisible('pageelement', 'review_details_ratings')) {
            $text = preg_quote($this->getControlAttribute('pageelement', 'review_details_ratings', 'text'));
            $actualReview = trim(preg_replace('#' . $text . '#', '', $actualReview), " \t\n\r\0\x0B");
            $elements = $this->getControlElements('pageelement', 'review_details_ratings');
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            foreach ($elements as $element) {
                if (count($this->getChildElements($element, '//tr[1]', false)) == 0) { //@TODO
                    continue;
                }
                $actualRatings[] = trim($this->getChildElement($element, '//tr[1]')->text());
            }
        }
        //Verification on product page
        $this->assertEquals($summary, $actualSummary,
            'Review Summary is not equal to specified: (' . $summary . ' != ' . $actualSummary . ')');
        $this->assertEquals($review, $actualReview,
            'Review Text is not equal to specified: (' . $review . ' != ' . $actualReview . ')');
        $this->assertEquals($ratingNames, $actualRatings, 'Review Rating names is not equal to specified');
        //Verification on Review Details page
        $this->clickControl('link', 'review_summary');
        $actualProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
        $actualReview = $this->getControlAttribute('pageelement', 'review_details', 'text');
        $this->assertSame($productName, $actualProductName,
            "'$productName' product not display on Review Details page");
        $this->assertSame($review, $actualReview, "'$review' review text not display on Review Details page");
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verification review on frontend
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $reviewData
     * @param string $productName
     */
    public function frontVerifyReviewDisplayingInMyAccount($reviewData, $productName)
    {
        //Verification in "My Account"
        $this->navigate('customer_account');
        $this->addParameter('productName', $productName);
        $this->assertTrue($this->controlIsPresent('link', 'product_name'),
            "Can not find product with name: $productName in My Recent Reviews block");
        $this->clickControl('link', 'product_name');
        $actualReview = $this->getControlAttribute('pageelement', 'review_details', 'text');
        $expectedReview = $reviewData['review'];
        $this->assertSame($expectedReview, $actualReview,
            "'$expectedReview' review text not display on Review Details page");
        //Verification in "My Account -> My Product Reviews"
        $this->navigate('my_product_reviews');
        $this->assertTrue($this->controlIsPresent('link', 'product_name'),
            "Can not find product with name: $productName in My Product Reviews block");
    }

    /**
     * Add parameter ReviewId
     *
     * @param string $linkName
     */
    public function defineCorrectParam($linkName)
    {
        $url = $this->getControlAttribute('link', $linkName, 'href');
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category', $url));
    }
}
