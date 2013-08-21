<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Zend_View_Helper_FileSize
{

    public function FileSize($size)
    {
    	$util = new LambLib_Controller_Action_Helper_Utilities;
    	return $util->filesize_format($size);
    }
    
}