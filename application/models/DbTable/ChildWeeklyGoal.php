<?php
/**
 * ChildWeeklyGoal model class used to handel bal_child_weekly_goal
 * table where we perform task like fetch and update record
 * @category   balance
 * @package    child
 * @subpackage child week goal
 * @copyright  Copyright (c) A3logics. (http://www.a3logics.in)
 * @version    Release: 1.0
 *
 */
class Application_Model_DbTable_ChildWeeklyGoal extends Zend_Db_Table_Abstract
{

	protected $_name = 'bal_child_weekly_goals'; //table name;

	/***function for insert data into table
	 *@param  arrray
	*return int 0 for not inserted and last table id where inserted
	*/

	public function addChildWeekGoals($data){
		return $this->insert($data);
	}
	/**
	 * function for fetch week goal value
	 * @param int childId
	 * @return ArrayIterator
	 */
	public function getWeekGoalPoints($childId){
		$where = "bcwg.child_id = '$childId' AND bcwg.closed ='N'";
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'bcwg' => $this->_name,
		), array (
				'bcwg.*'
		) )->joinLeft ( array (
				'bcg' => 'bal_child_goals'
		), 'bcwg.child_id = bcg.child_id',array('weekly_points'=>'bcg.weekly_points') )
		->where ( $where );
		$weeklyData = $db->fetchRow ( $select );
		return $weeklyData;
	}
	/**
	 * function for fetch week data for start and end date find
	 * @param int childId
	 * @return ArrayIterator
	 */
	public function getWeekGoalData($childId){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('childWeekGoal' => $this->_name),
				array('childWeekGoal.*'))
				->where("childWeekGoal.child_id = $childId")
				->where("childWeekGoal.closed ='N'");
		$result = $db->fetchAll($select);
		return $result;
		
	}
	/**
	 * function for fetch week goal id  for last 4 week
	 * @param int childId
	 * @return ArrayIterator
	 */
	public function getLasrFourWeekGoalId($childId,$startDate,$endDate){
		$db = Zend_Db_Table::getDefaultAdapter();
                $where = $this->_db->quoteInto("childWeekGoal.end_date<=?", $endDate);
                $where .= $this->_db->quoteInto(" AND childWeekGoal.closed=?", 'Y');
                $where .= $this->_db->quoteInto(" AND childWeekGoal.child_id=?", $childId);
		$select = $db->select()
		->from(array('childWeekGoal' => $this->_name),
				array('goalPoints' =>'childWeekGoal.goal_points',
					'status' =>'childWeekGoal.status',
					'end_date' =>'childWeekGoal.end_date','childWeekGoal.weekly_goals_id',
                                    'start_date' => 'childWeekGoal.start_date'))
		->join('bal_child_weekly_goal_stats', 'childWeekGoal.weekly_goals_id = bal_child_weekly_goal_stats.weekly_goal_id',
				array('achivedPoints' =>'bal_child_weekly_goal_stats.total_points_achieved'))
                ->where($where)
                        ->order("childWeekGoal.end_date DESC")
                        ->limit(1);
		$result = $db->fetchAll($select);
		return $result;
	
	}
	/*
	 * function for get data from bal_child_weekly_goal_stats for
	* showing all weekly details of child
	* @param int childId
	* return array
	*/
	public function getAllWeeklyData($childId,$statusselect,$sOrder=null,$sortOr = null, $sWhere = null,$shaving = null){
		if(isset($statusselect) && !empty($statusselect)){
			$where = "childWeekGoal.status = '$statusselect'"; 
		}else{
			$where = 1;
		}
		if(!empty($sOrder) && $sOrder != null){
			$order = $sOrder." ".$sortOr;
		}else{
			$order = 'bal_child_weekly_goal_stats.weekly_goal_id DESC';
		}
		if(!empty($sWhere) && $sWhere != null){
			$where .= " and $sWhere";
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('childWeekGoal' => $this->_name),
				array('status' =>'childWeekGoal.status',
						'weeklyPoints' =>'childWeekGoal.goal_points',
						'end_date' =>'childWeekGoal.end_date',
			new Zend_Db_Expr("CASE childWeekGoal.status WHEN 'A' THEN 'Achieved' WHEN 'M' THEN 'Missed' WHEN 'NA' THEN 'Not Achieved' END AS goal_mode"),
				'start_date' =>'childWeekGoal.start_date'))
				->where("childWeekGoal.child_id = $childId")
				->where("childWeekGoal.closed ='Y'")
				->where($where)
				->order($order)
				->joinRight('bal_child_weekly_goal_stats', 'childWeekGoal.weekly_goals_id = bal_child_weekly_goal_stats.weekly_goal_id',
					array('weekly_goal_id','total_points_achieved','total_question_asked','total_correct_answer','total_incorrect_answer',
					"(total_correct_answer+total_incorrect_answer) as TotalAnswered"));
					if(!empty($shaving) && $shaving != null){
						$select->having($shaving);
					}
					//echo $select;die;
					$result = $db->fetchAll($select);
					return $result;
	
	}
	/********function for get all weekly data for not week end
	 * @param string
	 * @return array
	 * *********/
	public function getWeeklyData($where){
		return $this->fetchAll($where);
	}
	/***************
	 * function for delete child weekly goals
	* @param childId Int
	* return int
	*
	* *************************/
	public function deleteData($childId)
	{
		$where = $this->_db->quoteInto("child_id = ?",$childId);
		return $this->delete($where);
	}
}	
	