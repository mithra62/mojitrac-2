<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/AbstractModel.php
 */

namespace Application\Model;

use Base\Model\BaseModel;

 /**
 * Model Abstract
 *
 * Sets things up for abstracted functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/src/Application/Model/AbstractModel.php
 */
abstract class AbstractModel extends BaseModel
{	
	/**
	 * Moji Abstract Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $sql)
	{
		parent::__construct($adapter, $sql);
	}
}