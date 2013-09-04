<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Format HTML View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */
class FormatHtml extends BaseViewHelper
{
	function __invoke($str)
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