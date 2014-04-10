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

use Zend\View\Helper\AbstractHelper, DateTime, IntlDateFormatter, DateInterval;

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
	
	private function initDateParams ($date)
	{
		$this->monthNames = $this->getMonthNames(); //locale month list
		$this->dayNames = $this->getDayNames(); //locale day list
		$this->setValidDateRange(); //locale valid dates
		$this->numMonthDays = $this->date->format('t'); //num days in locale month
		$this->setNextMonth($date); //the next month
		$this->setPrevMonth($date); //the previous month
		$this->firstDayOfWeek = $this->date->format('w'); //first day of the curr month
		$this->numWeeks = ceil(($this->getFirstDayOfWeek() + $this->getNumMonthDays()) / 7); //num weeks of curr month
	}
	
	private function setMonthNames()
	{
		$range = range(1,12);
		$return = array();
		foreach($range AS $key => $monthNum)
		{
		    $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM');
			$return[$monthNum] = datefmt_format( $fmt , mktime(0,0,0,$monthNum,1,date('Y')));
		}

		return $return;
	}
	
	private function setDayNames()
	{
	    $range = range(1,7);
	    $return = array();
	    foreach($range AS $key => $dayNum)
	    {
	    	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'eee');
	    	$key = strtolower(datefmt_format( $fmt , mktime(0,0,0,1,$dayNum,date('Y'))));
	    	
	    	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'EEEE');
	    	$return[$key] = datefmt_format( $fmt , mktime(0,0,0,1,$dayNum,date('Y')));
	    }

	    return $return;
	}
	
	/**
	 * Sets the date range the calendar uses
	 * @param int $startOffset
	 * @param int $endOffset
	 */
	public function setValidDateRange ($startOffset = -1, $endOffset = 12)
	{
		$this->validDates = array();
		$startDate = clone $this->now;
		$abs = abs($startOffset);
		$startMonth = $startDate->add(DateInterval::createFromDateString($startOffset.' month'));

		$startNum = intval($startMonth->format("m"));
		$key_fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
		$value_fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMM- yyyy');
		$key = datefmt_format( $key_fmt , strtotime($startMonth->format('r')));
		
		$this->validDates[$key] = datefmt_format( $key_fmt , strtotime($startMonth->format('r')));
		for ($i = $startNum; $i <= ($startNum + $endOffset); $i ++) {
		    
		    $month = $startDate->add(DateInterval::createFromDateString('1 month'));
		    $str = datefmt_format( $key_fmt , strtotime($month->format('r')));
			//$str = $startMonth->addMonth(1)->get("MMMM yyyy");
			$this->validDates[$str] = $str;
		}
		unset($startDate);
		unset($startMonth);
		unset($startNum);
	}

	private function setNextMonth (DateTime $date)
	{
		$tempDate = clone $date;
		$this->nextMonth = $tempDate->add(DateInterval::createFromDateString('1 month'));
		unset($tempDate);
	}

	private function setPrevMonth (DateTime $date)
	{
		$tempDate = clone $date;
		$this->prevMonth = $tempDate->sub(DateInterval::createFromDateString('1 month'));
		unset($tempDate);
	}

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
	
	 //day numbers displaydate
	 $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'd');
	 $today = datefmt_format($fmt, strtotime($this->now->format('r')));
	 
	 $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	 $nowDate = datefmt_format($fmt, strtotime($this->now->format('r')));
	 $focusDate = $this->getDateAsString();
	 $calDayNum = 1;
	
		 //day numbers display loop
		 for ($i = 0; $i < $this->getNumWeeks(); $i ++) 
		 {
			 $html .= "<tr class=\"days\">";
			 for ($j = 0; $j < 7; $j ++) 
			 {
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
	 			$date = $calDayNum;// Zend_Locale_Format::toNumber($calDayNum, array('locale' => $this->localeStr));
		
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
        $html .= $this->getCalendarHeaderHtml(
            array(
                'showPrevMonthLink'=>$showPrevMonthLink,
                'showNextMonthLink'=>$showNextMonthLink,
                'selectBox'=>$selectBox,
                'selectBoxName'=>$selectBoxName,
                'selectBoxFormName'=>$selectBoxFormName)
        ); 
        
        $html .= "<div id=\"calendar_body\">\n";
        $html .= $this->getCalendarBodyHtml(array('showToday'=>$showToday,
        'tableClass'=>$tableClass)); //returns a table
        $html .= "</div>\n</div>\n";

        return $html;
    }

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
        if(!$this->dayNames)
        {
        	$this->dayNames = $this->setDayNames();
        }
	    	    
        return $this->dayNames;
	}

	
	public function getLocale ()
	{
        return $this->locale;
	}
	
	public function getLocaleAsString ()
	{
        return $this->locale->toString();
	}
	
	public function getFirstDayOfWeek ()
	{
        return $this->firstDayOfWeek;
	}
	
	public function getDate ()
	{
        return $this->date;
	}

    public function getDateAsString ()
    {
        $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
        return datefmt_format($fmt, strtotime($this->date->format('r')));        
    }
    
	public function setDate ($date = null, $locale = "en_US")
	{
    	$this->now = new DateTime();
    	$this->locale = $locale; 
    	
    	//date
    	try {
    	   $this->date = new DateTime(strtotime($date));
    	} catch (Exception $zde) {
    	   $this->date = new DateTime(time());
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
	
	public function getMonthName ()
	{
        return $this->date->format('F');
	}

    public function getMonthShortName ()
	{
        return $this->date->format("M");
	}
	
	public function getMonthNum ()
	{
	    return $this->date->format("m");
	}

    public function getYear ()
	{
        return $this->date->format("y");
	}
	
    public function getNextMonthName ()
	{
		return $this->nextMonth->format("F");
	}

	public function getNextMonthNum ()
	{
		return $this->nextMonth->format("m");
	}

     public function getNextMonthYear ()
     {
     	return $this->nextMonth->format("y");
     }
	     
    public function getNextMonthAsDateString ()
	{
	    $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	    return datefmt_format($fmt, strtotime($this->nextMonth->format('r')));
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
	 	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	 	return datefmt_format($fmt, strtotime($this->prevMonth->format('r')));
	}
	/**
	* @return int
	 */
	 public function getNumWeeks ()
	 {
	 	return $this->numWeeks;
	 }	
}