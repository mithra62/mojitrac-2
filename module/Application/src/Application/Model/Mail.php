<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Mail.php
 */

namespace Application\Model;

/**
* Mail Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/models/Mail.php
*/
class Mail extends AbstractModel
{
	/**
	 * The email method
	 * @var object
	 */
	public $sending_transport = null;
	
	/**
	 * The sendmail type transport
	 * @var object \Zend\Mail\Transport\Smtp
	 */
	public $sendmail_transport = null;
	
	/**
	 * The file type transport (mostly used for logging emails sent)
	 * @var object \Zend\Mail\Transport\File
	 */
	public $file_transport = null;
	
	/**
	 * The SMTP type transport
	 * @var object \Zend\Mail\Transport\Smtp
	 */
	public $smtp_transport = null;
	
	/**
	 * The URL to reference the main site
	 * @var mixed
	 */
	public $web_url = FALSE;
	
	/**
	 * Mail Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 * @param \Zend\Mail\Message $message
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \Zend\Mail\Message $message){
		
		parent::__construct($adapter, $db);
		
		$this->message = $message;
		$this->message->setEncoding("UTF-8");
		$this->message->getHeaders()->addHeaderLine('X-MailGenerator', 'MojiTrac');
		$this->message->addReplyTo("no-reply@mojitrac.com", "No Reply");
		$this->message->setSender("no-reply@mojitrac.com", "MojiTrac");
		$this->message->setFrom("no-reply@mojitrac.com", "MojiTrac");
		$this->web_url = 'http://'.$_SERVER['HTTP_HOST'];
	}
	
	/**
	 * Sets the Sendmail Tranport object
	 * @param \Zend\Mail\Transport\Sendmail $sendmail
	 * @return Mail
	 */	
	public function setSendmailTransport(\Zend\Mail\Transport\Sendmail $sendmail)
	{
		$this->sendmail_transport = $sendmail;
		return $this;
	}
	
	/**
	 * Sets the File Tranport object
	 * @param \Zend\Mail\Transport\File $file
	 * @return Mail
	 */
	public function setFileTransport(\Zend\Mail\Transport\File $file)
	{
		$this->file_transport = $file;
		return $this;		
	}
	
	/**
	 * Sets the SMTP Tranport object
	 * @param \Zend\Mail\Transport\Smtp $smtp
	 * @return Mail
	 */
	public function setSmtpTransport(\Zend\Mail\Transport\Smtp $smtp)
	{
		$this->smtp_transport = $smtp;
		return $this;		
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