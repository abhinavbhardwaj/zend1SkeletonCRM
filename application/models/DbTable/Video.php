<?php
/*
 * This is a model class for Video Information
 * Created By Suman Khatri
 * Thursday, August 16 2013 
 */
class Application_Model_DbTable_Video extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_video';
	
	/*
	 * this function is used to get video
	 * created by suman
	 * on August 16 2013
	 */	
	public function getVideo()
	{
		$result = $this->fetchAll();
		if(!empty($result)){
			return $result[0];
		}else{
			return $result;
		}
	}
	
	
	/*
	 * this function is used to update video
	 * created by suman
	 * on August 16 2013
	 */	
	public function updateVideoData($videoId,$data)
	{
		$where = "id = '$videoId'";
		$result = $this->update($data, $where);
		
	}
	public function addVideoData($data)
	{
		return $this->insert($data);
		
	}
	
}	