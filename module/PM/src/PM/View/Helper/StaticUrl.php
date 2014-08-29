<?php
class Zend_View_Helper_StaticUrl
{
	function StaticUrl()
	{
		switch(APPLICATION_ENV)
		{
			case 'production':
			default:
				return '';
			break;
			
			case 'development':
				return '';
			break;
		}
	}
}