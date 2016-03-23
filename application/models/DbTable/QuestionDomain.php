<?php
/*
 * This is a model class for Question Domain 
 * Created By Sunil Khanchandani
 * 
 */
class Application_Model_DbTable_QuestionDomain extends Zend_Db_Table_Abstract
{
	// This is the name of Table
	protected $_name = 'bal_question_domains';
	
	public function existDomain($domainCode)
	{
		$where = "code = '$domainCode'";
		$domainExist = $this->fetchRow($where);
		if($domainExist){
			return true;
		}else{
			return false;
		}		
	}
	
	public function getDomainData($domainCode)
	{
		$where = "code = '$domainCode'";
		$domainData = $this->fetchRow($where);
		return $domainData;
		
	}
	
public function addDomainData($domainData)
	{
		
		$domainDataInsert = $this->insert($domainData);
		return $domainDataInsert;
		
	}
}	

