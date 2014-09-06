<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/View/Helper/FileSize.php
 */

namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Allows overriding the rendered partials a given view is using
 * 
 * Dispatches the event wrapper to override the partials being used to construct a page 
 *
 * @param	array	$partials	The partial views in the order they're to be rendered
 * @param	array 	$context	The view parameters
 * @package 	ViewHelpers\Views
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/FileSize.php
 */
class DispatchRouteEvents extends BaseViewHelper
{
	/**
	 * @ignore
	 */
    public function __invoke(array $partials = array(), array $context = array())
    {
    	return $partials;
    }
    
}