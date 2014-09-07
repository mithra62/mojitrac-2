<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/ViewEvents.php
 */

namespace Application\Model;

use Application\Model\AbstractModel;

/**
 * PM - View Events Model
 *
 * @package 	Events\View
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/ViewEvents.php
 */
class ViewEvents extends AbstractModel
{
	public function runEvent($event, array $partials, array $context = array())
	{
		$ext = $this->trigger($event, $this, compact('partials', 'context'));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $partials = $ext->last();
		
		return $partials;
	}
}