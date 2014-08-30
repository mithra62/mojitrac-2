<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use Michelf\MarkdownExtra;

/**
 * PM - Format HTML View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */
class FormatHtml extends BaseViewHelper
{
	public function __invoke($str)
	{
		return MarkdownExtra::defaultTransform($str);
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