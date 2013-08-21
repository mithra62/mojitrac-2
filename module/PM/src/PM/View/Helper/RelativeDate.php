<?php
class Zend_View_Helper_RelativeDate extends LambLib_Controller_Action_Helper_Utilities
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    	
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	function RelativeDate($date, $include_time = FALSE)
	{	
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