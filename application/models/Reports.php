<?php
class Application_Model_Reports extends Zend_Loader_Autoloader
{	
/*
	 * defined all object variables that are used in entire class
	 */
	private $_tblQuestion;
	/*
	 * function for create all model table object used this object to call model table functions
	 */
	public function __construct() {
		 //creates object of model file ChildQuestion	
		$this->_tblQuestion 			= new Application_Model_DbTable_ChildQuestion ();
	}
	/**
     * @desc Function to get question report category wise
     * @param catId,childId,gradeId,firstDate,lastDate
	 * @author suman khatri on 13th November 2013
	 * @returns array
     */
	public function getMonthlyQuestionForReportCategoryWise($catId, $childId, $gradeId, $firstDate, $lastDate ,
	$sOrder=null ,$sortOr=null,$sWhere =null, $subjectId = null, $isCategoryReport = false) {
		//fetches monthly report array category wise
		$result 				= $this->_tblQuestion->GetMonthlyQuestionForReportCategoryWise($catId, $childId,
		 $gradeId, $firstDate, $lastDate, $sOrder, $sortOr, $sWhere, $subjectId );
                
               // $isCategoryReport = 1;
                if($isCategoryReport) {
                    $temp = $result;
                    $result = array();
                    
                    foreach ($temp as $key => $domainsub) {
                        foreach ($domainsub as $sub) {
                            
                            if(empty($sub['description'])) {
                                $sub['description'] = 'Other';
                            }

                            if(isset($result[$key][$sub['description']])) {
                                $result[$key][$sub['description']]['totalCount'] += $sub['totalCount'];
                                $result[$key][$sub['description']]['totalCorrect'] += $sub['totalCorrect'];
                            } else {
                                $result[$key][$sub['description']] = $sub;
                            }
                        }
                    }
                    
                    $temp = $result;
                    $result = array();
                    
                    foreach ($temp as $key => $domainsub) {
                        foreach ($domainsub as $sub) {
                            $result[$key][] = $sub;
                        }
                    }
                }
		return $result; //return  monthly report array 
	}
	
	/**
     * @desc Function to get question report monthly
     * @param catId,childId,gradeId,firstDate,lastDate
	 * @author suman khatri on 13th November 2013
	 * @returns array
     */
	public function getMonthlyReportOfChild($subId, $childId, $gradeId , $month , $year ,$sOrder=null ,$sortOr=null,$sWhere =null) {
		$this->_tblQuestion			= new Application_Model_DbTable_ChildQuestion(); //creates model class for childquestion
		//fetches report data according to type (monthly or quarterly)
		$firstDate = date("$year-$month-01 00:00:00");
		$lastDate = date("$year-$month-t 23:59:59" ,strtotime($firstDate));
		$result = $this->_tblQuestion->GetMonthlyQuestionForReport ($subId, $childId, $gradeId ,$firstDate , $lastDate,$sOrder ,$sortOr,$sWhere);
		return $result;//returns result (report data)
	}
	
	/**
     * @desc Function to get question report quarterly
     * @param catId,childId,gradeId,firstDate,lastDate
	 * @author suman khatri on 13th November 2013
	 * @returns array
     */
	public function getQuarterlyReportOfChild($subId, $childId, $gradeId, $month, $year ,$quarter,$sOrder=null ,$sortOr=null,$sWhere =null) {
		if($quarter == 1) {
		$qdates = date("$year-9-01 00:00:00")."&".date("$year-11-t 23:59:59");
		} else if($quarter == 2) {
			if($month == 12){
				$startYear = $year;
				$endYear = $year+1;
			}else if($month == 1 || $month == 2){
				$startYear = $year-1;
				$endYear = $year;
			}else{
				$startYear  = $year-1;
				$endYear	= $year;
			}
			$leap = date('L', mktime(0, 0, 0, 1, 1, $endYear));
			$date = $leap?29:28;
			$qdates = date("$startYear-12-01 00:00:00")."&".date("$endYear-2-$date 23:59:59");
		} else if($quarter == 3) {
			$qdates = date("$year-3-01 00:00:00")."&".date("$year-5-t 23:59:59");
		} else if($quarter == 4) {
			$qdates = date("$year-6-01 00:00:00")."&".date("$year-8-t 23:59:59");
		} 
		$result = $this->_tblQuestion->GetQuarterlyQuestionForReport ($subId, $childId, $gradeId,$qdates,$sOrder ,$sortOr,$sWhere);
		return $result;//returns result (report data)
	}
	
/**
     * @desc Function to get type of report
     * @param type,firstDate,lastDate
	 * @author suman khatri on 13th November 2013
	 * @return returntype
     */
	public function getReturnType($firstDate, $lastDate ,$type ) {

		if(!empty($type) && $type != null){
			//fetches returntype according type
	     	if($type == "quarterly") {
	     		$retunType = "quarterly";
	     	}
	     	if($type == "monthly") {
	     		$retunType = "monthly";
	     	}
		} 
		if(!empty($firstDate) && !empty($lastDate) && $firstDate != null && $lastDate != null) {
			//fetches returntype according firstDate and lastDate
			$datediff = abs(strtotime($firstDate) - strtotime($lastDate));
	     	$days = floor($datediff/(60*60*24));
	     	if($days > 31) {
	     		$retunType = "quarterly";
	     	} else {
	     		$retunType = "monthly";
	     	}
		}
		return $retunType;
	}
}