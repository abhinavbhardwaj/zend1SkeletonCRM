<?php
/*
 * This is a model class for Admin Info 
 * @package    CRM
 * @subpackage USER
 * @author: Abhinav Bhardwaj
 * @since: March 23, 2016
 */
class Application_Model_DbTable_UserLogin extends Zend_Db_Table_Abstract
{
	// This is name of Table
	protected $_name = 'bal_admin_users';
	
	
	/*
	 * This is a function to check userexist
	 */
	
	
	public function existUser($userName,$password)
	{ 
		$md5CodedPassword = md5($password);
                /*
		$where = "username = '$userName'";
		$where .= " AND password = '$md5CodedPassword'";
		//$where .= " AND is_active = 'Y'"; 
                 */
		$checkUser = $this->fetchRow(array('username = ?' => $userName, 'password = ?' => $md5CodedPassword)); 
                return $checkUser;
		/*if($checkUser){
			return true;
		}else{
			return false;
		}*/
	}
	
	public function fetchUserdata($username)
	{
	
		$where = "username = '$username'";  
		$getUserData = $this->fetchRow($where);
		return $getUserData;
		
	}
	//get admin data by email address
	public function fetchUserdataByEmail($emailId)
	{
		$where = $this->_db->quoteInto("email=?",$emailId);
		//$where = "username = '$username'";
		$getUserData = $this->fetchRow($where);
		return $getUserData;
	
	}	
/////Kuldeep Suntions for admin users
	public function add($data)
	{
	
		$options 		= $this->insert($data);
			/*	$sql = $select->__toString();
echo "$sql\n";	  
exit;*/
		return $options;
		
	}
	
	public function edit($DataArray,$admin_user_id )
	{
		$where = $this->_db->quoteInto("admin_user_id=?",$admin_user_id);
		$update_data		= $this->update($DataArray,$where);
	
		return $update_data;
	}

    public function getList($searchData=null)
	{
		$where = '1';
		if($searchData!=null){
				
			$where .= " AND bl.name LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where .= " OR bl.username  LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where .= " OR bl.email LIKE ".$this->_db->quote($searchData)."";
			$where .= " OR bl.phone LIKE ".$this->_db->quote('%'.$searchData.'%')."";
			$where1 = "bl.type != 1";
				
		}else{
			$where1 = "bl.type != 1";
		}
		
		

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_admin_users'),
		array('bl.*'))
		
		->where($where)
		->where($where1)
		//->group('grds.grade_name')
		->order('admin_user_id desc');
		$Info =  $db->fetchAll($select);
		return $Info;
	}
	
	public function delete($admin_user_id)
	{

		$where = $this->_db->quoteInto("admin_user_id=?",$admin_user_id);
		$delete =  $this->delete($where);
		if($delete){
				
			return true;
				
		}
	}
	
	
	public function getParticular($admin_user_id){

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $this->_db->quoteInto("admin_user_id=?",$admin_user_id);
		$select = $db->select()
		->from($this->_name)
		->where($where);
		$Data = $db->fetchRow($select);
		
		return $Data;
	}
	


   public function check($column,$value){

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where = $this->_db->quoteInto("$column=?",$value);
		$select = $db->select()
		->from($this->_name, array('count(*) as tot'))
		->where($where);
		$Data = $db->fetchRow($select);
		
		return $Data['tot'];
	}
  
  public function check2($column,$value,$id){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$where[] = $this->_db->quoteInto("$column=?",$value);
		$where[] = $this->_db->quoteInto("admin_user_id!=?",$id);
		$cond=implode(' and ',$where);
		$select = $db->select()
		->from($this->_name, array('count(*) as tot'))
		->where($cond);
		$Data = $db->fetchRow($select);
		return $Data['tot'];
	}	
	/**
	 * @desc function used to get list of admin users
	 * @param N/A
	 * @return ArrayIterator
	 * @author Suman Khatri on 09-10-2013
	 */
	public function GetAllAdminUsers()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('bl' => 'bal_admin_users'),
		array('bl.*'))
		->order('admin_user_id desc');
		$Info =  $db->fetchAll($select);
		return $Info;
	}

/*******************function for featch all admin user according there question count*********************/
	public function fetAllAdminCountQuestion($adminName,$dateWeek){
		$startWeek = $dateWeek['start_date'];
		$endWeek = $dateWeek['end_date'];
		$where1 = "blq.bal_question_id IS NULL";
		if(isset($adminName) && !empty($adminName)){
			$where = "name LIKE '%$adminName%'";
		}else{
			$where = 1;
		}
		$db = Zend_Db_Table::getDefaultAdapter ();
		$select = $db->select ()->from ( array (
				'adu' => $this->_name,
		), array (
				'adu.*'
		) )->joinLeft ( array (
				'blq' => 'bal_questions'
		), "adu.admin_user_id = blq.created_byid AND blq.created_date >= '$startWeek' AND blq.created_date <= '$endWeek'", array (
				'total' => 'SUM(IF(blq.bal_question_id IS NULL,0,1))',
		) )->where($where)
		//->where('adu.type = 2')
		->group('adu.admin_user_id')
		->order(total);
		$adminData = $db->fetchAll($select);
		return $adminData;
		
		
		
		
	}
	public function updateAdmin($data, $userId){
		$where =  $this->_db->quoteInto("admin_user_id =?",$userId);
		return $this->update($data, $where);
	}
	
}// end of class Application_Model_DbTable_UserLogin