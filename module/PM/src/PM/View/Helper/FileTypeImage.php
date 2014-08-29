<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FileTypeImage.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

/**
 * PM - File Type Image View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/FileTypeImage.php
 */
class FileTypeImage extends AbstractViewHelper
{
    
    public function __invoke($mime)
    {
    	
    	switch($mime)
    	{
    		case 'text/plain':
    			$image = 'page_white_text.png';
    		break;
    		
    		case 'image/gif':
    			$image = 'picture.png';
    		break;
    		
    		case 'image/png':
    			$image = 'png.png';
    		break;    		
    		
    		case 'image/jpeg':
    		case 'image/jpg':
    			$image = 'jpg.png';
    		break;    		

    		case 'application/pdf':
    			$image = 'page_white_acrobat.png';
    		break;
    		
    		case 'application/vnd.ms-excel':
    			$image = 'page_excel.png';
    		break;
    		
    		case 'application/zip':
    		case 'application/x-bzip2':
    		case 'application/x-gzip':
    		case 'application/x-compressed':
    		case 'application/force-download':
    			$image = 'compress.png';
    		break;

    		case 'text/x-sql':
    			$image = 'doc_access.png';
    		break;
    		
    		case 'audio/mpeg':
    			$image = 'music.png';
    		break;    

    		case 'image/psd':
    			$image = 'psd.png';
    		break;     		
    		
    		case 'application/vnd.ms-powerpoint':
    			$image = 'page_white_powerpoint.png';
    		break;    		
    		
    		case 'application/x-shockwave-flash':
    			$image = 'page_white_flash.png';
    		break;    		

    		case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    		case 'application/msword':
    			$image = 'page_word.png';
    		break;    		
    		
    		default: 
    			$image = 'page_copy.png';
    		break;
    	}
    	
    	$image = '<img src="'.$this->view->StaticUrl().'/images/filetypes/'.$image.'" />';
        
        return $image;
    }
}