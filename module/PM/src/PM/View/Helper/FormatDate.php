<?php
class Zend_View_Helper_FormatDate 
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        $this->util = new LambLib_Controller_Action_Helper_Utilities;
    }
    	
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	function FormatDate($date, $include_time = FALSE)
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