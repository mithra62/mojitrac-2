<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Zend_View_Helper_FileTypeImage
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
    public function FileTypeImage($mime)
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