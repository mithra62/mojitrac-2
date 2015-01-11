<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Event/ViewEvent.php
 */

namespace Api\Event;

use Base\Event\BaseEvent;

/**
 * Api - View Event
 * 
 * Injects the view event
 *
 * @package 	Api\View
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Event/ViewEvent.php
 */
class ViewEvent extends BaseEvent
{	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'view.render.account' => 'modAccountView'
    );
    
    public function __construct($identity, \PM\Model\Users $user)
    {
    	parent::__construct();
    	$this->identity = $identity;
    	$this->user = $user;
    }

    /**
     * Registers the Event with ZF and our Application Model
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    public function register( \Zend\EventManager\SharedEventManager $ev)
    {
    	foreach($this->hooks AS $key => $value)
    	{
    		$ev->attach('Base\Model\BaseModel', $key, array($this, $value));
    	}
    }
    
    /**
     * Merges our settings array into the MojiTrac Settings array 
     * @param \Zend\EventManager\Event $event
     * @return array
     */
	public function modAccountView(\Zend\EventManager\Event $event)
	{
		$partials = $event->getParam('partials');
		$partials[] = 'api/account/key';
		$event->setParam('partials', $partials);
		return $partials;
		
	}
}