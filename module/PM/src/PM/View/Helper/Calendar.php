<?php
class Zend_View_Helper_Calendar
{
	function Calendar($calendar_data = FALSE, $base_url = FALSE, $date_key = FALSE, $link_rel = 'facebox')
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