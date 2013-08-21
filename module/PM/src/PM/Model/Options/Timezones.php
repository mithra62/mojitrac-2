<?php
class PM_Model_Options_Timezones
{
	static public function tz()
	{
		return Zend_Locale::getTranslationList('WindowsToTimezone', 'en');
	}
}