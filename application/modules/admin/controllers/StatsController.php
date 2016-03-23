<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Stats
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses       StatsController
 * @category   Stats
 * @package    Zend_Application
 * @subpackage Stats
 */  
class Admin_StatsController extends Zend_Controller_Action
{
	protected  $_todayDate;
	protected $_weekdayDigit;
	/*
	 * function call during initialization
	 */
	public function init()
	{	 
		parent::init();
		$layout = Zend_Layout::getMvcInstance();//Create object
		 $layout->setLayout('admin',true);//set layout admin
		 require_once APPLICATION_PATH.'/../library/functions.php';
		 $todayDateObj = new Zend_Date ( strtotime ( Zend_Date::now () ) );
		 $date = $todayDateObj->toString ( "YYYY-MM-dd HH:mm:ss" );
		 $this->_todayDate = $date;
		 $date = Zend_Date::now();
		 $this->_weekdayDigit = $date->get(Zend_Date::WEEKDAY_DIGIT);
	}
	/*
	 * function for when admin page request then redirect on login page for authentication
	 */
	public function indexAction()
	{
		//create question table object
		$request 					= $this->getRequest();
		$subjectName				= $request->getParam('search_subject');
		$tblQuestion = new Application_Model_DbTable_ChildQuestion();
		$tblGrade = new Application_Model_DbTable_ChildGrade();
		$tblSubject = new Application_Model_DbTable_ChildSubject();
		$subjectId = '';
		$subjectList = '';
		if(!empty($subjectName)){
			$subjectIdData = $tblSubject->getAllData($subjectName);
			if(!empty($subjectIdData)){
			foreach ($subjectIdData as $subjectIdDataA){
			$subjectId = $subjectIdDataA['subject_id'];
			$subjectList[] = $tblSubject->getSubjectDataOnSubjectId($subjectId);
			}	
			}
		}else{
			$subjectList = $tblSubject->getAllSubjectList();
		}
		$gradeList = $tblGrade->getAllGradeList();
		$subjectWiseData=array();
		$gradeWiseTotalQuestions=array();
			foreach ($gradeList as $gradeListArray){
				$gradeId = $gradeListArray['grades_id'];
				$statsData[$gradeId] = $tblQuestion->getquestionStats($gradeId,$subjectId);

				if($statsData[$gradeId])
				{
					foreach($statsData[$gradeId] as $subjectDetails)
					{
						$subjectWiseData[$subjectDetails['subject_id']][$gradeId]['total']=$subjectDetails['total'];
						$subjectWiseData[$subjectDetails['subject_id']][$gradeId]['totalApproved']=$subjectDetails['totalApproved'];
						if(!isset($gradeWiseTotalQuestions[$gradeId]))
						{
							$gradeWiseTotalQuestions[$gradeId]['total']=0;
							$gradeWiseTotalQuestions[$gradeId]['totalApproved']=0;
						}						
						if(!empty($subjectList)){
							$gradeWiseTotalQuestions[$gradeId]['total']+=$subjectDetails['total'];
							$gradeWiseTotalQuestions[$gradeId]['totalApproved']+=$subjectDetails['totalApproved'];
						}
					}
				}
			}
			$this->view->subjectList = $subjectList;
			$this->view->gradeList = $gradeList;
			$this->view->subjectWiseData= $subjectWiseData;
			$this->view->gradeWiseTotalQuestions= $gradeWiseTotalQuestions;
			$this->view->searchSubject = $subjectName;
	}
	public function statslistAction()
	{
		//create question table object
		$request = $this->getRequest ();
		$subjectId = $request->getParam ( 'subjectId' );
		$subjectId = base64_decode($subjectId);
		if($request->isPost()){
			$categoryCode = $request->getPost( 'search_statslist' );
		}
		$tblQuestion = new Application_Model_DbTable_QuestionCategories();
		$statsList = $tblQuestion->getCategoryList($subjectId,$categoryCode);
		$page						= $this->_getParam('page',1);
		$paginator 					= Zend_Paginator::factory($statsList);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->statsList     = $paginator;
		$this->view->totalRecord  = count($statsList);
		$this->view->currentPage  = $page;
		$this->view->searchKeyword = $categoryCode;
		$this->view->subjectId = $subjectId;
	}
	public function personstatsAction()
	{
		$todayDateObj = new Zend_Date ( strtotime ( Zend_Date::now () ) );
		$date = $todayDateObj->toString ( "YYYY-MM-dd" );
		$request = $this->getRequest ();
		$tblAdminUser = new Application_Model_DbTable_UserLogin();
		$tblQuestion = new Application_Model_DbTable_ChildQuestion();
		$currentweakDate = currentWeekDate($this->_weekdayDigit);
		if($request->isPost()){
			$persionName = $request->getPost( 'persion_name' );
		}
		$userAdmin = $tblAdminUser->fetAllAdminCountQuestion($persionName,$currentweakDate);//echo "<pre>";print_r($userAdmin);die;
		foreach ($userAdmin as $userAdminA){
			$userId = $userAdminA['admin_user_id'];
			$dataResult[$userId] = $tblQuestion->getCountData($userId,$currentweakDate,$date);
			$dataResult[$userId]['admin_name'][]['name'] = $userAdminA['name'];
			$dataResult[$userId]['admin_id'][]['id'] = $userId;
		}
		//echo "<pre>";print_r($dataResult);die;
		$this->view->dataResult = $dataResult;
		$this->view->searchName = $persionName;
		$this->view->totalRecord  = count($dataResult);
	}
	public function personstatslistAction()
	{
		$tblSubject = new Application_Model_DbTable_ChildSubject();
		$subjectList = $tblSubject->getAllSubjectList();
		 //create question table object
		$request = $this->getRequest ();
		$userId = $request->getParam ( 'userId' );
		$userId = base64_decode($userId);
		if($request->isPost()){
			$searchDate = $request->getPost( 'fromDatePersion' );
		}
		$tblQuestion = new Application_Model_DbTable_ChildQuestion();
		$dateArray = $tblQuestion->getCreatedDate($userId);
		
		$startDate = $dateArray[0]['minDate'];
		$weekName = date('l', strtotime($startDate));
		$weekStart = date('w',strtotime($weekName));
		$endMDate = $dateArray[0]['MaxDate'];
		if(isset($searchDate) && !empty($searchDate)){
			if(strtotime($searchDate) > strtotime($endMDate)){
				$endMDate = $dateArray[0]['MaxDate'];
			}else{
				$endMDate = $searchDate;
			}
		}else{
			$endMDate = $dateArray[0]['MaxDate'];
		}
		if(!empty($startDate) && !empty($endMDate)){
		$weekEName = date('l', strtotime($endMDate));
		$weekEStart = date('w',strtotime($weekEName));
		$wheekSatrtDate = endDateVal($weekStart,$startDate);
		$wheekForIndexDate = date('Y-m-d',strtotime("+0 days",strtotime($wheekSatrtDate)));
		$weekEndSatrtDate = endDateVal($weekEStart,$endMDate);
		$fromDate = date('Y-m-d',strtotime("-1 days",strtotime($startDate)));
		$totalWeekDataQuestion = 0;
		while (strtotime($wheekSatrtDate) <= strtotime($weekEndSatrtDate)){
			$total = 0;
			$totalApproved =0;
			$userData[$wheekForIndexDate] = $tblQuestion->getAllUserWiseData($userId,$fromDate,$wheekSatrtDate);
			$weekDate[]['week'] = $wheekForIndexDate;
			foreach ($userData[$wheekForIndexDate] as $userDataA){
				$weekDateWiaseDat[$wheekForIndexDate][$userDataA['subject_id']]['total'] = $userDataA['total'];
				$weekDateWiaseDat[$wheekForIndexDate][$userDataA['subject_id']]['totalApproved'] = $userDataA['totalApproved'];
				$total = $total +$userDataA['total'];
				$totalApproved = $totalApproved+$userDataA['totalApproved'];
				}
				foreach ($subjectList as $subjectListA){
					
					if(!empty($weekDateWiaseDat[$wheekForIndexDate][$subjectListA['subject_id']]['total'])){
						$subjectWiseData[$subjectListA['subject_id']]['total'] = $subjectWiseData[$subjectListA['subject_id']]['total'] + $weekDateWiaseDat[$wheekForIndexDate][$subjectListA['subject_id']]['total'];
					}else{
						$subjectWiseData[$subjectListA['subject_id']]['total'] = $subjectWiseData[$subjectListA['subject_id']]['total'] +0; 	
					}
					if(!empty($weekDateWiaseDat[$wheekForIndexDate][$subjectListA['subject_id']])){
						$subjectWiseData[$subjectListA['subject_id']]['totalApprove'] = $subjectWiseData[$subjectListA['subject_id']]['totalApprove'] + $weekDateWiaseDat[$wheekForIndexDate][$subjectListA['subject_id']]['totalApproved'];
					}else{
						$subjectWiseData[$subjectListA['subject_id']]['totalApprove'] = $subjectWiseData[$subjectListA['subject_id']]['totalApprove'] +0;
					}
					
					
					 
				}
			$weekTotalQuestion[$wheekForIndexDate] =$total;
			$weekTotalApprovedQuestion[$wheekForIndexDate] =$totalApproved;
			$totalWeekDataQuestion = $totalWeekDataQuestion + $total;
			$totalWeekDataApprovedQuestion = $totalWeekDataApprovedQuestion + $totalApproved;
			$fromDate = $wheekSatrtDate;
			$wheekSatrtDate = date('Y-m-d 23:59:59',strtotime("+7 days",strtotime($wheekSatrtDate)));
			$wheekForIndexDate = date('Y-m-d',strtotime("+0 days",strtotime($wheekSatrtDate)));
			
		}
		}
		$this->view->weekDateWiaseDat = $weekDateWiaseDat; 
		$this->view->weekDateWiaseDataApproved = $weekDateWiaseDataApproved;
		$this->view->subjectList = $subjectList;
		$this->view->weekCount = count($weekDate);
		$this->view->weekDate = $weekDate;
		$this->view->weekTotalQuestion = $weekTotalQuestion;
		$this->view->weekTotalApprovedQuestion = $weekTotalApprovedQuestion;
		$this->view->subjectWiseTotal = $subjectWiseData;
		$this->view->totalWeekDataQuestion =$totalWeekDataQuestion;
		$this->view->totalWeekDataApprovedQuestion = $totalWeekDataApprovedQuestion;
		$this->view->userId = $userId;
		$this->view->searchDate = $searchDate;
		
	}
}