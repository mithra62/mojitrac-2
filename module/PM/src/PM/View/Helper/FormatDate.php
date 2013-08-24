<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

/**
 * PM - Format Date View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */
class FormatDate extends AbstractViewHelper
{   	
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	function __invoke($date, $include_time = FALSE)
	{	
		if ( '0000-00-00 00:00:00' == $date || '0000-00-00' == $date || null == $date) 
		{
			return 'N/A';
		} 
		else 
		{	
			$str_date = strtotime($date);
			$settings = Zend_Registry::get('pm_settings');
			if($settings['date_format'] == 'custom')
			{
				$settings['date_format'] = $settings['date_format_custom'];
			}
			
			if($settings['time_format'] == 'custom')
			{
				$settings['time_format'] = $settings['time_format_custom'];
			}

			if($settings['time_format'] == '')
			{
				$settings['time_format'] = 'g:i A';
			}
			
			if($settings['date_format'] == '')
			{
				$settings['date_format'] = 'F j, Y';
			}

			if(!$include_time)
			{
				$settings['time_format'] = '';
			}
			return $this->c($str_date).$this->util->formatDate($date, $settings['date_format'].' '.$settings['time_format']);
		}
	}
	
	private function c($str)
	{
		return '<!--'.$str.'-->';
	}
}