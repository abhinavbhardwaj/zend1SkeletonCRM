<?php
/*
 * This is a model class for child domains
 * Created By Suman Khatri
 *
 */
class Application_Model_DbTable_ChildDomains extends Zend_Db_Table_Abstract
{
    // This is name of Table
    protected $_name = 'bal_child_domains';



    /**
     * @desc function to add domain in subject for child  
     * @param Array(data)
     * @return last inserted id
     * @author Suman Khatri on 19th June 2014
     */
    public function addDomainForChild($insertData)
    {
        return $this->insert($insertData);
    }    
	
}
