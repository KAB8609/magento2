<?php
/**
 * Issue tag
 *
 * @package phpDocumentorCustom
 */
class parserIssueTag extends parserTagCustom
{
    /**
     * Tag keyword
     * @var string
     */
    public $keyword = 'issue';

    /**
     * Command line options array
     * @var array
     */
    public $phpDocOptions = array(
        'issue-baseurl' => array(
            'tag'  => array('--issue-baseurl'),
            'desc' => 'base url for links generated from @issue tag',
            'type' => 'value',
        ),
    );

    /**
     * Tag parser
     * 
     * @param string $keyword
     * @param mixed $value
     */
    function parserIssueTag($keyword, $value)
    {
        $this->generateUrl($keyword, $value, 'issue-baseurl');
    }
}
