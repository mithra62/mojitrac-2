<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Image.php
*/

namespace Application\Model;

/**
 * Image Model
 *
 * @package 	Image
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/Image.php
*/
class Image
{
	/**
	 * The actual image manager
	 * @var \Intervention\Image\ImageManager
	 */
	private $manager = null;
	
	/**
	 * @ignore
	 * @param \Intervention\Image\ImageManager $image_manager
	 */
	public function __construct(\Intervention\Image\ImageManager $image_manager = null)
	{
		$this->manager = $image_manager;
	}
	
	/**
	 * The permissible image formats we can use
	 * @var array
	 */
	private $formats = array(
		'gd' => array('jpeg', 'png', 'gif', 'tif', 'bmp', 'ico', 'psd'),
		'imagick' => array('jpeg', 'png', 'gif')
	);

	/**
	 * Handles the processing of the images
	 * @param string $name
	 * @param string $path
	 */
	public function processImage($name, $path, $image_data = false)
	{
		if(!$image_data)
		{
			$image_data = getimagesize($path.DS.$name);
		}
	
		if('image/psd' == $image_data['mime'])
		{
			$name = $this->convertPsd($path, $name, 'jpg');
		}
	
		$this->resize($path.DS.$name, $path.DS.'mid_'.$name, 700, false);
		$this->resize($path.DS.$name, $path.DS.'tb_'.$name, 100, false);
	}
	
	/**
	 * Determines what the preview size should be
	 * @param string $view_size
	 * @return string
	 */
	public function getPreviewSize($view_size)
	{
		switch($view_size)
		{
			case 'mid':
			case 'tb':
				$view_size = $view_size.'_';
				break;
					
			default:
				$view_size = 'mid_';
				break;
		}
		return $view_size;
	}	

	/**
	 * Takes a file and returns the format / type
	 *
	 * @param	string	$image_name		Full path to the file
	 * @global	array	$vars			System settings; used for ImageMagickPath
	 * @return	string
	 */
	public function identify($image_name)
	{	
		$exec_str= $this->convert." identify -verbose $image_name | grep 'Geometry\|Resolution\|Format\|Filesize'";
		$image_resize_exe = exec($exec_str, $output);
		return $output;	
	}
	
	/**
	 * Resizes an image to specified height and width
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$dest_name		Full path to saved file
	 * @param	int		$t_width		Width of the new image
	 * @param	int		$t_height		Height of new image
	 * @return	void
	 */
	public function resize($image_name, $dest_name, $t_width = 0, $t_height = 0)
	{
		$stats = $this->getSize($image_name);
		if($stats['height'] < $t_height && $stats['width'] < $t_width)
		{
			copy($image_name, $dest_name);
			return;
		}
		
		$image = $this->manager->make($image_name)->resize($t_width, $t_height, function ($constraint) {
		    $constraint->aspectRatio();
		});
		
		$image->save($dest_name);
	}
	
	/**
	 * Resizes a psd to specified height and width
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$type			Full path to saved file
	 * @return	void
	 */
	public function convertPsd($file_path, $file_name, $type)
	{
		$new_name = str_replace('.psd', '.'.str_replace('.','', $type), $file_name);
		$exec_str= $this->convert." -quality 95 -flatten ".$file_path.DS.$file_name."[0] " . $file_path.DS.$new_name;
		$image_resize_exe = system($exec_str, $output);
		return $new_name;
	}	
	
	/**
	 * Returns an array with the image data
	 * @param string $image
	 * @return array
	 */
	public function getSize($image)
	{
		$s = array();
		if(file_exists($image))
		{
			list($s['width'], $s['height'], $s['type'], $s['attr']) = getimagesize($image);
		}
		
		return $s;
	}
	
	/**
	 * Changes an image from one format to another (gif => jpg, jpg => png, etc...)
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$dest_name		Full path to saved file
	 * @global	array	$vars			System settings; used for ImageMagickPath
	 * @return	void
	 */
	public function changeFormat($image_name, $dest_name)
	{
		$exec_str= $this->convert." $image_name $dest_name";
		$image_resize_exe = system($exec_str, $output);
	} 
	
	/**
	 * Rotates an image by passed degrees
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$dest_name		Full path to saved file
	 * @param	int		$degree			Rate to rotate an image by
	 * @global	array	$vars			System settings; used for ImageMagickPath
	 * @return	void
	 */
	public function rotate($degree, $image_name, $dest_name)
	{
		$exec_str= $this->convert." convert -rotate $degree $image_name $dest_name";
		$image_resize_exe = system($exec_str, $output);
	} 
	
	/**
	 * Crops an image to meet passed dimensions squared
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$dest_name		Full path to saved file
	 * @return	void
	 */
	public function shave($image_name, $dest_name)
	{
		$stats = $this->get_image_size($image_name);
		if($stats['width'] >= $stats['height'])
		{	
			$exec_str = $this->convert." -shave " .round(($stats['width']-$stats['height'])/2) . "x0 \"$image_name\" \"$dest_name\" ";								
		}
		if($stats['width'] < $stats['height'])
		{
			$exec_str = $this->convert." -shave 0x" . round(($stats['height']-$stats['width'])/2) . " \"$image_name\" \"$dest_name\" ";
		}
		
		$image_resize_exe = system($exec_str, $output);	
	}
	
	/**
	 * Crops an image to meet passed dimensions squared
	 *
	 * @param	string	$image_name		Full path to orignal file
	 * @param	string	$dest_name		Full path to saved file
	 * @param	string	$Text			String to write to the image
	 * @global	array	$vars			System settings; used for ImageMagickPath
	 * @return	void
	 */
	public function watermark($image_name, $dest_name, $Text)
	{
	
		$CONVERT= $this->convert;
	$temp = <<<HTML
	    $CONVERT -size 200x25 xc:none -gravity center \
	            -stroke black -strokewidth 2 -draw "text 0,0 '$Text'" \
	            -gaussian 0x3 \
	            -stroke none -fill white     -draw "text 0,0 '$Text'" \
	            miff:- | \
	       composite -gravity south -geometry +0-5 -type TruecolorMatte \
	                    -   $image_name   $dest_name
HTML;
		$image_resize_exe = system($temp, $output);
	}	
}