<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/forms/Mail.php
*/

/**
* Mail Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/models/Mail.php
*/
class Model_Mail extends Zend_Mail 
{
		
	/**
	 * The email method
	 * @var object
	 */
	public $transport;
	
	/**
	 * The users info
	 * @var mixed
	 */
	public $user_data = FALSE;
	
	/**
	 * The URL to reference the main site
	 * @var mixed
	 */
	public $web_url = FALSE;
	
	public $utils;	
	
	public function __construct(){
		
		parent::__construct("UTF-8");
				
		$this->addHeader('X-MailGenerator', 'MojiTrac');
		$this->setFrom('noreply@mojitrac.com', 'MojiTrac');
				
		$config = array('auth' => 'login',
		                'username' => 'noreply@mojitrac.com',
		                'password' => 'dimens35');
		
		if('development' == APPLICATION_ENV)
		{
			$this->transport = new Zend_Mail_Transport_Smtp('63.236.10.80', $config);
		}
		
		$this->web_url = 'http://'.$_SERVER['HTTP_HOST'];
		$this->utils = new LambLib_Controller_Action_Helper_Utilities;
	}
	

	public function makeLink($body, $pk, $type = 'project')
	{
		$url = '';
		switch($type)
		{
			case 'download':
				$url = '/pm/files/download-revision/id/'.$pk;
			break;
			
			case 'file':
				$url = '/pm/files/view/id/'.$pk;
			break;			
						
			case 'project':
				$url = '/pm/projects/view/id/'.$pk;
			break;
			
			case 'user':
				$url = '/pm/users/view/id/'.$pk;
			break;
			
			case 'company':
				$url = '/pm/companies/view/id/'.$pk;
			break;			
			
			case 'task':
			default:
				$url = '/pm/tasks/view/id/'.$pk;
			break;
		}
		
		return '<a href="'.$this->web_url.$url.'">'.$body.'</a>';
	}	
	
	public function makeHtml($text) 
	{
		$text = nl2br($text);
		$pattern = "@\b(https?://)?(([0-9a-zA-Z_!~*'().&=+$%-]+:)?[0-9a-zA-Z_!~*'().&=+$%-]+\@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-zA-Z_!~*'()-]+\.)*([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z]\.[a-zA-Z]{2,6})(:[0-9]{1,4})?((/[0-9a-zA-Z_!~*'().;?:\@&=+$,%#-]+)*/?)@";
		$text = preg_replace($pattern, '<a href="\0">\0</a>', $text);
		
		return $text;
	} 
}