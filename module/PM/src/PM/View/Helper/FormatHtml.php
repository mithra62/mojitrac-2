<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

/**
 * PM - Format Date View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */
class FormatHtml extends AbstractViewHelper
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