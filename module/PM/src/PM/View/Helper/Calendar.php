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
    private $locale;
    private $now;
    private $date;
    private $monthNames = false;
    private $dayNames;
    private $validDates;
    private $numMonthDays;
    private $nextMonth;
    private $prevMonth;
    private $firstDayOfWeek;
    private $numWeeks;
    private $localeStr;
    public  $url_base = FALSE;
    public  $date_key = FALSE;
    public  $date_data = FALSE;
    public  $link_rel = 'facebox';
        
	public function __invoke($calendar_data = FALSE, $base_url = FALSE, $date_key = FALSE, $link_rel = 'facebox')
	{
		//set the locale
		$locale = "en_US";
		$base_date = null;

		if (array_key_exists('date', $_GET)) {
			$base_date = $_GET['date'];
		} 
		
		$this->url_base = $base_url;
		$this->date_key = $date_key;
		$this->date_data = $calendar_data;
		$this->link_rel = $link_rel;
		
		$this->setDate($base_date, $locale);
		
		$this->setValidDateRange(-12,24);
		//show the calendar HTML
		return $this->getCalendarHtml(
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
	
	private function initDateParams (Zend_Date $date)
	{
		$this->monthNames = $this->getMonthNames(); //locale month list
		$this->dayNames = Zend_Locale::getTranslationList('Day', $this->locale); //locale day list
		$this->setValidDateRange(); //locale valid dates
		$this->numMonthDays = $date->get(Zend_Date::MONTH_DAYS); //num days in locale month
		$this->setNextMonth($date); //the next month
		$this->setPrevMonth($date); //the previous month
		$this->firstDayOfWeek = $date->get(Zend_Date::WEEKDAY_DIGIT); //first day of the curr month
		$this->numWeeks = ceil(($this->getFirstDayOfWeek() + $this->getNumMonthDays()) / 7); //num weeks of curr month
	}
	
	private function setMonthNames()
	{
		$range = range(1,12);
		$return = array();
		foreach($range AS $key => $monthNum)
		{
			$return[$key+1] = date("F", mktime(0, 0, 0, $monthNum, 10));
		}
		 
		print_r($return);
		exit;
		return $return;
		print_r($range);
		exit;
	}
		
	/*
	 *
	*/
	public function setValidDateRange ($startOffset = -1, $endOffset = 12)
	{
		$this->validDates = array();
		$startDate = clone $this->now;
		$startMonth = $startDate->subMonth(abs($startOffset));
		$startNum = intval($startMonth->get("M"));
		$this->validDates[$startMonth->get("MMMM yyyy")] = $startMonth->get("MMM- yyyy");
		for ($i = $startNum; $i <= ($startNum + $endOffset); $i ++) {
			$str = $startMonth->addMonth(1)->get("MMMM yyyy");
			$this->validDates[$str] = $str;
		}
		unset($startDate);
		unset($startMonth);
		unset($startNum);
	}
	/*
	 *
	*/
	private function setNextMonth (Zend_Date $date)
	{
		$tempDate = clone $date;
		$this->nextMonth = $tempDate->addMonth(1);
		unset($tempDate);
	}
	/*
	 *
	*/
	private function setPrevMonth (Zend_Date $date)
	{
		$tempDate = clone $date;
		$this->prevMonth = $tempDate->subMonth(1);
		unset($tempDate);
	}
	/**
	 * @param $locale
	 */
	public function setLocale ($locale)
	{
		if (Zend_Locale::isLocale($locale)) {
			$this->locale = new Zend_Locale($locale);
			$this->date->setLocale($locale);
		} else {
			$this->locale = new Zend_Locale("en_US"); //default locale
			$this->date->setLocale("en_US");
		}
		//update the date params
		$this->initDateParams($this->date);
	}
	/**
	 * @return String
	 */
	public function getCalendarHeaderHtml ( $arr = NULL )
	{
		$showPrevMonthLink=false;
		$showNextMonthLink=false;
		$selectBox=false;
		$selectBoxName="selectMonth";
		$selectBoxFormName="selectMonthForm";
		if (is_array($arr))
			extract( $arr );
	
		//prev/next link in header display
		$pLink = $nLink = "";
		$pLinkClass = "id=\"prevMonth\" style=\"visibility: visible;\"";
		$nLinkClass = "id=\"nextMonth\" style=\"visibility: visible;\"";
		if ($showPrevMonthLink) {
			$t = $this->getPrevMonthAsDateString();
			if (! array_key_exists($t, $this->validDates)) //check if the prev month in list of valid dates
				$pLinkClass = "id=\"prevMonth\" style=\"visibility: hidden;\"";
			$pLink = "<a $pLinkClass href=\"?$selectBoxName=" . urlencode($t) . "\">&lt;&nbsp;$t</a>\n";
		}
		if ($showNextMonthLink) {
			$t = $this->getNextMonthAsDateString();
			if (! array_key_exists($t, $this->validDates)) //check if the next month in list of valid dates
				$nLinkClass = "id=\"nextMonth\" style=\"visibility: hidden;\"";
			$nLink = "<a $nLinkClass href=\"?$selectBoxName=" . urlencode($t) . "\">$t&nbsp;&gt;</a>\n";
		}
		//month in header display
		$headDate = $this->getDateAsString();
		if ($selectBox) {
			$headDate = "\n<form name=\"$selectBoxFormName\" method=\"get\">\n";
			$headDate .= $this->getValidDatesSelectBox(array('selectedDateStr'=>$this->getDateAsString(),
					'selectBoxName'=>$selectBoxName));
			$headDate .= "</form>\n";
		}
		return "<div id=\"calendar_header\">$pLink&nbsp;$headDate&nbsp;$nLink</div>\n";
	
	}
	/**
	* @return String
	 */
	 public function getCalendarBodyHtml ( $arr = NULL )
	 {
	 $showToday=false;
	 $tableClass="calendar";
	 if (is_array($arr))
	 extract($arr);
	 	
	 $html = '';
	
	 $html .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"$tableClass\">\n";
	 $html .= "<tr class=\"weekdays\">\n";
	
        //days of the week display
	 foreach ($this->dayNames as $dayShort=>$dayFull)
	 $html .= "<td class='header'>$dayFull</td>\n";
	 $html .= "</tr>\n";
	
	 //day numbers display
	 $today = $this->now->get("d");
	 $nowDate = $this->now->get("MMMM yyyy");
	 $focusDate = $this->getDateAsString();
	 $calDayNum = 1;
	
	 //day numbers display loop
	 for ($i = 0; $i < $this->getNumWeeks(); $i ++) {
	 $html .= "<tr class=\"days\">";
	 for ($j = 0; $j < 7; $j ++) {
	 $cellNum = ($i * 7 + $j);
	 $class = '';
	 	$m_date = FALSE;
	 			if($this->url_base)
	 	{
	 	$m_date = $this->getYear().'-'.(strlen($this->getMonthNum()) == 1 ? '0'.$this->getMonthNum() : $this->getMonthNum()).'-'.(strlen($calDayNum) == 1 ? '0'.$calDayNum : $calDayNum);
	 	}
	
	 		if ($showToday && $nowDate == $focusDate && $today == $calDayNum && $cellNum >= $this->getFirstDayOfWeek())
	 			$class = "class = \"today\"";
	 			$html .= "<td $class>";
	 			 
	 			$date = FALSE;
	 			if ($cellNum >= $this->getFirstDayOfWeek() && $cellNum < ($this->getNumMonthDays() + $this->getFirstDayOfWeek())) {
	 			$date = Zend_Locale_Format::toNumber($calDayNum, array('locale' => $this->localeStr));
	
	 			if($m_date && $this->date_key)
	 			{
	 			$date = '<a href="'.$this->url_base.'/'.$this->date_key.'/'.$m_date.'" rel="'.$this->link_rel.'">'.$date.'</a>';
	 			$html .= $date;
	 			$html .= $this->process_date_data($m_date);
	 			}
	 			$calDayNum ++;
	 			}
	
	 					$html .= "</td>\n";
	 			}
	 			$html .= "</tr>\n";
	 			}
	 			$html .= "</table>\n";
	 			return $html;
	 			}
	
	 			private function process_date_data($m_date)
	 			{
	 					if(!is_array($this->date_data))
	 					{
	 							return;
	 			}
	
	 			$stuff = '';
	 			if(array_key_exists($m_date, $this->date_data))
	 			{
	 			foreach($this->date_data[$m_date] AS $data)
	 			{
	 			$stuff .= '<br /><a href="'.$data['href'].'" rel="'.$data['rel'].'" title="'.strip_tags($data['string']).'">'.$data['string'].'</a>';
	 }
	 }
	
		return $stuff;
	 }
	
	 /**
	 * @return String
	 */
	 public function getCalendarHtml ( $arr = NULL )
	 {
	 $showToday=false;
	 $showPrevMonthLink=false;
	 $showNextMonthLink=false;
	 $tableClass="calendar";
	 $selectBox=false;
	 $selectBoxName="selectMonth";
	 $selectBoxFormName="selectMonthForm";
	 if (is_array($arr))
			extract ($arr);
					
		$html = "<div id=\"calendar_wrapper\">\n";
			$html .= $this->getCalendarHeaderHtml(array('showPrevMonthLink'=>$showPrevMonthLink,
	 'showNextMonthLink'=>$showNextMonthLink,
	 'selectBox'=>$selectBox,
	 'selectBoxName'=>$selectBoxName,
	 	'selectBoxFormName'=>$selectBoxFormName)); //returns a div
	 	$html .= "<div id=\"calendar_body\">\n";
	 			$html .= $this->getCalendarBodyHtml(array('showToday'=>$showToday,
	 			'tableClass'=>$tableClass)); //returns a table
	 			$html .= "</div>\n</div>\n";
	 			return $html;
    }
    /**
     * @return String
	     */
	     public function getValidDatesSelectBox ( $arr = NULL )
	     {
	     $selectedDateStr=false;
	     $selectBoxName="";
	     if (is_array($arr))
	     extract($arr);
	
        $html = "<select name=\"$selectBoxName\" class=\"select\" onchange=\"submit();\">\n";
	 			foreach ($this->validDates as $option => $value) {
	 			$sel = "";
	 			if ($selectedDateStr && $selectedDateStr == $option)
	 				$sel = "selected";
	 				$html .= "<option value=\"$option\" $sel>$value</option>\n";
	 }
	 $html .= "</select>\n";
	 return $html;
	}
	/**
	* @return Associative Array
	*/
	public function getValidDates ()
	{
	return $this->validDates;
	}
	/**
	* @return Array
	*/
    public function getMonthNames ()
    {
    	if(!$this->monthNames)
    	{
    		$this->monthNames = $this->setMonthNames();
    	}
    	
        return $this->monthNames;
    }
	/**
	* @return Array
	*/
	public function getDayNames ()
	{
	return $this->dayNames;
	}
	/**
	* @return Zend_Locale
	*/
	public function getLocale ()
	{
	return $this->locale;
	}
	/**
	* @return String
	*/
	public function getLocaleAsString ()
	{
	return $this->locale->toString();
	}
	/**
	* @return int
	*/
	public function getFirstDayOfWeek ()
	{
	return $this->firstDayOfWeek;
	}
	/**
	* @return Zend_Date
	*/
	public function getDate ()
	{
	return $this->date;
	}
	/**
	* @return String "MMMM yyyy"
		*/
			public function getDateAsString ()
			{
			return $this->date->get("MMMM yyyy");
	}
	/**
	* @param Zend_Date
	*/
	public function setDate ($date = null, $locale = "en_US")
	{
    	//locale
    	if (Zend_Locale::isLocale($locale)) {
    	   $this->now = Zend_Date::now($locale); //today
    	   $this->locale = new Zend_Locale($locale);
    	} else {
    	   $this->now = Zend_Date::now("en_US"); //today, default locale
    	   $this->locale = new Zend_Locale("en_US"); //default locale
    	}
    	//date
    	try {
    	   $this->date = new Zend_Date($date, "MMMM yyyy", $this->locale);
    	} catch (Zend_Date_Exception $zde) {
    	   $this->date = new Zend_Date(null, "MMMM yyyy", $this->locale);
    	}
    	//date params
    	$this->initDateParams($this->date);
	}
	/**
	* @return int
	*/
	public function getNumMonthDays ()
		{
			return $this->numMonthDays;
	}
	/**
	* @return String
	*/
	public function getMonthName ()
	{
	return $this->date->get("MMMM");
	}
		/**
		* @return String
     */
				public function getMonthShortName ()
				{
				return $this->date->get("MMM");
	}
	/**
	* @return int
			*/
	public function getMonthNum ()
		{
		return $this->date->get("MM");
	}
		/**
			* @return int
			*/
			public function getYear ()
			{
			return $this->date->get("yyyy");
		}
		/**
		* @return String
			*/
				public function getNextMonthName ()
			{
			return $this->nextMonth->get("MMMM");
		}
		/**
		* @return int
		*/
		public function getNextMonthNum ()
		{
		return $this->nextMonth->get("MM");
		}
		/**
     * @return int
	     */
	     public function getNextMonthYear ()
	     {
	     return $this->nextMonth->get("yyyy");
	     }
	     /**
	     * @return String "MMMM yyyy"
	     */
	     public function getNextMonthAsDateString ()
	     {
	     return $this->nextMonth->get("MMMM yyyy");
	}

	
    public function getPrevMonthName ()
	{
		return $this->prevMonth->get("MMMM");
	}
	/**
	* @return int
	*/
	public function getPrevMonthNum ()
	{
	return $this->prevMonth->get("MM");
	}
	/**
	* @return int
	*/
	public function getPrevMonthYear ()
	{
	return $this->prevMonth->get("yyyy");
	 }
	 
	 /**
	 * @return String
	 */
	 public function getPrevMonthAsDateString ()
	 	{
	 	return $this->prevMonth->get("MMMM yyyy");
	}
	/**
	* @return int
	 */
	 public function getNumWeeks ()
	 {
	 	return $this->numWeeks;
	 }	
}