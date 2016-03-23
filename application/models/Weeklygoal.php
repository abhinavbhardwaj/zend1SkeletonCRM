<?php
class Application_Model_Weeklygoal extends Zend_Loader_Autoloader
{ 
/*
	 * defined all object variables that are used in entire class
	 */
	private $_tblWeeklyGoal;
	private $_tblWeeklyGoalSta;
	/*
	 * function for create all model table object used this object to call model table functions
	 */
	public function __construct() {
		//creates object for model file ChildDeviceInfo
		$this->_tblWeeklyGoal 	= new Application_Model_DbTable_ChildWeeklyGoal();
		//creates object for model file ParentInfo
		$this->_tblWeeklyGoalStats 		= new Application_Model_DbTable_ChildWeeklyGoalStats(); 
	}
	/*
	 * functions for geting child goal points
	 * @param int childId
	 * @return array;
	 * @Dharmendra Mishra
	 */
	function getWeekGoalPoints ( $childId ){
		return $this->_tblWeeklyGoal->getWeekGoalPoints($childId);
	}
	/*
	 * functions for geting child goal list
	* @param int childId
	* @return array;
	* @Dharmendra Mishra
	*/
	function getWeekGoalData ( $childId ){
		return $this->_tblWeeklyGoal->getWeekGoalData($childId);
	}
	/*
	 * functions for geting child get Total Achive Points
	* @param int childId |date range
	* @return array;
	* @Dharmendra Mishra
	*/
	function getTotalAchivePoints ( $childId, $weeklyGoalDateRange ){
		return $this->_tblWeeklyGoalSta->getTotalAchivePoints($childId);
	}
	/*
	 * functions for geting child last four week data
	* @param int childId |start date end date
	* @return array;
	* @Dharmendra Mishra
	*/
	function getLasrFourWeekGoalId ( $childId, $startDate, $endDate ){
	return $this->_tblWeeklyGoal->getLasrFourWeekGoalId($childId, $startDate, $endDate);
	}
}