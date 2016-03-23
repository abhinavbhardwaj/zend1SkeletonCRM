<?php
/*
 * This is a model class for Parent Device relation Information
 * Created By Suman Khatri
 * October 07 2014
 */
class Application_Model_DbTable_ParentDeviceRelationInfo 
    extends Zend_Db_Table_Abstract
{   
    // This is name of Table
    protected $_name = 'bal_parent_device_relation';	

    /**
     * @desc Function to add parent device data in DB
     * @param array $insertData
     * @author suman khatri on 8th October 2014
     * @return last insertId
     */
    public function addParentDeviceInfo($insertData) {
        return $this->insert($insertData);//add data in DB and returns 
        //last insert id
    }
    
    
    /**
     * @desc Function to check device is already exist or not in 
     * bal_parent_device_relation
     * @param $deviceId
     * @author suman khatri on October 08 2014
     * @return array or null
     */
    public function checkDeviceExistOrNotInParentDeviceRelation($deviceId) {
        $where = $this->_db->quoteInto("device_id = ?",$deviceId); 
        $deviceInfo = $this->fetchRow($where); //get device data if exist
        return $deviceInfo; //returns result
    }
    
    /**
     * @desc Function to delete parent device data in DB
     * @param $id
     * @author suman khatri on 8th October 2014
     * @return result
     */
    public function deleteData($id) {
        $where = $this->_db->quoteInto("id = ?",$id); 
        //delete record matching where condition and return result
        return $this->delete($where); 
    }
    
    /**
    * Function to get parentId using accessToken and deviceId
    * @param $accessToken,$deviceId,$returnType
    * @author Suman Khatri on 9th October 2014
    * @return parentId/arrayObject
    */
    public function getParentIdUsingAccessTokenAndDeviceId($accessToken,$deviceId,
        $returnType) 
    {
        if(!empty($accessToken) && $accessToken != null){
            $where = $this->_db->quoteInto("access_token = ?",$accessToken); 
        }if(!empty($where) && $where != null){
            $where .= $this->_db->quoteInto(" and device_id = ?",$deviceId); 
        }else{
            $where = $this->_db->quoteInto("device_id = ?",$deviceId);
        }
        //fetching record matching where condition
        $parentInfo = $this->fetchRow($where);
        //if parent info is not emapty
        if(!empty($parentInfo) && $parentInfo != null){
            if($returnType == 'parId'){
                //assign value of parent id into variable
                $parentId = $parentInfo['parent_id']; 
                return $parentId;//return parentId
            }if($returnType == 'parData'){
                return $parentInfo;//return parentId
            }
        }else{//if parent info is emapty
            return false;
        }
    }
    
    
    /**
    * Function to get regId using ParentId
    * @param $parentId
    * @author Suman Khatri on 12th October 2014
    * @return ArrayIterator
    */
    public function getAllregisteredDeviceInfoOfParent($parentId,$deviceId = null) {
        
        if(($parentId != '' && $parentId != null) || $parentId === 0){
            $where = $this->_db->quoteInto("bpdr.parent_id = ?",$parentId); 
        }
        if(!empty($deviceId)){
            if(!empty($where)){
                $where .= $this->_db->quoteInto(" and bdi.device_id != ?",$deviceId); 
            }else{
                 $where = $this->_db->quoteInto("bdi.device_id != ?",$deviceId); 
            }
        }
        if(!empty($where)){
            $whereCond = $where; 
        }else{
            $whereCond = 1;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('bpdr' => 'bal_parent_device_relation'),
            array(''))
             ->joinInner(array('bdi' => 'bal_device_info'),
                                    'bdi.device_id = bpdr.device_id',     	 				
            array('bdi.*'))
            ->where($whereCond);
        $deviceInfo =  $db->fetchAll($select);
        return $deviceInfo;
    }
    
    /**
     * @desc Function to get all child devices
     * @param array $childId,$parId
     * @author suman khatri on October 08 2014
     * @return Last insert Id
     */
    public function getRemainingDevicesOfParent($childId,$parId) 
    {
        $where = $this->_db->quoteInto("bpdr.parent_id = ?", $parId);
        $where .= $this->_db->quoteInto(" and bcgr.child_id != ?", $childId);
        $where .= $this->_db->quoteInto(" and bcgr.is_associated = ?", 1);
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from(array('bcgr' => 'bal_child_device_relation'),
            array('bcgr.*'))
            ->joinInner(array('bdi' => 'bal_device_info'),
                'bdi.device_id = bcgr.device_id',     	 				
            array('bdi.*'))
            ->joinInner(array('bpdr' => 'bal_parent_device_relation'),
                'bpdr.device_id = bdi.device_id',     	 				
            array(''))
            ->where($where)
            ->group("bdi.device_id");
      $deviceInfo =  $db->fetchAll($select);
      return $deviceInfo;
    }
    
}	

