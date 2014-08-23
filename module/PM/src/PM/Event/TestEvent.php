<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/TestEvent.php
 */

namespace PM\Event;

/**
 * PM - Test/Example Event
 * 
 * Provides a working example of an Event for developers.
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/TestEvent.php
 */
class TestEvent
{		
	public function edit_task($obj)
	{
		/*
		 * You can grab the passed parameters using the getParam method like the below
		 */
		$task_data = $obj->getParam('data');
		
		/*
		 * If you want to modify the parameters you have to use the setParam method after modifying the parameter
		 * This is to ensure that *other* events that execute after yours can access those changes. If you don't use setParam, the next time
		 * getParam is called it will be the original copy with no changes. 
		 * 
		 * Below, I'm just appending 'fdsa' to the task name. Easy.
		 */
		$task_data['name'] = $task_data['name'].' fdsa';
		$obj->setParam('data', $task_data);
		
		/*
		 * In addition to modifying parameters for all other Events, you can also override the expected return value for a given event.
		 * In those cases, in addition to the above use of setParam for other Events, simple return back the paramater the model expects.
		 * 
		 * Note that since you never know what Events *could* happen after yours it's best practice to always return the main data object.
		 */
		return $task_data;
		
		/*
		 * If you want to ensure that nothing else happens after your Event, including the calling Method 
		 * set the stopPropagation method with a value of true. Note that this will require a return value consistant with 
		 * calling method's signature for returns.
		 */
		//$obj->stopPropagation(true);
		//return $task_data;
		
	}
}