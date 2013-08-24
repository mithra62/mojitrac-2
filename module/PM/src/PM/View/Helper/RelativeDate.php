<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/RelativeDate.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

 /**
 * PM - Check Permission View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/RelativeDate.php
 */
class RelativeDate extends AbstractViewHelper
{
    	
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	function __invoke($date, $include_time = FALSE)
	{	
		return $date;
		if ( '0000-00-00 00:00:00' == $date || '0000-00-00' == $date || null == $date) 
		{
			return 'N/A';
		} 
		else 
		{	
			$str_date = strtotime($date);
			$prefs = array();
			if(Zend_Registry::isRegistered('pm_prefs'))
			{
				$prefs = Zend_Registry::get('pm_prefs');
				if(isset($prefs['enable_rel_time']) && $prefs['enable_rel_time'] == '0')
				{
					return $this->c($str_date).$this->view->formatDate($date);
				}
			}

			if(date('Y-m-d') == $date)
			{
				return $this->c($str_date).'Today';
			}
			
			$time_diff = time() - $str_date;
			

			if ( ($time_diff > 0 && $time_diff < (24*60*60)*7) || ($time_diff < 0 && $time_diff < (24*60*60)*7))
			{
				return $this->c($str_date).$this->relative_datetime($date); 
			}
			else
			{			
				return $this->c($str_date).$this->view->formatDate($date);
			}
		}
	}
	
	private function c($str)
	{
		return '<!--'.$str.'-->';
	}
}