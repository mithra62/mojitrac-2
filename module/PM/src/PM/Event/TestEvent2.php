<?php
namespace PM\Event;

//http://framework.zend.com/manual/1.12/en/zend.event-manager.event-manager.html
class TestEvent2
{
	public function register()
	{
		Model_Event::add_events(
			'tasks', 
			'pre.moji_task_edit', 
			array($this, 'edit_task'), 
			4
		);
		
	}
		
	public function edit_task($obj)
	{
		
		//echo __CLASS__.'<br />';
		//$params = $obj->getParams('data');	
		$task_data = $obj->getParam('data');
		$task_data['name'] = $task_data['name'].' fdsa';

		print_r($task_data);
		$obj->setParam('data', $task_data);
		
		return $task_data;
		
	}
}