<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/Calendar.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * PM - Calendar View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/Calendar.php
 */
class Calendar extends AbstractHelper
{
	public function __invoke($calendar_data = FALSE, $base_url = FALSE, $date_key = FALSE, $link_rel = 'facebox')
	{
		//set the locale
		$locale = "en_US";
		$base_date = null;

		if (array_key_exists('date', $_GET)) {
			$base_date = $_GET['date'];
		} 
		
		$cal = new LambLib_Views_Helpers_Calendar($base_date, $locale);
		$cal->url_base = $base_url;
		$cal->date_key = $date_key;
		$cal->date_data = $calendar_data;
		$cal->link_rel = $link_rel;
		
		$cal->setValidDateRange(-12,24);
		//show the calendar HTML
		return $cal->getCalendarHtml(
			array('showToday'=>TRUE, 
				  'showPrevMonthLink'=>TRUE, 
				  'showNextMonthLink'=>TRUE, 
				  'tableClass'=>"calendar", 
				  'selectBox'=>TRUE, 
				  'selectBoxName'=>"date", 
				  'selectBoxFormName'=>"selectMonthForm"
			)
		);
		
	}
}