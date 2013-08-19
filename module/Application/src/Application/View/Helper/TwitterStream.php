<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Zend_View_Helper_twitterStream
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function twitterStream()
    {
    	$twitter = new JT_Model_DbTable_Twitter_Tweet;
    	return $twitter->getTweetsByUser(73530251, 4);
        
    }
}