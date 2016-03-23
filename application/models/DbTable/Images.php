<?php
/*
 * This is a model class for Video Information
 * Created By Suman Khatri
 * Thursday, August 16 2013 
 */
class Application_Model_DbTable_Images extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_images';
	
	/*
	 * this function is used to get video
	 * created by suman
	 * on August 16 2013
	 */	
	public function getImages($imageId)
	{
		$where = "id = '$imageId'";
		$result = $this->fetchRow($where);
		return $result;
	}
	
	
	/*
	 * this function is used to update video
	 * created by suman
	 * on August 16 2013
	 */	
	public function updateImageData($imageId,$data)
	{
		$where = "id = '$imageId'";
		$result = $this->update($data, $where);
		
	}
	public function addImageData($data)
	{
		return $this->insert($data);
		
	}
	
	public function getAllImages()
	{
		$where = 1;
		$result = $this->fetchAll($where);
		return $result;
	}
	
}	