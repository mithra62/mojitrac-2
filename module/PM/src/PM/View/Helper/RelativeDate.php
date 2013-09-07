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

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Relative Date View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/RelativeDate.php
 */
class RelativeDate extends BaseViewHelper
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
			$prefs = $this->getUserData();
			if(isset($prefs['enable_rel_time']) && $prefs['enable_rel_time'] == '0')
			{
				return $this->c($str_date).$this->view->formatDate($date);
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
				return $this->c($str_date).$this->formatDate($date, $prefs['date_format']);
			}
		}
	}
	
	/**
	 * Takes a time stamp (string) and converts it to a different format using date() strings
	 *
	 * @param   string  $oldDate	Original date string
	 * @param   string  $format		Converted date string
	 * @return  string				The new time stamp string
	 */
	public function formatDate($oldDate, $format) {
		$newDate = date($format, strtotime($oldDate));
		return $newDate;
	}

	function relative_datetime($timestamp)
	{
		if(!$timestamp)
		{
			return 'N/A';
		}
	
		$timestamp = (int)strtotime($timestamp);
	
		if($timestamp == '0')
		{
			return 'N/A';
		}
	
		$difference = time() - $timestamp;
		$periods = array("sec", "min", "hour", "day", "week","month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		$total_lengths = count($lengths);
	
		if ($difference > 0)
		{
			// this was in the past
			$ending = "ago";
		}
		else
		{
			// this was in the future
			$difference = -$difference;
			$ending = " from now";
		}
		//return;
	
		for($j = 0; $difference > $lengths[$j] && $total_lengths > $j; $j++)
		{
		$difference /= $lengths[$j];
		}
	
		$difference = round($difference);
		if($difference != 1)
		{
		$periods[$j].= "s";
		}
	
		$text = "$difference $periods[$j] $ending";
	
		return $text;
	}	
	
	private function c($str)
	{
		return '<!--'.$str.'-->';
	}
}