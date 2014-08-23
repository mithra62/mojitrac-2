<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Project/Team.php
 */

namespace PM\Model\Options\Project;

use PM\Model\Options\AbstractOptions;

/**
 * PM - Project Team Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options/Project/Team.php
 */
class Team extends AbstractOptions
{	
	static public function team($project, $project_id, $blank = FALSE)
	{
		$arr = $project->getProjectTeamMembers($project_id);
		$_new = array();
		if($blank)
		{
			$_new['0'] = 'Unassigned';
		}
		foreach($arr AS $user)
		{
			$_new[$user['user_id']] = $user['first_name'].' '.$user['last_name'];
		}
		
		return $_new;
	}
}