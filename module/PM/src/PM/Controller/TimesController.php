<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/TimesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Times Controller
 *
 * Routes the Times requests
 *
 * @package 		TimeTracker
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/TimesController.php
 */
class TimesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );  ;
        $this->layout()->setVariable('active_nav', 'time');
        $this->layout()->setVariable('sub_menu', 'times');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', '');
		return $e;       
	}
    
    public function indexAction()
    {
    	$times = $this->getServiceLocator()->get('PM\Model\Times');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	
    	$view['month'] = $month;
    	$view['year'] = $year;

    	if($this->perm->check($this->identity, 'manage_time'))
    	{
    		$items = $times->getCalendarItems($month, $year);
    		$view['calendar_data'] = $items;
    	}
    	else
    	{
    		$view['calendar_data'] = $times->getCalendarItems($month, $year, $this->identity);
    	}
    	
    	return $view;
    }
    
    public function viewDayAction()
    {
    	$times = $this->getServiceLocator()->get('PM\Model\Times');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	$day = $this->params()->fromRoute('day', date('d'));
		$view = $this->params()->fromRoute('view');
    	    		    	
		$form = $this->getServiceLocator()->get('PM\Form\TimeForm');

		//$form->setData(array('date' => date('Y-m-d', mktime(0,0,0,$month, $day, $year))));
		$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$formData = $this->getRequest()->getPost();
			$form->setInputFilter($times->getInputFilter());
    		$form->setData($formData->toArray());
    		if ($form->isValid($formData->toArray()))
    		{
    			$formData = $formData->toArray();
				$formData['creator'] = $this->identity;
				$formData['user_id'] = $this->identity;
				$time_id = $times->addTime($formData);
				if($time_id)
				{
					$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
					$this->flashMessenger()->addMessage($translate('time_added', 'pm'));
					return $this->redirect()->toRoute('times/view-day', array('month' => $month, 'year' => $year, 'day' => $day));
					
				} 
				else 
				{	
					$view['errors'] = array('Something went wrong...');
				}
				
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}
		}    	
    	
		$view['month'] = $month;
		$view['year'] = $year;
		$view['day'] = $day;
		$view['active_sub'] = $view;
		$where = array('month' => $month, 'year' => $year, 'day' => $day);
		if($this->perm->check($this->identity, 'manage_time'))
    	{		
	    	$view['times'] = $times->getAllTimes($where); 
    	}
    	else
    	{
    		$where = array_merge($where, array('i.creator' => $this->identity));
    		$view['times'] = $times->getAllTimes($where); 
    	}

	    $view['form'] = $form;
	    return $view;    
    }

    public function removeAction()
    {
    	$time = $this->getServiceLocator()->get('PM\Model\Times');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
    	$id = $this->params()->fromRoute('time_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('times');
    	}
    	
    	
        $time_data = $time->getTimeById($id);
        $view = array();
    	$view['time_data'] = $time_data;
    	if(!$view['time_data'])
    	{
			return $this->redirect()->toRoute('times');
    	}
    	
    	$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('times/view-day', array('month' => $view['time_data']['month'], 'day' => $view['time_data']['day'], 'year' => $view['time_data']['year']));
				}

				$project = $this->getServiceLocator()->get('PM\Model\Projects');
    			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
	    		if($time->removeTime($id, $time_data, $project, $task))
	    		{	
					$this->flashMessenger()->addMessage($translate('time_removed', 'pm'));
					return $this->redirect()->toRoute('times/view-day', array('month' => $view['time_data']['month'], 'day' => $view['time_data']['day'], 'year' => $view['time_data']['year']));
	    		}
			}
    	}
    	
    	$view['form'] = $form;
    	$view['id'] = $id;
		return $this->ajaxOutput($view);
    }
}