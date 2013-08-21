<?php
class Zend_View_Helper_FormatHtml
{
	function FormatHtml($str)
	{
		return $this->makeLinks($str);
	}
	
	public function makeLinks($text) 
	{

        $in=array(
        '`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
        '`((?<!//)(www\.\S+[[:alnum:]]/?))`si'
        );
        $out=array(
        '<a href="$1" title="$1" target="_blank">$1</a> ',
        '<a href="http://$1" title="$1" target="_blank">$1</a>'
        );
        		
		$text = preg_replace($in,$out,$text);
		$text = nl2br($text);
		
		return $text;
	} 	
}