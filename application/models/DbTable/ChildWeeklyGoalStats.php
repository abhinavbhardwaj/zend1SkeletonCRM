<?php
/**
 * ChildWeeklyGoalStats model class used to handel bal_child_weekly_goal_stats
 * table where we perform task like fetch and update record
 * @category   balance
 * @package    child
 * @subpackage child week goal Stats
 * @copyright  Copyright (c) A3logics. (http://www.a3logics.in)
 * @version    Release: 1.0
 *
 */
class Application_Model_DbTable_ChildWeeklyGoalStats extends Zend_Db_Table_Abstract
{

	protected $_name = 'bal_child_weekly_goal_stats'; //table name;
	/*
	 * function for get data from bal_child_weekly_goal_stats for 
	 * showing all weekly details of child
	 * @param int childId
	 * return array
	 */
	public function getAllWeeklyData($childId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('childWeekGoal' => $this->_name),
				array('goalPoints' =>'childWeekGoal.goal_points'))
				->where("childWeekGoal.child_id = $childId")
				->where("childWeekGoal.start_date >='2013-09-16 15:58:16'")
				->where("childWeekGoal.start_date <='2013-09-16 15:58:16'")
				->where("childWeekGoal.closed ='N'")
		->join('bal_child_weekly_goal_stats', 'childWeekGoal.weekly_goals_id = bal_child_weekly_goal_stats.weekly_goal_id',
				array('achivedPoints' =>'bal_child_weekly_goal_stats.total_points_achieved'));
		
	}
	/***************
	 * function for delete all Weekly goals detials tables
	* @param weeklyGoalId Int
	* return int
	*
	* *************************/
	public function deleteData($weeklyGoalId)
	{
		$where = $this->_db->quoteInto("weekly_goal_id = ?",$weeklyGoalId);
		return $this->delete($where);
	}

}	
	