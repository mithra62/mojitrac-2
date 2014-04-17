<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Form/View/Helper/FormElementErrors.php
 */

namespace Base\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;

/**
 * Form Element Errors - View Helper
 * 
 * Sets the styling to use for ZF form errors
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Form/View/Helper/FormElementErrors.php
 */
class FormElementErrors extends OriginalFormElementErrors
{
	protected $messageCloseString     = '</li></ul>';
	protected $messageOpenFormat      = '<ul%s class="errors"><li>';
	protected $messageSeparatorString = '</li><li class="error">';
}