<?php
class Zend_View_Helper_StaticUrl
{
	function StaticUrl()
	{
		switch(APPLICATION_ENV)
		{
			case 'production':
			default:
				return 'http://static.mojitrac.com';
			break;
			
			case 'development':
				return '';
			break;
		}
	}
}