<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FileSize.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - FileSize View Helper
 *
 * @package 	ViewHelpers\Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/FileSize.php
 */
class FileSize extends BaseViewHelper
{

    public function __invoke($size)
    {
    	return $this->filesizeFormat($size);
    }
    
}