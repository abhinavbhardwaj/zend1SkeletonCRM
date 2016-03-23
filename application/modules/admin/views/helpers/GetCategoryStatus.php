<?php
/*
* @category   PSV BALANCE
 * @package    PSV BALANCE 
 * @subpackage Helper
 * @category  	get Category Status
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV BALANCE 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses       Helper
 * @category   get answer
 * @package    Zend_Application
 * @subpackage etchilddevice id
 */
class Zend_View_Helper_GetCategoryStatus extends Zend_View_Helper_Abstract 
{
	
    public function getCategoryStatus($categoryId)
    {  
		$tblQuestion					= new Application_Model_DbTable_ChildQuestion();
		$quesCount				= $tblQuestion->getCategoryQuesCount($categoryId);
		return $quesCount;
    }
	
}
?>