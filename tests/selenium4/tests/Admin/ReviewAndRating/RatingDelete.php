<?php

class Admin_ReviewAndRating_RatingDelete extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/reviewandrating');
        $this->setUiNamespace();
    }

    /**
     * Test Rating Delete
     */
    function testRating()
    {
        $reviewData = array(
            'search_rating_name' => 'Test Rating(Default Value)',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteRating($reviewData);
        }
    }

}