<?php
/*
 * This is a model class for Child Information
 * Created By Suman Khatri
 * Thursday, September 09
 */
class Application_Model_DbTable_ChildRedeemPoints extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_child_redeem';
	
	//function to add record
	public function Addredeemrequest($data)
	{
		return $this->insert($data);
	}
	
	//function used to get request of particular child with grade
	public function getRedeemforToday($childId)
	{
		//echo $date_before1 = date("Y-m-d H:i:s", strtotime(date("Y-m-d") . " -1 days"));
	 	$date = date("Y-m-d 0:0:0");
		$where = "child_id = $childId and created_date >= '$date'";
		$res	= $this->fetchAll($where);
		return $res;
	}
	
	//function to get request of this week of particular child with grade
	public function getRedeemIdforThisweek($childId)
	{
		if(date('w')==0)
		{
			 $first_day_of_week = date('Y-m-d 0:0:0');
		}
		else
		{
			 $first_day_of_week = date('Y-m-d 0:0:0', strtotime('Last Sunday', time()));
		}
		
		$last_day_of_week = date('Y-m-d 0:0:0', strtotime('+6 days',strtotime($first_day_of_week)));
		$date = date('Y-m-d H:i:s');
		//$first_day_of_week = date('Y-m-d H:i:s', strtotime('Last Sunday', time()));
		$date = date('Y-m-d H:i:s');
		$where = "child_id = $childId and created_date >= '$first_day_of_week' and created_date <='$last_day_of_week'";
		$order = "created_date DESC";
		$res	= $this->fetchAll($where,$order);
		return $res;
	}
	
	
	//function to get request of this month with grade
	public function getRedeemIdforThisMonth($childId)
	{
		$first_day_of_month = date('Y-m-1 0:0:0');
		$date = date('Y-m-d H:i:s');
		$where = "child_id = $childId and created_date >= '$first_day_of_month'";
		$order = "created_date DESC";
		$res	= $this->fetchAll($where,$order);
		return $res;
	}
	/***************
	 * function for delete child redeem points
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