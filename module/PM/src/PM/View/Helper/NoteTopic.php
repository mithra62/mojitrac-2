<?php
class Zend_View_Helper_NoteTopic
{
	function NoteTopic($status)
	{
		return PM_Model_Options_Notes::translateTopicId($status); 
	}
}