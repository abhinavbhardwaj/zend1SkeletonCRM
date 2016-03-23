<?php
/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Questions
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */


class Admin_QuestionsController extends Zend_Controller_Action
{

	protected $_categoryTbObj;
        protected $_childTbObj;
        protected $_tblCategoryInfo;
        protected $_tblDomainInfo;
	protected $_tblGrade;
	protected $_tblSubject ;
	protected $_tblQuestionGrade;
	protected $_tblChild;
        protected $_tblParentNofic;
        protected $_tblSequence;

	public function init()
	{
		parent::init();
		$layout = Zend_Layout::getMvcInstance();//Create object
		$layout->setLayout('admin',true);//set layout admin
		require_once APPLICATION_PATH.'/../library/functions.php';
		require_once APPLICATION_PATH.'/../library/function_image_array.php';
		require_once APPLICATION_PATH.'/../library/imagecreater.php';
		require_once (APPLICATION_PATH . '/../library/excel_reader/excel_reader.php');
		
		$this->_categoryTbObj = new Application_Model_Category ();
                $this->_childTbObj = new Application_Model_Child ();
                
                $this->_tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		$this->_tblDomainInfo   = new Application_Model_DbTable_QuestionDomain();
		$this->_tblGrade        =  new Application_Model_DbTable_ChildGrade();
		$this->_tblSubject        =  new Application_Model_DbTable_ChildSubject();
		$this->_tblQuestionGrade = new Application_Model_DbTable_QuestionGrade();
		$this->_tblChild       = new Application_Model_DbTable_ChildInfo();
                $this->_tblParentNofic = new Application_Model_DbTable_ParentNotifications();
		$this->_tblSequence     = new Application_Model_DbTable_ChildQuestionSequence();
                
                
                
                
                
                
	}


	public function indexAction()
	{

	}
	/*
	 * function to add category
	 */
	public function addcategoryAction()
	{
		$request         = $this->getRequest();
		//$tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		//$tblDomainInfo   = new Application_Model_DbTable_QuestionDomain();
		//$tblGrade        =  new Application_Model_DbTable_ChildGrade();
		//$tblSubject        =  new Application_Model_DbTable_ChildSubject();
		//$tblQuestionGrade = new Application_Model_DbTable_QuestionGrade();
		//$tblChild       = new Application_Model_DbTable_ChildInfo();
                //$tblParentNofic = new Application_Model_DbTable_ParentNotifications();
		//$tblSequence		= new Application_Model_DbTable_ChildQuestionSequence();
               
		if($request->isPost()){
                    //echo "<pre>";
                   // print_r($request->getPost());die;
                        $domainName   = $request->getPost('domain_name_add');
			$domainCode   = $request->getPost('domain_code_add'); 
			$category     = $request->getPost('category');
			$category     = strtolower($category);
			$subjectId     = $request->getPost('subject_id_add');
			$standard      = $request->getPost('standard_add');
			$standardCode = $request->getPost('standard_id_add');
			$domainId     = $request->getPost('domain_code_id_add');
			$grade        = $request->getPost('grade_add');
			$description  = $request->getPost('category_desc');
			$framework = $request->getPost('framework_add');
			$gradeDataArray  = array('grades_id');
			$gradeInfo    = $this->_tblGrade->getGradeDataOnGradeName($grade,$gradeDataArray);
			$gradeId = $gradeInfo['grades_id'];

			$subTopicCode        = $request->getPost('sub_topic_add');
			$subTopicName        = $request->getPost('subtopic_name');
			$headLine        = $request->getPost('headlines');

			$checkCategoryExist = $this->_tblCategoryInfo->checkCategoryExist($category);
			if($checkCategoryExist==true){
				$this->view->error = 'Category already exist';
				return false;
			}
			if($domainName!=null){
				$domainNameData = array('code' =>$domainCode,
									'name' =>$domainName);
				$domainId  = $this->_tblDomainInfo->addDomainData($domainNameData);
			}else{
				$domainId = $domainId;
                                $where = "domain_id = $domainId";
                                $domainData = $this->_tblDomainInfo->fetchRow($where);
                                $domainName = $domainData['name'];
			}
                        $date = date('Y-m-d H:i:s');
			if(strtolower($standard)=='bal'){
				if($subjectId==null){
					
					$frameworkData = array('standard_id'=>$standardCode,'subject_name'=>$framework,'created_date' => $date);
					$addframework = $this->_tblSubject->addSubjectInfo($frameworkData);
                                        $this->sendPushToAllDevices($addframework, $framework);
                                        if('maths' == strtolower($framework)){
                                            $dataM = array('subject_id' =>26);
                                            $whereM = "subject_name = '".strtolower($framework)."'";
                                            $addframework = $this->_tblSubject->updateSubjectInfo($dataM, $whereM);
                                            $addframework = 26;
                                        }else if(strtolower('CURRENTAFFAIRS') == strtolower($framework)){
                                            $dataM = array('subject_id' =>23);
                                            $whereM = "subject_name = '".strtolower($framework)."'";
                                            $addframework = $this->_tblSubject->updateSubjectInfo($dataM, $whereM);
                                            $addframework = 23;
                                        }
					$subjectId = $addframework;

				}
			}
			
			$adminInfoSession = new Zend_Session_Namespace('adminInfo');
			$adminLogindata = $adminInfoSession->adminData;
			$adminName = $adminLogindata->name;
                        //add notification if category contains new subject and grade
                        
			//$addNoti = $this->_addNotificationForkidIfNewSubjectAdded($grade,$framework,$subjectId,$domainId,$domainName);
                        //end add notification if category contains new subject and grade
			$psvCode = 'psv.'.strtolower($standard).'.'.strtolower($framework).'.'.strtolower($grade).'.'.strtolower($domainCode).'.'.strtolower($subTopicCode);
			$categoryDataArray = array(
										'category_code' => $category,
										'standard_id' => $standardCode,
										'domain_id' =>$domainId,
										'subject_id' =>$subjectId,
										'subtopic_code' =>$subTopicCode,
										'subtopic_name' => $subTopicName,
										'headline' => $headLine,
										'description' =>$description,
										'created_by' => $adminName,
										'psv_code' =>$psvCode,	
										'created_date' =>$date,
										'modified_date' =>$date
			);
			$addCategoryData = $this->_tblCategoryInfo->addCategory($categoryDataArray);
			if(strpos($grade,'-')){
				$getRangeGrade = explode('-',$grade);
				if($addCategoryData){
					$differenOfGrade = $getRangeGrade[1]-$getRangeGrade[0];
					$addGrade = 0;
					$addGrade = $getRangeGrade[0];
					for($i=0;$i<=$differenOfGrade;$i++){
						$addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($addGrade, $addCategoryData, $subjectId,$domainId);
						$addGrade = $addGrade+1;
					}
					if($addQuesGrade){
						$this->_helper->flashMessenger->addMessage('Data added successfully');
						$this->_redirect('/admin/questions/categorylist');
					}else{
						$this->view->error = 'Error adding question grade data';
					}
				}else{
					$this->view->error = 'Error adding category data';
				}

			}else{
				if($addCategoryData){
					$addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($gradeId, $addCategoryData, $subjectId,$domainId);
					if($addQuesGrade){
						$this->_helper->flashMessenger->addMessage('Data added successfully');
						$this->_redirect('/admin/questions/categorylist');
					}else{
						$this->view->error = 'Error adding question grade data';
					}


				}else{
					$this->view->error = 'Error adding category data';
				}
			}
			
		}
	}
        
        /**
	 * @desc Function to add category having specified grade and subject
	 * @param $gradeId,$addCategoryData,$subjectId
         * @author suman khatri on 14th May 2014
	 * @return result
	 */
        private function _addCategoryToChildHavingGradeAndSubject($gradeId,$addCategoryData,$subjectId,$domainId){
            $tblChild			= new Application_Model_DbTable_ChildInfo();
            $tblSequence		= new Application_Model_DbTable_ChildQuestionSequence();
            $tblQuestionGrade = new Application_Model_DbTable_QuestionGrade();
            $quesGradeDataArray = array('grade_id' => $gradeId,
                                        'category_id' => $addCategoryData
					);
            $addQuesGrade = $tblQuestionGrade->addGradeData($quesGradeDataArray);
            $childwithgardeandsub	= $tblChild->GetChildInfoGradeSubjectAndDomainwise($gradeId,$subjectId,$domainId);
            if($childwithgardeandsub)
            {
                    foreach ($childwithgardeandsub as $child)
                    {
                            $childId  			= $child['child_id'];
                            $maxSequenceInfo	= $tblSequence->getMaxsequence($childId);
                            $maxsequence		= $maxSequenceInfo['maxsequenceid'];
                            $j = $maxsequence +1;
                            $arraySequence 	= array(
                                                            'child_id'			=> $childId,
                                                            'category_id'		=> $addCategoryData,
                                                            'sequence_number'	=> $j,
                                                            'created_date'		=> date('Y-m-d H:i:s')
                            );
                            $addsequence	= $tblSequence->addSequence($arraySequence);
                    }
                if($addsequence){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
	
private function _questionWithImage($strQuestions){
	$tagName = 'PSVEQ[';
	$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
	$arrTagStr = explode($tagName, $strQuestions);
	$arrEqStr = array();
	$arrImageUrls = array();
	for($i=1; $i<=count($arrTagStr); $i++){
		$strFinal = '';
		if(!empty($arrTagStr[$i]) && strpos($arrTagStr[$i], ']')){
			$arrEqDesc = str_split($arrTagStr[$i]);
			$intPosition = 0;
			for($p=0; $p<count($arrEqDesc); $p++){
				if($arrEqDesc[$p] == ']'){
					$intPosition = $p;
				}
			}
	
			for($m=0; $m<$intPosition; $m++){
				$strFinal .= $arrEqDesc[$m];
			}
	
			array_push($arrEqStr, $strFinal);
		}
	}
	
	for($j=0; $j<count($arrEqStr); $j++){
		array_push($arrImageUrls, $urlImageProcess.  str_replace(' ', '&space;', $arrEqStr[$j]));
	}
	
	$strDesWithDummyTag = $this->replaceEQTagsWithImage($tagName, $strQuestions);
	$arrDesWithDummyTag = explode('equation_tags', $strDesWithDummyTag);
	
	$strTextWithImages = $arrDesWithDummyTag[0];
	
	for($k=1; $k<count($arrDesWithDummyTag); $k++){
	
		if(@fopen($arrImageUrls[$k-1], "r")){
			$strTextWithImages .= '<img src="'.htmlentities($arrImageUrls[$k-1]).'" />'.$arrDesWithDummyTag[$k];
		}else{
			$strTextWithImages = 'Unable to load image, please try again';
		}
	}
	
	return nl2br($strTextWithImages);
	
} 	
	
	
	public function previewquestionAction() {
		$this->_helper->layout->disableLayout();
		$request         = $this->getRequest();
		$question =  $request->getParam('question');
		$rigthAnswer =  $request->getParam('right_answer');
		$wrongAnswer1 =  $request->getParam('wrong_answer1');
		$wrongAnswer2 =  $request->getParam('wrong_answer2');
		$wrongAnswer3 =  $request->getParam('wrong_answer3');
		$frameWork = $request->getParam('framework');
		//echo $frameWork;die;
		if(in_array($frameWork,array('7','26'))){
			$this->view->question =  $this->_questionWithImage($question);
			$this->view->rigthAnswer =  $this->_questionWithImage($rigthAnswer);
			$this->view->wrongAnswer1 =  $this->_questionWithImage($wrongAnswer1);
			$this->view->wrongAnswer2 =  $this->_questionWithImage($wrongAnswer2);
			$this->view->wrongAnswer3 =  $this->_questionWithImage($wrongAnswer3);
		}else{
			$this->view->question =  $question;
			$this->view->rigthAnswer =  $rigthAnswer;
			$this->view->wrongAnswer1 =  $wrongAnswer1;
			$this->view->wrongAnswer2 =  $wrongAnswer2;
			$this->view->wrongAnswer3 =  $wrongAnswer3;
				
		}
}
private function _addImportCat($dataArray){

                        $domainName   = $dataArray['domain_name_add'];
			$domainCode   = $dataArray['domain_code_add']; 
			$category     = $dataArray['category'];
			$category     = strtolower($category);
			$subjectId     = $dataArray['subject_id_add'];
			$standard      = $dataArray['standard_add'];
			$standardCode = $dataArray['standard_id_add'];
			$domainId     = $dataArray['domain_code_id_add'];
			$grade        = $dataArray['grade_add'];
			$description  = $dataArray['category_desc'];
			$framework = $dataArray['framework_add'];
                        $subTopicCode        = $dataArray['sub_topic_add'];
			$subTopicName        = $dataArray['subtopic_name'];
			$headLine        = $dataArray['headlines'];
                        
                        
                        
                        
			$gradeDataArray  = array('grades_id');
			$gradeInfo    = $this->_tblGrade->getGradeDataOnGradeName($grade,$gradeDataArray);
			$gradeId = $gradeInfo['grades_id'];

			if($domainName!=null){
				$domainNameData = array('code' =>$domainCode,
									'name' =>$domainName);
				$domainId  = $this->_tblDomainInfo->addDomainData($domainNameData);
			}else{
				$domainId = $domainId;
			}
			
			$date = date('Y-m-d H:i:s');
			if(strtolower($standard)=='bal'){
				if($subjectId==null){
					
					$frameworkData = array('standard_id'=>$standardCode,'subject_name'=>$framework,'created_date' => $date);
					$addframework = $this->_tblSubject->addSubjectInfo($frameworkData);
                                        $this->sendPushToAllDevices($addframework, $framework);
					$subjectId = $addframework;
                                        if('maths' == strtolower($framework)){
                                            $dataM = array('subject_id' =>26);
                                            $whereM = "subject_name = '$framework'";
                                            $addframework = $this->_tblSubject->updateSubjectInfo($dataM, $whereM);
                                            $addframework = 26;
                                        }else if(strtolower('CURRENTAFFAIRS') == strtolower($framework)){
                                            $dataM = array('subject_id' =>23);
                                            $whereM = "subject_name = '$framework'";
                                            $addframework = $this->_tblSubject->updateSubjectInfo($dataM, $whereM);
                                            $addframework = 23;
                                        }

				}
			}
		
			$adminInfoSession = new Zend_Session_Namespace('adminInfo');
			$adminLogindata = $adminInfoSession->adminData;
			$adminName = $adminLogindata->name;
                        //add notification if category contains new subject and grade
			//$addNoti = $this->_addNotificationForkidIfNewSubjectAdded($grade,$framework,$subjectId,$domainId,$domainName);
//end add notification if category contains new subject and grade
			$psvCode = 'psv.'.strtolower($standard).'.'.strtolower($framework).'.'.strtolower($grade).'.'.strtolower($domainCode).'.'.strtolower($subTopicCode);
			$categoryDataArray = array(
										'category_code' => $category,
										'standard_id' => $standardCode,
										'domain_id' =>$domainId,
										'subject_id' =>$subjectId,
										'subtopic_code' =>$subTopicCode,
										'subtopic_name' => $subTopicName,
										'headline' => $headLine,
										'description' =>$description,
										'created_by' => $adminName,
										'psv_code' =>$psvCode,	
										'created_date' =>$date,
										'modified_date' =>$date
			);
			
			$addCategoryData = $this->_tblCategoryInfo->addCategory($categoryDataArray);
			if(strpos($grade,'-')){
				$getRangeGrade = explode('-',$grade);
				if($addCategoryData){
					$differenOfGrade = $getRangeGrade[1]-$getRangeGrade[0];
					$addGrade = 0;
					$addGrade = $getRangeGrade[0];
					for($i=0;$i<=$differenOfGrade;$i++){
						$addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($addGrade, $addCategoryData, $subjectId,$domainId);
						$addGrade = $addGrade+1;
					}
					if($addQuesGrade){
						return true;
					
                                        }else{
                                            return false;
                                        }
				}

			}else{
				if($addCategoryData){
					$addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($gradeId, $addCategoryData, $subjectId,$domainId);
					if($addQuesGrade){
						return true;
						
                                        }else{
                                            return false;
                                        }


				}
			}
}
    public function importexcelcateAction() {
        $this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender();
        $countErorr = 0;
        echo "<pre>";
	$excel = new PhpExcelReader ();
	$data = array ();
        $fileNamePath = APPLICATION_PATH.'/../public/category/';
        $fileName = 'CategoryMatrix.xls';
        @chmod($fileNamePath.$fileName, 0777);
        $j = 3;
        if(file_exists($fileNamePath.$fileName)){
            $excel->read ($fileNamePath.$fileName);   
            foreach ( $excel->sheets as $sheets ) {
            for($i = 3; $i <= $sheets['numRows'];$i++){
                    if(empty($sheets['cells'][$i][2])){
                       $dataResultError[$i] = "BALCodeValue column not readable"; 
                       continue;
                    }
                    $result = $this->checkcategoryAction($sheets['cells'][$i][2]);
                    $resultData = Zend_Json::decode($result);
                    if($resultData['status']!='error'){
                    $checkdomainResult = $this->checkdomainAction($resultData['data']['domain']);
                    $checkdomainResultData = Zend_Json::decode($checkdomainResult);
                    $dataArray = array('category'=>$sheets['cells'][$i][2],
                        'standard_add'=>strtolower($resultData['data']['standard']),
                        'standard_id_add'=>$resultData['data']['standardId'],
                        'grade_add'=>$resultData['data']['grade'],
                        'domain_code_add'=>$resultData['data']['domain'],
                        'subject_id_add'=>$resultData['data']['subject_id'],
                        'framework_add'=>$resultData['data']['framework'],
                        'domain_code_id_add'=>$checkdomainResultData['domaindata']['domainId'],
                        'category_add'=>'',
                        'sub_topic_add'=>$resultData['data']['subtopic'],
                        'subtopic_name'=>$sheets['cells'][$i][8],
                        'headlines'=>'',
                        'category_desc'=>$sheets['cells'][$i][11]
                        );
                     if($checkdomainResultData['status'] =='error'){
                        $dataArray['domain_name_add'] = $sheets['cells'][$i][6];
                        }
                    $result = $this->_addImportCat($dataArray);
                    
                    
                    $dataResultSuccess[$i] = $result;
                    }else{
                        $dataResultError[$i] = $resultData['message']; 
                    }
                   
                }   
                break;
            }
            if(count($dataResultSuccess) >0){
                echo count($dataResultSuccess)." Category inserted";
            }
            
            if(count($dataResultError) >0){
                echo "<pre>";
                echo count($dataResultError)." Category not inserted".PHP_EOL;
                print_r($dataResultError);
            }else{
                $this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( count($dataResultSuccess)." categories are inserted into table successfully" );
                $this->_redirect ( '/admin/questions/categorylist' );
            }
        }
    }           

	public function checkcategoryAction($categoryImport = NULL)
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = $this->getRequest();
		$tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		$tblSubject        =  new Application_Model_DbTable_ChildSubject();
		$category = $request->getParam('category');
                if(empty($category)){
                    $category = $categoryImport;
                }
		$categoryToLower = strtolower($category);
		$checkCategoryExist = $tblCategoryInfo->checkCategoryExist($categoryToLower);
		if($checkCategoryExist==true){
			$verifyArray = array('status' => 'error',
									'message' => "Category already exist",
									'data' => null
			);
			$response = Zend_Json::encode($verifyArray);
                        if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
		}

		$breakCategory =  explode('.',$category);
		$standard = strtolower($breakCategory[0]);
		$frameWork = $breakCategory[1].'.'.$breakCategory[2];

		$tblStandardInfo = new Application_Model_DbTable_CategoriesStandards();
		$standardDataFetch = array('standard_id','name');
		$fetchAllStandardsData = $tblStandardInfo->fetchAllStandard($standardDataFetch);
		$standardArray =  array();

		for($i=0;$i<count($fetchAllStandardsData);$i++){
			$standardArray[] = strtolower($fetchAllStandardsData[$i]['name']);
		}
		if(!(in_array($standard,$standardArray))){

			$verifyArray = array('status' => 'error',
									'message' => "Standard does not match",
									'data' => null
			);
			$response = Zend_Json::encode($verifyArray);
			if(!empty($categoryImport)){
                        return $response;
			}else{                          
			echo $response;
			exit();
                        }

		}else{

			$standardId = '';
			for($i=0;$i<count($fetchAllStandardsData);$i++){
				if($standard==strtolower($fetchAllStandardsData[$i]['name'])){
					$standardId = $fetchAllStandardsData[$i]['standard_id'];
				}
			}
			if($standard=='ccss'){
				if(strtolower($frameWork)=='ela-literacy.ccra'){

					$frameToLower = strtolower($frameWork);
					$getFrameworkData = array('subject_id');
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validateelaliteracyccra($category);
					$domain = $breakCategory[3];
                                        $nth = $this->_nthstrpos($category, '.', 4, true);
                                        $subtopic = substr($category, $nth+1); 
					//$subtopic =  $breakCategory[4];
					$grade = $breakCategory[4];
					$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										'standard' => $breakCategory[0],
										'standardId' =>$standardId,
										'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of else if condition of Framework
				else if(strtolower($breakCategory[1])=='ela-literacy'){
					$frameToLower = strtolower($breakCategory[1]);
					$getFrameworkData = array('subject_id');
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validateelaliteracy($category);
					$domain = $breakCategory[2];
					$grade = 	$breakCategory[3];
                                        $nth = $this->_nthstrpos($category, '.', 4, true);
                                        $subtopic = substr($category, $nth+1); 
					//$subtopic =  $breakCategory[4];

					$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$breakCategory[1],
										'standard' => $breakCategory[0],
										'standardId' =>$standardId,
										'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of if condition of Framework
				else if(strtolower($frameWork)=='math.content'){
					$getFrameworkData = array('subject_id');
					$frameToLower = strtolower($frameWork);
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validatemathcontent($category);

					$grade = 	$breakCategory[3];
					$domain = $breakCategory[4];
                                        $nth = $this->_nthstrpos($category, '.', 5, true);
                                        $subtopic = substr($category, $nth+1); 
					//$subtopic1 =  $breakCategory[5];
					//$subtopic2 =  $breakCategory[6];
					//$subtopic = $subtopic1.'.'.$subtopic2;


					$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										 'standard' => $breakCategory[0],
										  'standardId' =>$standardId,
											'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of else if condition of Framework

				else{
					$verifyArray = array('status' => 'error',
									'message' => 'Invalid framework',
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
				}
			}else if($standard=='bal'){




				if(validateFrameWork($breakCategory[1]==false)){
					$verifyArray = array('status' => 'error',
									'message' => 'Invalid framework',
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					if(!empty($categoryImport)){
                        return $response;
			}else{
			echo $response;
			exit();
                        }
				}

				$getFrameworkData = array('subject_id');
				$frameWork =  $breakCategory[1];
				$frameToLower = strtolower($breakCategory[1]);
				$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
				$subjectId    = $getFrameWorkId['subject_id'];
				$checkFields = $this->_validatebal($category);

				$grade = 	$breakCategory[2];
				$domain = $breakCategory[3];
				$subtopic1 =  $breakCategory[4];
				//$subtopic2 =  $breakCategory[5];
				//$subtopic = $subtopic1.'.'.$subtopic2;
				$subtopic = $grade.'.'.$subtopic1;

				$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										 'standard' => $breakCategory[0],
										  'standardId' =>$standardId,
											'subject_id' =>$subjectId); 
				if($checkFields!='success'){
					$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					echo $response;
					exit();
				}else{
					$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
					);
					$response = Zend_Json::encode($verifyArray);
                                        if(isset($categoryImport) && !empty($categoryImport)){
                                            return $response;
                                        }else{
                                            echo $response;
                                            exit();
                                        }
				}

			}

		}// end of else condition of standard if condition

	}

        private function _nthstrpos($str, $substr, $n, $stri = false)
        {
            if ($stri) {
                $str = strtolower($str);
                $substr = strtolower($substr);
            }
            $ct = 0;
            $pos = 0;
            while (($pos = strpos($str, $substr, $pos)) !== false) {
                if (++$ct == $n) {
                    return $pos;
                }
                $pos++;
            }
            return false;
        }
        private function _validatemathcontent($category)
	{
		$breakCategory =  explode('.',$category);
		$grade = $breakCategory[3];
		$domain = $breakCategory[4];
		$subtopic1 =  $breakCategory[5];
		$subtopic2 =  $breakCategory[6];
		$tblGrade =  new Application_Model_DbTable_ChildGrade();
		if(validateNotNull($grade)==false){

			return "Invalid grade";
			exit();
		}// end of if confition for null check for grade

		if(validateGrade($grade)==false){
			return "Invalid grade";
			exit();
		}else {
			if(strpos($grade,'-')){
					
				$getRangeGrade = explode('-',$grade);
				if($getRangeGrade[0]==null || $getRangeGrade[1]==null){
					return "Invalid grade";
					exit();
				}// end of if confition for null check for grade
				if($getRangeGrade[0]>$getRangeGrade[1]){
					return "Invalid grade";
					exit();
				}// end of if condition for grade greater check for grade
				$checkGradeRange1 = $tblGrade->checkGradeExist($getRangeGrade[0]);
				$checkGradeRange2 = $tblGrade->checkGradeExist($getRangeGrade[1]);
				if($checkGradeRange1==false || $checkGradeRange2==false ){
					return "Invalid grade";
					exit();
				}else if($checkGradeRange1==true && $checkGradeRange2==true ){
					if(validateNotNull($domain)==false){
						return "Invalid domain";
						exit();
					}
					if(validateDomain($domain)==false){
						return "Invalid domain";
						exit();
					}else{

						if(validateDomainLength($domain)==false){
							return "Invalid domain";
							exit();
						}else{
							if(validateNotNull($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicFirst($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}

							if(validateNotNull($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}
							
							if(validateSubTopicLength($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}
							else{
								return 'success';
								exit();
							}
						}

					}// end of else condition for check range in db

				}
			}else{
					
				$checkGradeRange = $tblGrade->checkGradeExist($grade);
				if($checkGradeRange==false){

					return "Invalid grade";
					exit();
				}else{

					if(validateNotNull($domain)==false){
						return "Invalid domain";
						exit();
					}
					if(validateDomain($domain)==false){
						return "Invalid domain";
						exit();
					}else{

						if(validateDomainLength($domain)==false){
							return "Invalid domain";
							exit();
						}else{
							if(validateNotNull($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicFirst($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}

							if(validateNotNull($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicLength($subtopic2)==false){
								return "Invalid sub topic";
								exit();
							}

							else{
								return 'success';
								exit();
							}

						}
					}
				}

			}

		}// end of else condition for check range in db


	}


	private function _validatebal($category)
	{
		$breakCategory =  explode('.',$category);
		$grade = 	$breakCategory[2];
		$domain = $breakCategory[3];
		$subtopic1 =  $breakCategory[4];
		//$subtopic2 =  $breakCategory[5];
        $subtopic2='';
		$tblGrade =  new Application_Model_DbTable_ChildGrade();
		if(validateNotNull($grade)==false){

			return "Invalid grade";
			exit();
		}// end of if confition for null check for grade

		if(validateGrade($grade)==false){
			return "Invalid grade";
			exit();
		}else {
			if(strpos($grade,'-')){
					
				$getRangeGrade = explode('-',$grade);
				if($getRangeGrade[0]==null || $getRangeGrade[1]==null){
					return "Invalid grade";
					exit();
				}// end of if confition for null check for grade
				if($getRangeGrade[0]>$getRangeGrade[1]){
					return "Invalid grade";
					exit();
				}// end of if condition for grade greater check for grade
				$checkGradeRange1 = $tblGrade->checkGradeExist($getRangeGrade[0]);
				$checkGradeRange2 = $tblGrade->checkGradeExist($getRangeGrade[1]);
				if($checkGradeRange1==false || $checkGradeRange2==false ){
					return "Invalid grade";
					exit();
				}else if($checkGradeRange1==true && $checkGradeRange2==true ){
					if(validateNotNull($domain)==false){
						return "Invalid domain";
						exit();
					}
					if(validateDomain($domain)==false){
						return "Invalid domain";
						exit();
					}else{

						if(validateDomainLength($domain)==false){
							return "Invalid domain";
							exit();
						}else{
							if(validateNotNull($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}

							/*if(validateNotNull($subtopic2)==false){
								return "Invalid Sub Topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic2)==false){
								return "Invalid Sub Topic";
								exit();
							}*/else{
								return 'success';
								exit();
							}
						}

					}// end of else condition for check range in db

				}
			}else{
					
				$checkGradeRange = $tblGrade->checkGradeExist($grade);
				if($checkGradeRange==false){

					return "Invalid grade";
					exit();
				}else{

					if(validateNotNull($domain)==false){
						return "Invalid domain";
						exit();
					}
					if(validateDomain($domain)==false){
						return "Invalid domain";
						exit();
					}else{

						if(validateDomainLength($domain)==false){
							return "Invalid domain";
							exit();
						}else{
							if(validateNotNull($subtopic1)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic1)==false){
								
								return "Invalid sub topic";
								exit();
							}
							/*if(validateNotNull($subtopic2)==false){
								
								return "Invalid Sub Topic";
								exit();
							}
							if(validateSubTopicSecond($subtopic2)==false){
								
								return "Invalid Sub Topic";
								exit();
							}
							if(validateSubTopicLength($subtopic2)==false){
								return "Invalid Sub Topic";
								exit();
							}*/
							else{
								return 'success';
								exit();
							}

						}
					}
				}

			}

		}// end of else condition for check range in db


	}


	private function _validateelaliteracy($category)
	{
		$breakCategory =  explode('.',$category);
		$domain = $breakCategory[2];
		$grade = 	$breakCategory[3];
		$subtopic =  $breakCategory[4];
		$tblGrade =  new Application_Model_DbTable_ChildGrade();

		if(validateNotNull($domain)==false){
			return "Invalid domain";
			exit();
		}
		if(validateDomain($domain)==false){
			return "Invalid domain";
			exit();
		}else{

			if(validateDomainLength($domain)==false){
				return "Invalid domain";
				exit();
			}else{
					
				if(validateNotNull($grade)==false){
					return "Invalid grade";
					exit();
				}else {
					$tblGrade =  new Application_Model_DbTable_ChildGrade();
					if(validateGrade($grade)==false){
						return "Invalid grade";
						exit();
					}// end of if confition for null check for grade

					if(strpos($grade,'-')){
							
						$getRangeGrade = explode('-',$grade);
						if($getRangeGrade[0]==null || $getRangeGrade[1]==null){

							return "Invalid grade";
							exit();
						}// end of if confition for null check for grade
						if($getRangeGrade[0]>$getRangeGrade[1]){

							return "Invalid grade";
							exit();
						}// end of if condition for grade greater check for grade
						$checkGradeRange1 = $tblGrade->checkGradeExist($getRangeGrade[0]);
						$checkGradeRange2 = $tblGrade->checkGradeExist($getRangeGrade[1]);
						if($checkGradeRange1==false || $checkGradeRange2==false ){
							return "Invalid grade";
							exit();
						}else if($checkGradeRange1==true && $checkGradeRange2==true ){
							if(validateNotNull($subtopic)==false ){
									
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopic($subtopic)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicLength($subtopic)==false){
								return "Invalid sub topic";
								exit();
							}
							else{
								return "success";
								exit();
							}

						}// end of else condition for check range in db

							
					}else{

						$checkGradeRange = $tblGrade->checkGradeExist($grade);
						if($checkGradeRange==false){
							return "Invalid grade";
							exit();
						}else{

							if(validateNotNull($subtopic)==false){

								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopic($subtopic)==false){
								return "Invalid sub topic";
								exit();
							}
							if(validateSubTopicLength($subtopic)==false){
								return "Invalid sub topic";
								exit();
							}

							else{
								return "success";
								exit();
							}

						}// end of else condition for check range in db
					}
				}
			}
		}

	}


	private function _validateelaliteracyccra($category)
	{
		$breakCategory =  explode('.',$category);
		$domain = $breakCategory[3];
		$subtopic =  $breakCategory[4];
		$grade =  $breakCategory[4]; 
		$tblGrade =  new Application_Model_DbTable_ChildGrade();

		if(validateNotNull($domain)==false){
			return "Invalid domain";
			exit();
		}
		if(validateDomain($domain)==false){
			return "Invalid domain";
			exit();
		}else{

			if(validateDomainLength($domain)==false){
				return "Invalid domain";
				exit();
			}else{

				
				if(validateNotNull($grade)==false){
					return "Invalid grade";
					exit();
				}else {
					$tblGrade =  new Application_Model_DbTable_ChildGrade();
					
					if(validateGrade($grade)==false){
						return "Invalid grade";
						exit();
					}
					$checkGradeRange = $tblGrade->checkGradeExist($grade);
						if($checkGradeRange==false){
							return "Invalid grade";
							exit();
						}else{/* end of if confition for null check for grade*/
								if(validateNotNull($subtopic)==null ){

							return "Invalid sub topic";
							exit();
						}
						if(validateSubTopicLiteracyCCRA($subtopic)==false){
							return "Invalid sub topic";
							exit();
						}
						if(validateSubTopicLength($subtopic)==false){
							return "Invalid sub topic";
							exit();
						}
						else{
							return "success";
							exit();
						}
					}
				}
					
			}// end of else condition for check range in db
		}
	}


	public function checkdomainAction($ImportDomainId = NULL)
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
                
                if(isset($ImportDomainId) && !empty($ImportDomainId)){
                    $domainCode = $ImportDomainId;
                }  else {
                    $domainCode = $request->getParam('domainid');
                }
		$tblDomainInfo =  new Application_Model_DbTable_QuestionDomain();
		$checkDomainExist = $tblDomainInfo->existDomain($domainCode);
		if($checkDomainExist==false){
			$domainArray = array('status' => 'error',
									'message' => 'notexist',
									'domaindata' => null
			);
			$response = Zend_Json::encode($domainArray);
                        if(isset($ImportDomainId) && !empty($ImportDomainId)){
                            
                            return $response;
                            
			
                }  else {
                    echo $response;
			exit();
                }
			
		}else if($checkDomainExist==true){
			$getDomainData = $tblDomainInfo->getDomainData($domainCode);
			$domainCode = $getDomainData->code;
			$domainName = $getDomainData->name;
			$domainId = $getDomainData->domain_id;
			$domainDataArray = array('domaincode' =>$domainCode,
									  'domainname' => $domainName,
										'domainId' => $domainId);
			$domainArray = array('status' => 'success',
									'message' => 'exist',
									'domaindata' => $domainDataArray
			);
			$response = Zend_Json::encode($domainArray);
                         if(isset($ImportDomainId) && !empty($ImportDomainId)){
                            return $response;
                         }else{
                            echo $response;
			exit();
                         }
		}
	}


	public function getdescriptionAction()
	{
		require_once APPLICATION_PATH.'/../library/simple_html_dom.php';
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$descUrl = $request->getPost('a');
		$URL = $descUrl; // Service url
                //$lastChar = substr(trim($URL), -1);
                //if($lastChar != '/'){
                  // $URL = $URL.'/'; 
               // }
                $ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		$output = curl_exec($ch);
		$html = str_get_html($output);
		$elem = $html->find('section[class=content clearfix]', 0);
		echo strip_tags($elem);
		exit();



	}


	public function categorylistAction()
	{
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
		}
		$request 					= $this->getRequest();
		$searchData = $request->getParam('seachdata');
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$tblCategories =  new Application_Model_DbTable_QuestionCategories();
		$getCategoriesList = $tblCategories->getCategoriesInfo($searchData);
		$totalRecords	= count($getCategoriesList);
		$page			=$this->_getParam('page',1);
		$paginator 		= Zend_Paginator::factory($getCategoriesList);
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->categoryData     = $paginator;
		$this->view->totalRecord  = $totalRecords;
		$this->view->currentPage  = $page;
		$this->view->searchdata = $searchData;
	}


	//function to displat the list of questions
	public function questionlistAction()
	{
		$request 					= $this->getRequest();
		$tblStandards				= new Application_Model_DbTable_CategoriesStandards();
		$tblquestion				= new Application_Model_DbTable_ChildQuestion();
		$tblcategory				= new Application_Model_DbTable_QuestionCategories();
		$tblframeworks				= new Application_Model_DbTable_Framework();
		$tblgrades					= new Application_Model_DbTable_ChildGrade();
		$tbldomain					= new Application_Model_DbTable_QuestionDomain();
		$tblAdmin					= new Application_Model_DbTable_UserLogin();
		$adminUsers					= $tblAdmin->GetAllAdminUsers();
		$this->view->adminUser		= $adminUsers;
		$standard_id				= $request->getParam('standard');
		$framework_id				= $request->getParam('framework');
		$searchBy					= $request->getParam('search');
		if($framework_id != '')
		{
			$whereframe 		= "subject_id = $framework_id";
			$framework 			= $tblframeworks->fetchRow($whereframe);
			$this->view->frame		= $framework;
		}
		$grade_id 					= $request->getParam('grades');
		if($grade_id != '')
		{
			$wheregrade 		= "grades_id = $grade_id";
			$grade 			= $tblgrades->fetchRow($wheregrade);
			$this->view->grade		= $grade;
		}
		$domain_id 					= $request->getParam('domain');
		if($domain_id != '')
		{
			$wheredoamin 		= "domain_id = $domain_id";
			$domain 				= $tbldomain->fetchRow($wheredoamin);
			$this->view->domain		= $domain;
		}
		$search_question= $request->getParam('search_question');
		$data 			= $tblcategory->getCategory($standard_id,$framework_id,$grade_id,$domain_id);
        $categoryId = '';
		foreach ($data as $d)
		{
			if($categoryId == '')
			{
				$categoryId = $d['category_id'];
			}
			elseif(!empty($d['category_id']))
			{
				$categoryId = $categoryId.' , '.$d['category_id'];
			}
		}
		$this->view->search_question 	= $search_question;
		$this->view->domain_id		 	= $domain_id;
		$this->view->grade_id			= $grade_id;
		$this->view->framework_id		= $framework_id;
		$this->view->standard_id		= $standard_id;
		$this->view->searchBy			= $searchBy;

		if($categoryId == '')
		{
			$categoryId = '';
			$questions					= $tblquestion->getQuestionsbyCatGrade($categoryId,$grade_id);
		}
		if($categoryId)
		{
			$questions					= $tblquestion->getQuestionsbyCatGrade($categoryId,$grade_id);
		}
		if($search_question != '')
		{
			$questions					= $tblquestion->getQuestionsbysearch($search_question);
		}
		if(!empty($searchBy)) {
			$questions					= $tblquestion->GetQuestionSearchCreatedandModifiedBy($searchBy);
		}
		
		$standards					= $tblStandards->fetchAll();
		$this->view->standard		= $standards;
		$page						= $this->_getParam('page',1);
		$paginator 					= Zend_Paginator::factory($questions);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->questions     = $paginator;
		$this->view->totalRecord  = count($questions);
		$this->view->currentPage  = $page;
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}


	//function to get list of framework on the basis of std.
	public function getframeworkAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblframework	= new Application_Model_DbTable_Framework();
		$request 		= $this->getRequest();
		$standard_id 	= $request->getParam('standard_id');
		$where 			= "standard_id = $standard_id";
		$data			= $tblframework->getAllframeworks($where);
		if($data)
		{
			$frameworkArray = array('message' => null,
									'status' =>'success', 								  
									'data' => $data, 								  
									'count' => count($data)); 			
			$response = Zend_Json::encode($frameworkArray);
			echo $response;
			exit();
		}
		else
		{
			$frameworkArray = array('message' => 'No framework found',
												 'status' =>'blank', 								  
												 'data' => '', 								  
												 'count' => 0); 			
			$response = Zend_Json::encode($frameworkArray);
			echo $response;
			exit();
		}
	}



	//function to get list of framework on the basis of framework.
	public function getgradeanddomainAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$request 		= $this->getRequest();
		$framework_id 	= $request->getParam('framework_id');
		$standard_id 	= $request->getParam('standard_id');
		$datagrade		= $tblcategory->getgradesInfo($framework_id,$standard_id);
		$datadomain		= $tblcategory->getdomainInfo($framework_id);
		if($datagrade != '' && $datadomain != '')
		{
			$frameworkArray = array('message' => null,
									'status' =>'success', 								  
									'datagrade' => $datagrade, 								  
									'countgrade' => count($datagrade), 								  
									'datadomain' => $datadomain, 								  
									'countdomain' => count($datadomain)); 			
			$response = Zend_Json::encode($frameworkArray);
			echo $response;
			exit();
		}
	}
	//function to ADD questions
	public function questionAction()
	{
            
		$request 				= $this->getRequest();
		$tblStandards                           = new Application_Model_DbTable_CategoriesStandards();
		$tblcategory                            = new Application_Model_DbTable_QuestionCategories();
		$tblquestion                            = new Application_Model_DbTable_ChildQuestion();
		$tbloptions				= new Application_Model_DbTable_QuestionOptions();
		$standards				= $tblStandards->fetchAll();
		$categories				= $tblcategory->fetchAll();
		$this->view->categories = $categories;
		$this->view->standard	= $standards;
		$adminInfoSession = new Zend_Session_Namespace('adminInfo');
		$adminLogindata = $adminInfoSession->adminData;
		$adminLoginId			= $adminLogindata->admin_user_id;
		$adminName = $adminLogindata->name;
		$edit_id				= $request->getParam('edit_id');
                $type				= $request->getParam('type');
		$id				= $request->getParam('id');
		/***************image path for question*********************/
		$fileGetPath = PUBLIC_PATH.'/images/equation_image/';
		/**************************variables for assigned null************************/
		$questionEquation = '';
		$questionEquationImage = '';
		$questionEquationImgName = '';
		$explanationEquation='';				
		$explanationEquationImg ='';
		$explanationEquationImgName ='';
                
                $answerExplanationEquation='';				
		$answerExplanationEquationImg ='';
		$answerExplanationEquationImgName ='';
                
		$optionEquation ='';
		$optionEquationImg ='';
		$optionEquationImgName ='';
		$wrognEquation ='';
		$wrognEquationImg = '';
		$wrognEquationImgName ='';
		$wrogn1Equation ='';
		$wrogn1EquationImg ='';
		$wrogn1EquationImgName ='';
		$wrogn2Equation ='';
		$wrogn2EquationImg ='';
		$wrogn2EquationImgName ='';
		$strDescQuestion = '';
		$csvEqImagesQuestion = '';
		$wrongAnswer1Display = '';
		$wrongAnswer2Display = '';
		$wrongAnswer3Display = '';
		$userQuestionIdData = $tblquestion->fetchRow("bal_question_id = '$edit_id'");
		$createdById = $userQuestionIdData['created_byid'];
		if($edit_id != '')
		{
                        $this->view->headTitle(ADMIN_EDITQUES);
			$whereq 				= "bal_question_id = $edit_id";
			$question 				= $tblquestion->fetchRow($whereq);
			$whereo 				= "question_id = $edit_id";
			$options				= $tbloptions->fetchAll($whereo);
			$this->view->question 	= $question;
			$this->view->options	= $options;
			$this->view->edit_id	= $edit_id;
		}
		if(!empty($id)) {
                        $this->view->headTitle(ADMIN_ADDQUES);
			$whereq 				= "bal_question_id = $id";
			$question 				= $tblquestion->fetchRow($whereq);
			$whereo 				= "question_id = $id";
			$options				= $tbloptions->fetchAll($whereo);
			$this->view->question 	= $question;
			$this->view->options	= $options;
			$this->view->id	= $id;
		}
                if(empty($edit_id) && empty($id)){
                    $this->view->headTitle(ADMIN_ADDQUES);
                }
                $this->view->returnPage = $type;
		if($request->isPost())
		{
			//print_r($request->getPost());die;
			$frameWork			= $request->getPost('frameHidden');
			$expiryDate		= $request->getPost('expiryDate');
			$edit_id			= $request->getPost('edit_id');
			$copyId				= $request->getPost('copyId');
			$category_id		= $request->getPost('category');
			$question			= $request->getPost('question');
			$right_answer		= $request->getPost('right_answer');
			$wrong_answer1		= $request->getPost('wrong_answer1');
			/* if(empty($wrong_answer1)){
				$wrong_answer1		= $request->getPost('wrong_answer11');
			} */
			$wrong_answer2		= $request->getPost('wrong_answer2');
			$wrong_answer3		= $request->getPost('wrong_answer3');
			/* if(empty($wrong_answer3)){
				$wrong_answer3		= $request->getPost('wrong_answer33');
			} */
				
			$difficulty_level	= $request->getPost('difficulty_level');
			$grade				= $request->getPost('grades_val');
			$url_of_question	= $request->getPost('url_of_question');
			$question_image_url	= $request->getPost('question_image_url');
			$answer_image_url	= $request->getPost('answer_image_url');
			$explanation		= $request->getPost('explanation');
                        $answerExplanation	= $request->getPost('answer_explanation');
			$set_question		= $request->getPost('set_question');
			$refer_book_name	= $request->getPost('refer_book_name');
			$refer_book_chapter	= $request->getPost('refer_book_chapter');
			$refer_article_url	= $request->getPost('refer_article_url');
			$wolframalphaquery	= $request->getPost('wolframalphaquery');
			if($frameWork == 26 || $frameWork == 7) {
					
				/**************block for quation text and theire images****************/
				$strReplacedStringQuestion = $this->replaceEQTagsWithImage ( 'PSVEQ[', $question );
				$arrImageUrlsQuestion = $this->getEQTags ( 'PSVEQ[', $question );
				$newFileName = 'question';
				$arrEqImageNamesQuestion = $this->createdImages($arrImageUrlsQuestion,$newFileName);
				$strDescQuestion = $strReplacedStringQuestion;
				$csvEqImagesQuestion = implode ( ',', $arrEqImageNamesQuestion );
				$oldImageNameQuestion = $request->getPost('old_image_name_question');
				if(!empty($oldImageNameQuestion)){
					$this->removedEquestionsImages($oldImageNameQuestion);
				}
				/**************block for explationsa text and theire images****************/
				$strReplacedStringExplations = $this->replaceEQTagsWithImage ( 'PSVEQ[', $explanation );
				$arrImageUrlsExplations = $this->getEQTags ( 'PSVEQ[', $explanation );
				$newFileName = 'explations';
				$arrEqImageNamesExplations = $this->createdImages($arrImageUrlsExplations,$newFileName);
				$strDescExplations = $strReplacedStringExplations;
				$csvEqImagesExplations = implode ( ',', $arrEqImageNamesExplations );
				$oldImageNameExplation = $request->getPost('old_image_name_explation');
				if(!empty($oldImageNameExplation)){
					$this->removedEquestionsImages($oldImageNameExplation);
				}
                                
                                /**************block for Answer Explanation text and theire images****************/
				$strReplacedStringAnswerExplations = $this->replaceEQTagsWithImage ( 'PSVEQ[', $answerExplanation );
				$arrImageUrlsAnswerExplations = $this->getEQTags ( 'PSVEQ[', $answerExplanation );
				$newFileName = 'answerexplations';
				$arrEqImageNamesAnswerExplations = $this->createdImages($arrImageUrlsAnswerExplations,$newFileName);
				$strDescAnswerExplations = $strReplacedStringAnswerExplations;
				$csvEqImagesAnswerExplations = implode ( ',', $arrEqImageNamesAnswerExplations );
				$oldImageNameAnswerExplation = $request->getPost('old_image_name_answer_explation');
				if(!empty($oldImageNameAnswerExplation)){
					$this->removedEquestionsImages($oldImageNameAnswerExplation);
				}
                                
				/**************block for rigth answer text and theire images****************/
				
				
				
				/**************block for rigth answer text and theire images****************/
				$strReplacedStringRight = $this->replaceEQTagsWithImage ( 'PSVEQ[', $right_answer );
				$arrImageUrlsQuestionRight = $this->getEQTags ( 'PSVEQ[', $right_answer );
				$newFileName = 'right';
				$arrEqImageNamesRight = $this->createdImages($arrImageUrlsQuestionRight,$newFileName);
				$rightAnswerDisplay = $strReplacedStringRight == 'equation_tags'?'equation_tags':$strReplacedStringRight;
				$optionEquationImgName = implode ( ',', $arrEqImageNamesRight );
				$oldImageNameRight = $request->getPost('old_image_name_right');
				if(!empty($oldImageNameRight)){
					$this->removedEquestionsImages($oldImageNameRight);
				}
				
				/**************End block for rigth answer text and theire images****************/
				$strReplacedStringWrong1 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrong_answer1 );
				$arrImageUrlsWrong1 = $this->getEQTags ( 'PSVEQ[', $wrong_answer1 );
				$newFileName = 'wrong1';
				$arrEqImageNamesWrong1 = $this->createdImages($arrImageUrlsWrong1,$newFileName);
				$wrongAnswer1Display = $strReplacedStringWrong1 == 'equation_tags'?'equation_tags':$strReplacedStringWrong1;
				$wrognEquationImgName = implode ( ',', $arrEqImageNamesWrong1 );
				$oldImageNameWrong1  = $request->getPost('old_image_name_wrong1');
				if(!empty($oldImageNameWrong1)){
					$this->removedEquestionsImages($oldImageNameWrong1);
				}
				
				/**************block for quation text and theire images****************/
				
				$strReplacedStringWrong2 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrong_answer2 );
				$arrImageUrlsWrong2 = $this->getEQTags ( 'PSVEQ[', $wrong_answer2 );
				$newFileName = 'wrong2';
				$arrEqImageNamesWrong2 = $this->createdImages($arrImageUrlsWrong2,$newFileName);
				$wrongAnswer2Display = $strReplacedStringWrong2  == 'equation_tags'?'equation_tags':$strReplacedStringWrong2;
				$wrogn1EquationImgName = implode ( ',', $arrEqImageNamesWrong2 );
				$oldImageNameWrong2  = $request->getPost('old_image_name_wrong2');
				
				if(!empty($oldImageNameWrong2)){
					$this->removedEquestionsImages($oldImageNameWrong2);
				}
				/**************End block for rigth answer text and theire images****************/
				
				$strReplacedStringWrong3 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrong_answer3 );
				$arrImageUrlsWrong3 = $this->getEQTags ( 'PSVEQ[', $wrong_answer3 );
				$newFileName = 'wrong3';
				$arrEqImageNamesWrong3 = $this->createdImages($arrImageUrlsWrong3,$newFileName);
				$wrongAnswer3Display = $strReplacedStringWrong3  == 'equation_tags'?'equation_tags':$strReplacedStringWrong3;
				$wrogn2EquationImgName = implode ( ',', $arrEqImageNamesWrong3 );
				$oldImageNameWrong3  = $request->getPost('old_image_name_wrong3');
				if(!empty($oldImageNameWrong3)){
					$this->removedEquestionsImages($oldImageNameWrong3);
				}
				/**************End block for rigth answer text and theire images****************/
				
				
				
				
				
				
				
				
				
				/*****************variables for question equation in latex editior used***********************/
				//$questionEquation = $request->getPost('question_equation');
				//$questionEquationImage = $request->getPost('question_equation_image1');
				//$questionEquationImgName = '';
				//$oldImageNameQuestion = $request->getPost('old_image_name_question');
				/***************** End variables for question equation in latex editior used***********************/
				
				/*****************Variables for question equation Explations in latex editior used***********************/
				//$explanationEquation=$request->getPost('explanation_equation');
				//$explanationEquationImg =$request->getPost('explanation_equation_image1');
				//$explanationEquationImgName ='';
				//$oldImageNameExplation = $request->getPost('old_image_name_explation');
				//$oldImageNameQuestion = $request->getPost('old_image_name_question');
				//$oldImageNameRight = $request->getPost('old_image_name_right');
				//$oldImageNameWrong1  = $request->getPost('old_image_name_wrong1');
				//$oldImageNameWrong1  = $request->getPost('old_image_name_wrong2');
				//$oldImageNameWrong1  = $request->getPost('old_image_name_wrong3');
				/*****************End variables for question equation Explations in latex editior used***********************/
				
				/*****************variables for right option  equation in latex editior used***********************/
				//$optionEquation =$request->getPost('right_answer_equation');
				//$optionEquationImg =$request->getPost('right_equation_image1');
				//$oldImageNameRight = $request->getPost('old_image_name_right');
				//$optionEquationImgName ='';
				/*****************end variables for right option  equation in latex editior used***********************/
				/*****************variables for Wrong Answer1 equation in latex editior used***********************/
				//$wrognEquation =$request->getPost('wrong_answer1_equation');
				//$wrognEquationImg = $request->getPost('wrong_equation_image1');
				//$wrognEquationImgName ='';
				//$oldImageNameWrong1  = $request->getPost('old_image_name_wrong1');
				
				/*****************End variables for wrong_answer1 equation in latex editior used***********************/
				/*****************variables for wrong answer equation in latex editior used***********************/
				//$wrogn1Equation =$request->getPost('wrong_answer2_equation');;
				//$wrogn1EquationImg =$request->getPost('wrong_answer2_equation_image2');
				//$wrogn1EquationImgName ='';
				//$oldImageNameWrong2  = $request->getPost('old_image_name_wrong2');
				///*****************variables for question equation in latex editior used***********************/
				/*****************variables for question equation in latex editior used***********************/
				//$wrogn2Equation =$request->getPost('wrong_answer3_equation');
				//$wrogn2EquationImg =$request->getPost('wrong_answer2_equation_image3');
				//$wrogn2EquationImgName ='';
				//$oldImageNameWrong3  = $request->getPost('old_image_name_wrong3');
				/*****************variables for question equation in latex editior used***********************/
				/*****************variables for question equation in latex editior used***********************/
				/**************************images created**************************/
			}
			$adminInfoSession->questionData	= $request->getPost();
			//echo "<pre>";print_r($request->getPost());die;
/***************************end for All files***********************/			
			//check existance of question
			/*if($edit_id != '')
			{
			$where				= "category_id = $category_id and question = '$question' and bal_question_id != $edit_id";
			}
			else
			{
			$where				= "category_id = $category_id and question = '$question'";
			}
			$checkqexistance	= $tblquestion->fetchRow($where);
			if($checkqexistance)
			{
			$this->view->error = 'Question already exist with this category';
			return false;
			}*/
			if(!isset($category_id,$question,$right_answer,$wrong_answer1,$wrong_answer2,$wrong_answer3,$difficulty_level,$grade) ||
			($category_id != '' || $question != '' || $right_answer != '' || $wrong_answer1 != '' || $wrong_answer2 != '' || $wrong_answer3 != ''
			|| $difficulty_level != '' || $grade != '' ))
			{
				if($edit_id == '')
				{
					try
					{
						if(!empty($copyId)) {
							$updateCopyUrlData = array('copy_created' => 'T');
							$wherequestionCopy	= "bal_question_id = $copyId";
							$resultUpdateCopyData	= $tblquestion->updateQuestion($updateCopyUrlData,$wherequestionCopy);
						}
						$whereQuestionUrl = "url_of_question = '$url_of_question'";
						$checkQurl		= $tblquestion->fetchRow($whereQuestionUrl);
						if(count($checkQurl) != 0)
						{
							$this->view->error = "Question of URL is already exist";
							return false;
						}
						if(!empty($expiryDate)){
						$dataqInsert  	= array('question' 			=> $question,
												'question_equation' =>$questionEquation,
												'question_equation_image' =>$questionEquationImage,
												'question_equation_image_name' =>$questionEquationImgName,
												'difficulty_level'	=> $difficulty_level,
												'grade_id'			=> $grade,
												'category_id'		=> $category_id,
												'url_of_question'	=> $url_of_question,
												'question_image_url'=> $question_image_url,
												'answer_image_url' 	=> $answer_image_url,
												'explanation'		=> $explanation,
												'explanation_equation' =>$explanationEquation,
												'explanation_equation_image' =>$explanationEquationImg,
												'explanation_equation_image_name' =>$explanationEquationImgName,
                                                    
                                                                                                'answer_explanation'		=> $answerExplanation,
												'answer_explanation_equation' =>$strDescAnswerExplations,											
												'answer_explanation_image_name' =>$csvEqImagesAnswerExplations,
                                                    
                                                    
                                                    
												'set_question'		=> $set_question,
												'refer_book_name'	=> $refer_book_name,
												'refer_book_chapter'=> $refer_book_chapter,
												'refer_article_url' => $refer_article_url,
												'wolframalphaquery'	=> $wolframalphaquery,
												'expiry_date'		=> $expiryDate,
												'created_by'		=> $adminName,
												'created_byid'		=> $adminLoginId,
												'created_date'		=> date('Y-m-d H:i:s'),
												'date'				=> date('Y-m-d H:i:s'));
						}else{
							$dataqInsert  	= array(
									
									'question' => $question,
									'question_display' => $strDescQuestion,
									'question_equation_images' => $csvEqImagesQuestion,
									'question_equation' => $questionEquation,
									'question_equation_image' => $questionEquationImage,
									'question_equation_image_name' => $questionEquationImgName,
									'difficulty_level'	=> $difficulty_level,
												'grade_id'			=> $grade,
												'category_id'		=> $category_id,
												'url_of_question'	=> $url_of_question,
												'question_image_url'=> $question_image_url,
												'answer_image_url' 	=> $answer_image_url,
												'explanation'		=> $explanation,
												'explanation_equation' =>$strDescExplations,
												'explanation_equation_image' =>$explanationEquationImg,
												'explanation_equation_image_name' =>$csvEqImagesExplations,
                                                                                                 'answer_explanation'		=> $answerExplanation,
												'answer_explanation_equation' =>$strDescAnswerExplations,											
												'answer_explanation_image_name' =>$csvEqImagesAnswerExplations,
												'set_question'		=> $set_question,
												'refer_book_name'	=> $refer_book_name,
												'refer_book_chapter'=> $refer_book_chapter,
												'refer_article_url' => $refer_article_url,
												'wolframalphaquery'	=> $wolframalphaquery,
												'expiry_date'		=> $expiryDate,
												'created_by'		=> $adminName,
												'created_byid'		=> $adminLoginId,
												'created_date'		=> date('Y-m-d H:i:s'),
												'date'			    => date('Y-m-d H:i:s'));
						}
						$questionId		= $tblquestion->addQuestion($dataqInsert);
						$dataOrinsert	= array('option' 		=> $right_answer,
												'question_id'	=> $questionId,
												'option_equation'=>$rightAnswerDisplay,
												'option_equation_image'=>$optionEquationImg,
												'option_equation_image_name'=>$optionEquationImgName,
												'answer'		=> 'Y');
						$ranswerId		= $tbloptions->addOptionforQuestion($dataOrinsert);
						$dataOw1insert	= array('option' 		=> $wrong_answer1,
												'question_id'	=> $questionId,
								'option_equation'=>$wrongAnswer1Display,
								'option_equation_image'=>$wrognEquationImg,
								'option_equation_image_name'=>$wrognEquationImgName,
												'answer'		=> 'N');
						$w1answerId		= $tbloptions->addOptionforQuestion($dataOw1insert);
						$dataOw2insert	= array('option' 		=> $wrong_answer2,
												'question_id'	=> $questionId,
								'option_equation'=>$wrongAnswer2Display,
								'option_equation_image'=>$wrogn1EquationImg,
								'option_equation_image_name'=>$wrogn1EquationImgName,
												'answer'		=> 'N');
						$w2answerId		= $tbloptions->addOptionforQuestion($dataOw2insert);
						$dataOw3insert	= array('option' 		=> $wrong_answer3,
												'question_id'	=> $questionId,
										'option_equation'=>$wrongAnswer3Display,
								'option_equation_image'=>$wrogn2EquationImg,
								'option_equation_image_name'=>$wrogn2EquationImgName,
												'answer'		=> 'N');
						$w3answerId		= $tbloptions->addOptionforQuestion($dataOw3insert);
						$this->_helper->getHelper('FlashMessenger')
						->addMessage('Question added successfully');
						unset($adminInfoSession->questionData);
						$this->_redirect('/admin/questions/draftquestionlist');
					}
					catch (Exception $e)
					{
						$this->view->error = $e->getMessage();
						return false;
					}
				}
				else
				{
					$returnPage		= $request->getPost('returnPageUrl');
					$rightoptionid		= $request->getPost('rightoptionid');
					$wrongoptionid1		= $request->getPost('wrongoptionid1');
					$wrongoptionid2		= $request->getPost('wrongoptionid2');
					$wrongoptionid3		= $request->getPost('wrongoptionid3');
					try
					{
						if($category_id == '')
						{
							$category_id 	= $request->getPost('category_id_hidden');
						}
						if(!empty($expiryDate)){
							$dataqUpdate  	= array('question' 			=> $question,
												'question_equation' =>$questionEquation,
												'question_equation_image' =>$questionEquationImage,
												'question_equation_image_name' =>$questionEquationImgName,
												'difficulty_level'	=> $difficulty_level,
												'grade_id'			=> $grade,
												'category_id'		=> $category_id,
												'url_of_question'	=> $url_of_question,
												'question_image_url'=> $question_image_url,
												'answer_image_url' 	=> $answer_image_url,
												'explanation'		=> $explanation,
												'explanation_equation' =>$explanationEquation,
												'explanation_equation_image' =>$explanationEquationImg,
												'explanation_equation_image_name' =>$explanationEquationImgName,
                                                                                                'answer_explanation'		=> $answerExplanation,
												'answer_explanation_equation' =>$strDescAnswerExplations,											
												'answer_explanation_image_name' =>$csvEqImagesAnswerExplations,
												'set_question'		=> $set_question,
												'refer_book_name'	=> $refer_book_name,
												'refer_book_chapter'=> $refer_book_chapter,
												'refer_article_url' => $refer_article_url,
												'wolframalphaquery'	=> $wolframalphaquery,
												'expiry_date'		=> $expiryDate,
												'modified_by'		=> $adminName,
												'modified_byid'		=> $adminLoginId,
												'modified_Date'		=> date('Y-m-d H:i:s'),
												'date'				=> date('Y-m-d H:i:s'));
						} else {
							$dataqUpdate  	= array('question' 			=> $question,
									'question_display' => $strDescQuestion,
									'question_equation_images' => $csvEqImagesQuestion,
												'question_equation' =>$questionEquation,
												'question_equation_image' =>$questionEquationImage,
												'question_equation_image_name' =>$questionEquationImgName,
												'difficulty_level'	=> $difficulty_level,
												'grade_id'			=> $grade,
												'category_id'		=> $category_id,
												'url_of_question'	=> $url_of_question,
												'question_image_url'=> $question_image_url,
												'answer_image_url' 	=> $answer_image_url,
												'explanation'		=> $explanation,
												'explanation_equation' =>$strDescExplations,
												'explanation_equation_image' =>$explanationEquationImg,
												'explanation_equation_image_name' =>$csvEqImagesExplations,
                                                                                                'answer_explanation'		=> $answerExplanation,
												'answer_explanation_equation' =>$strDescAnswerExplations,											
												'answer_explanation_image_name' =>$csvEqImagesAnswerExplations,
												'set_question'		=> $set_question,
												'refer_book_name'	=> $refer_book_name,
												'refer_book_chapter'=> $refer_book_chapter,
												'refer_article_url' => $refer_article_url,
												'wolframalphaquery'	=> $wolframalphaquery,
												'expiry_date'		=> $expiryDate,
												'modified_by'		=> $adminName,
												'modified_byid'		=> $adminLoginId,
												'modified_Date'		=> date('Y-m-d H:i:s'),
												'date'				=> date('Y-m-d H:i:s'));
							
						}
						 if($createdById != $adminLoginId){
							
						} 
						$wherequestion	= "bal_question_id = $edit_id";
						$questionId		= $tblquestion->updateQuestion($dataqUpdate,$wherequestion);
						$questionData	= $tblquestion->fetchRow($questionId);
						
						if($questionData['created_byid'] != $adminLoginId){
							$dataqUpdateE = array('edited_question' => '1');
							$resUpdateEdit	= $tblquestion->updateQuestion($dataqUpdateE,$wherequestion);
						} 
						$dataOrupdate	= array('option' 		=> $right_answer,
												'question_id'	=> $edit_id,
								'option_equation'=>$rightAnswerDisplay,
								'option_equation_image'=>$optionEquationImg,
								'option_equation_image_name'=>$optionEquationImgName,
									
												'answer'		=> 'Y');
						
						
						$whererighto	= "question_option_id = $rightoptionid";
						$ranswerId		= $tbloptions->updateOptionforQuestion($dataOrupdate,$whererighto);
						$dataOw1update	= array('option' 		=> $wrong_answer1,
												'question_id'	=> $edit_id,
								'option_equation'=>$wrongAnswer1Display,
								'option_equation_image'=>$wrognEquationImg,
								'option_equation_image_name'=>$wrognEquationImgName,
												'answer'		=> 'N');
						$wherewrongo1	= "question_option_id = $wrongoptionid1";
						$w1answerId		= $tbloptions->updateOptionforQuestion($dataOw1update,$wherewrongo1);
						$dataOw2update	= array('option' 		=> $wrong_answer2,
												'question_id'	=> $edit_id,
								'option_equation'=>$wrongAnswer2Display,
								'option_equation_image'=>$wrogn1EquationImg,
								'option_equation_image_name'=>$wrogn1EquationImgName,
												'answer'		=> 'N');
						$wherewrongo2	= "question_option_id = $wrongoptionid2";
						$w2answerId		= $tbloptions->updateOptionforQuestion($dataOw2update,$wherewrongo2);
						$dataOw3update	= array('option' 		=> $wrong_answer3,
												'question_id'	=> $edit_id,
								'option_equation'=>$wrongAnswer3Display,
								'option_equation_image'=>$wrogn2EquationImg,
								'option_equation_image_name'=>$wrogn2EquationImgName,
												'answer'		=> 'N');
						$wherewrongo3	= "question_option_id = $wrongoptionid3";
						$w3answerId		= $tbloptions->updateOptionforQuestion($dataOw3update,$wherewrongo3);
						$this->view->success 	= 'Question updated successfully';
						$this->_helper->getHelper('FlashMessenger')
						->addMessage('Question updated successfully');
						unset($adminInfoSession->questionData);
						$this->_redirect('/admin/questions/'.$returnPage);
					}
					catch (Exception $e)
					{
						$this->view->error = $e->getMessage();
						return false;
					}
				}
			}
			else
			{
				$this->view->error 	= "Required fields can't be blank";
				return false;
			}
		}
	}

	
	
	
	
	//function used to get category on basis of domain id ,grade id, stanadard id and framework id
	public function getcategoryAction()
	{

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request 		= $this->getRequest();
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$standard_id	= $request->getParam('standard_id');
		$framework_id	= $request->getParam('framework_id');
		$grade_id 		= $request->getParam('grade_id');
		$domain_id 		= $request->getParam('domain_id');
		$data 			= $tblcategory->getCategory($standard_id,$framework_id,$grade_id,$domain_id);
		$categorykArray = array('message' => null,
								'status' =>'success', 								  
								'data' => $data, 								  
								'count' => count($data)); 			
		$response = Zend_Json::encode($categorykArray);
		echo $response;
		exit();
	}




	public function getgradeAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblcategorygrade	= new Application_Model_DbTable_QuestionCategoryGrade();
		$request 			= $this->getRequest();
		$category_id 		= $request->getParam('category_id');
		$datagrade			= $tblcategorygrade->getgradesInfoUsingCategory($category_id);
		$gradeArray = array('message' => null,
								'status' =>'success', 								  
								'datagrade' => $datagrade, 								  
								'countgrade' => count($datagrade)); 			
		$response = Zend_Json::encode($gradeArray);
		echo $response;
		exit();
	}

	/*public function deletequestionAction()
	 {

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request 			= $this->getRequest();
		$tblquestion			= new Application_Model_DbTable_ChildQuestion();
		$tbloptions				= new Application_Model_DbTable_QuestionOptions();
		$question_id 		= $request->getParam('id');
		if($question_id != '')
		{
		$whereo 		= "question_id = $question_id";
		$whereq 		= "bal_question_id = $question_id";
		$removeoption	= $tbloptions->deleteOptions($whereo);
		if($removeoption)
		{
		$removeq 	= $tblquestion->deleteQuestion($whereq);
		if($removeq)
		{
		$resultArray = array('message' => "Question deleted successfully",
		'status' =>'success',
		);
		$response = Zend_Json::encode($resultArray);
		echo $response;
		exit();
		}
		else
		{
		$resultArray = array('message' => "Error while deleting question",
		'status' =>'error',
		);
		$response = Zend_Json::encode($resultArray);
		echo $response;
		exit();
		}
		}
		else
		{
		$resultArray = array('message' => "Error while deleting options for the question",
		'status' =>'error',
		);
		$response = Zend_Json::encode($resultArray);
		echo $response;
		exit();
		}
		}
		}*/

	//function to show details of question
	public function questiondetailAction()
	{

		$request 				= $this->getRequest();
		$tblquestion			= new Application_Model_DbTable_ChildQuestion();
		$tbloptions				= new Application_Model_DbTable_QuestionOptions();
		$id				= $request->getParam('id');
                $type				= $request->getParam('type');
		if($id != '')
		{
			$question 				= $tblquestion->getQuestionsbyid($id);
			$whereo 				= "question_id = $id";
			$options				= $tbloptions->fetchAll($whereo);
			$this->view->question 	= $question;
			$this->view->options	= $options;
			$this->view->edit_id	= $id;
                        $this->view->returnPage = $type;
		}
	}


	//function to get domain value on basis of grades
	public function getdomainvaluesAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$request 			= $this->getRequest();
		$grade_id			= $request->getParam('grade_id');
		$framework_id 		= $request->getParam('framework_id');
		$standard_id 		= $request->getParam('standard_id');
		$datadomain			= $tblcategory->getdomainInfobygrade($grade_id,$framework_id,$standard_id);
		$domainArray = array('message' => null,
								'status' =>'success', 								  
								'datadomain' => $datadomain, 								  
								'count' => count($datadomain)); 			
		$response = Zend_Json::encode($domainArray);
		echo $response;
		exit();
	}
	
	/**
	 * @desc Function to notification if first category is added having specified grade and subject
	 * @param $grade,$framework
	 * @author suman khatri on 8th May 2014
         * @updated suman khatri on 14th May 2014
	 * @return ArrayIterator
	 */
	private function _addNotificationForkidIfNewSubjectAdded($grade, $framework, $subjectId,$domainId,$domainName){
            
            $childInfo = $this->_childTbObj->getChildParentInfoGradeWise($grade);
            $insertNoti = $this->_addNotificationForNewSubject($framework, $grade, $childInfo, $subjectId,$domainId,$domainName,'subject');
            return true;
            
		//add notification if category contains new subject and grade
		if(strpos($grade,'-')){
			$getRangeGrade = explode('-',$grade);
			$differenOfGrade = $getRangeGrade[1]-$getRangeGrade[0];
			$addGrade = 0;
			$addGrade = $getRangeGrade[0];
			for($i=0;$i<=$differenOfGrade;$i++){
				$checkCategoryUsingGradeAndSubject = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($subjectId, $addGrade);
                                if(count($checkCategoryUsingGradeAndSubject) == 0){
                                        $childInfo = $this->_childTbObj->getChildParentInfoGradeWise($addGrade, $subjectId);
					$insertNoti = $this->_addNotificationForNewSubject($framework, $addGrade, $childInfo, $subjectId,$domainId,$domainName,'subject');
				}else{
                                    $checkCategoryUsingGradeAndSubjectAndDomain = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($subjectId, $addGrade,$domainId);
                                    if(count($checkCategoryUsingGradeAndSubjectAndDomain) == 0){
                                        $childIds = $this->_childTbObj->getChildParentInfoGradeWiseHavingSpecifiedDomian($addGrade,$subjectId,$domainId);
                                        $childInfo = $this->_childTbObj->getChildParentInfoGradeWiseNotHavingSpecifiedDomian($addGrade,$subjectId,$domainId,$childIds);
                                        $insertNoti = $this->_addNotificationForNewSubject($framework, $addGrade, $childInfo, $subjectId,$domainId,$domainName,'domain');
                                    }
                                }
				$addGrade = $addGrade+1;
			}
		}else{
			$checkCategoryUsingGradeAndSubject = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($subjectId, $grade);
                        if(count($checkCategoryUsingGradeAndSubject) == 0){
                            $childInfo = $this->_childTbObj->getChildParentInfoGradeWise($grade, $subjectId);
                            $insertNoti = $this->_addNotificationForNewSubject($framework, $grade, $childInfo, $subjectId,$domainId,$domainName,'subject');
			}else{
                            $checkCategoryUsingGradeAndSubjectAndDomain = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($subjectId, $grade,$domainId);
                           
                            if(count($checkCategoryUsingGradeAndSubjectAndDomain) == 0){ 
                                $childIds = $this->_childTbObj->getChildParentInfoGradeWiseHavingSpecifiedDomian($grade,$subjectId,$domainId);
                                $childInfo = $this->_childTbObj->getChildParentInfoGradeWiseNotHavingSpecifiedDomian($grade,$subjectId,$domainId,$childIds);
                                $insertNoti = $this->_addNotificationForNewSubject($framework, $grade, $childInfo, $subjectId,$domainId,$domainName,'domain');
                            }                           
                        }
		}
	}
        

        /**
	 * @desc Function to add notification if first category is added having specified grade and subject
	 * @param $framework,$grade,$childInfo
	 * @author suman khatri on 9th May 2014
	 * @return nill
	 */
        private function _addNotificationForNewSubject($framework,$grade,$childInfo,$subjectId,$doimanId,$domainName,$typeOfNoti){
            $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
            if(!empty($childInfo) && $childInfo != null){
                foreach ($childInfo as $dataChild){
                    $userId = $dataChild['user_id'];
                    $childId = $dataChild['child_id'];
                    $parentId = $dataChild['parent_id'];
                    if($parentId != 0){
                        $childGender = '';
                        if($dataChild['gender'] == 'B'){
                            $childGender = 'his';
                        }else if($dataChild['gender'] == 'G'){
                            $childGender = 'her';
                        }
                        
                        if(strtolower($framework) == 'math.content') {
                            $framework = 'math';
                        }
                        
                        //if($typeOfNoti == 'subject'){
                           // $message = "has ".strtoupper($framework)." in ".$childGender." grade ".$grade.", update Learning customization";
                       // }else{
                            $message = "has ".strtoupper($domainName)." in ".strtoupper($framework)." ".$childGender." grade ".$grade.", update Learning customization";
                       // }
                            
                        $insertNotifdata = array(
                                        'user_id' => $userId,
                                        'notification_type' => 'NEW_SUBJECT',
                                        'description' => $message,
                                        'seen_by_user' => 'N',
                                        'deleted' => 'N',
                                        'child_device_id' => 0,
                                        'childe_name' => '',
                                        'child_id' => $childId,
                                        'created_date' => date('Y-m-d H:i:s')
                        );
                        $resnotifi = $tblParentNofic->AddParentNotification($insertNotifdata);
                    }  else {
                        // $addChildSubject = $this->_childTbObj->insertChildSubject ($childId, $subjectId,$doimanId);
                    }

                }
            }
        }

	public function deletecategoryAction()
	{
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$tblSequence	= new Application_Model_DbTable_ChildQuestionSequence();
        $tblFramework   = new Application_Model_DbTable_Framework();
        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
		$request        = $this->getRequest();
		$categoryId  	= $request->id;
                /*$getOldCategoryData = $tblcategory->categoryInfo($categoryId);
                $oldGradeId = $getOldCategoryData['grade_id'];
                $oldsubjectId = $getOldCategoryData['subject_id'];
                $whereFramework = "subject_id = $oldsubjectId";
                $oldFrameworlData = $tblFramework->getAllframeworks($whereFramework);
                $oldFrameworkName = $oldFrameworlData[0]['subject_name'];
                $checkCategoryUsingGradeAndSubject = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($oldsubjectId,$oldGradeId);
                if(count($checkCategoryUsingGradeAndSubject) == 1){
                    $childInfo = $this->_childTbObj->getChildParentInfoGradeWise($oldGradeId,$oldsubjectId);
                    foreach ($childInfo as $dataChild){
                         $userId = $dataChild['user_id'];
                         $insertNotifdata = array(
                             'user_id' => $userId,
                             'notification_type' => 'SUBJECT_DELETE',
                             'description' => strtoupper($oldFrameworkName)." is deleted from grade ".$oldGradeId,
                             'seen_by_user' => 'N',
                             'deleted' => 'N',
                             'child_device_id' => 0,
                             'childe_name' => '',
                             'child_id' => '',
                             'created_date' => date('Y-m-d H:i:s')
                         );
                         $resnotifi = $tblParentNofic->AddParentNotification($insertNotifdata);
                     }
                }*/
		try{
			$deleteCat	=	$tblcategory->deleteCategory($categoryId);
			$removeSequence	= $tblSequence->DeleteSequence($categoryId);
			if($deleteCat){
				$this->_helper->getHelper('FlashMessenger')
				->addMessage('Category deleted successfully.');
				$this->_redirect('admin/questions/categorylist');
			}
		}catch (Exception $ex) {
			$this->view->error =
			$this->_helper->getHelper('FlashMessenger')
			->addMessage(' Error: '.$ex->getMessage());
			$this->_redirect('admin/questions/categorylist');
		}
	}


	public function viewcategorydataAction()
	{

		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$request 		   = $this->getRequest();
		$categoryId  			   = $request->id;
		//$grd    			   = $request->grd;
		//$this->view->grd   = $grd;
		$getCategoryData = $tblcategory->categoryInfo($categoryId);
		$this->view->categoryData = $getCategoryData;
	}

	public function editcategoryAction()
	{

		$tblcategory	   = new Application_Model_DbTable_QuestionCategories();
		$request 		   = $this->getRequest();
		$categoryId  	   = $request->id;
		$getCategoryData   = $tblcategory->categoryInfo($categoryId);
		$this->view->categoryData = $getCategoryData;

		if($request->isPost()){
			$date = date('Y-m-d H:i:s');
			$headLine     = $request->getPost('headlines');
			$categoryDesc = $request->getPost('category_desc');
			$categoryId   = $request->getPost('category_id');
			$adminInfoSession = new Zend_Session_Namespace('adminInfo');
			$adminLogindata = $adminInfoSession->adminData;
			$adminName = $adminLogindata->name;
			$updateCategoryData = array(
										'headline' =>$headLine,
										'description' =>$categoryDesc,
										'modified_by' => $adminName,
										 'modified_date' =>$date);
			try{
				$updateCategoryData = $tblcategory->updateCategoryData($updateCategoryData,$categoryId);
				$this->_helper->flashMessenger->addMessage('Data updated successfully');
				$this->_redirect('/admin/questions/categorylist');

			}catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
				
		}

	}



	/*
	 * function to add category
	 */
	public function updatecategorycodeAction()
	{
		$request         = $this->getRequest();
		$categoryId  	 = $request->id;
		$categoryId      = base64_decode($categoryId);
		$tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		$fetchDataArray  = array('category_code');
		$categoryData = $tblCategoryInfo->getParticulaCategoryData($categoryId, $fetchDataArray);
		$this->view->categoryData = $categoryData;
		$this->view->categoryId = $categoryId;
		$tblDomainInfo   = new Application_Model_DbTable_QuestionDomain();
		$tblGrade        =  new Application_Model_DbTable_ChildGrade();
		$tblSubject        =  new Application_Model_DbTable_ChildSubject();
		$tblQuestionGrade = new Application_Model_DbTable_QuestionGrade();
		$tblChild			= new Application_Model_DbTable_ChildInfo();
		$tblSequence		= new Application_Model_DbTable_ChildQuestionSequence();
                $tblFramework           = new Application_Model_DbTable_Framework();
                $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
		if($request->isPost()){

			$oldCategoryId   = $request->getPost('old_category_id');
			$domainName   = $request->getPost('domain_name_add');
			$domainCode   = $request->getPost('domain_code_add');
			$category     = $request->getPost('category');
			$category     = strtolower($category);
			$subjectId    = $request->getPost('subject_id_add');
			$standard     = $request->getPost('standard_add');
			$standardCode = $request->getPost('standard_id_add');
			$domainId     = $request->getPost('domain_code_id_add');
			$grade        = $request->getPost('grade_add');
			$description  = $request->getPost('category_desc');
			$gradeDataArray  = array('grades_id');
			$gradeInfo    = $tblGrade->getGradeDataOnGradeName($grade,$gradeDataArray);
			$gradeId = $gradeInfo['grades_id'];

			$subTopicCode        = $request->getPost('sub_topic_add');
			$subTopicName        = $request->getPost('subtopic_name');
			$headLine        = $request->getPost('headlines');
			$framework = $request->getPost('framework_add');

			$checkCategoryExist = $tblCategoryInfo->checkCategoryExist($category);
			if($checkCategoryExist==true){
				$this->view->error = 'Category already exist';
				return false;
			}
			if($domainName!=null){
				$domainNameData = array('code' =>$domainCode,
									'name' =>$domainName);
				$domainId  = $tblDomainInfo->addDomainData($domainNameData);
			}else{
				$domainId = $domainId;
                                $where = "domain_id = $domainId";
                                $domainData = $this->_tblDomainInfo->fetchRow($where);
                                $domainName = $domainData['name'];
			}
			$date = date('Y-m-d H:i:s');
			if(strtolower($standard)=='bal'){
				if($subjectId==null){
					
					$frameworkData = array('standard_id'=>$standardCode,'subject_name'=>$framework,'created_date' => $date);
					$addframework = $tblSubject->addSubjectInfo($frameworkData);
                                        $this->sendPushToAllDevices($addframework, $framework);
					$subjectId = $addframework;

				}
			}

			$removeCategory = $tblCategoryInfo->deleteCategory($oldCategoryId);
			$removeSequence	= $tblSequence->DeleteSequence($oldCategoryId);
			$adminInfoSession = new Zend_Session_Namespace('adminInfo');
			$adminLogindata = $adminInfoSession->adminData;
			$adminName = $adminLogindata->name;
			//add notification if category contains new subject 
			//$addNoti = $this->_addNotificationForkidIfNewSubjectAdded($grade, $framework, $subjectId,$domainId,$domainName);
                        //add notification if category contains new subject end 
			$psvCode = 'psv.'.strtolower($standard).'.'.strtolower($framework).'.'.strtolower($grade).'.'.strtolower($domainCode).'.'.strtolower($subTopicCode);
			$categoryDataArray = array(
                                                'category_code' => $category,
                                                'standard_id' => $standardCode,
                                                'domain_id' =>$domainId,
                                                'subject_id' =>$subjectId,
                                                'subtopic_code' =>$subTopicCode,
                                                'subtopic_name' => $subTopicName,
                                                'headline' => $headLine,
                                                'description' =>$description,
                                                'psv_code' => $psvCode,
                                                'created_by' => $adminName,
                                                'created_date' =>$date,
                                                'modified_date' =>$date
                                            );

			$addCategoryData = $tblCategoryInfo->addCategory($categoryDataArray);

			if(strpos($grade,'-')){
				$getRangeGrade = explode('-',$grade);
				if($addCategoryData){

					$differenOfGrade = $getRangeGrade[1]-$getRangeGrade[0];
					$addGrade = 0;
					$addGrade = $getRangeGrade[0];
					for($i=0;$i<=$differenOfGrade;$i++){
                                            $addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($addGrade, $addCategoryData, $subjectId,$domainId);
                                            $addGrade = $addGrade+1;
					}
					if($addQuesGrade){
						$this->_helper->flashMessenger->addMessage('Data added successfully');
						$this->_redirect('/admin/questions/categorylist');
					}else{
						$this->view->error = 'Error adding question grade data';
					}
				}else{
					$this->view->error = 'Error adding category data';
				}

			}else{
				if($addCategoryData){
					$addQuesGrade = $this->_addCategoryToChildHavingGradeAndSubject($grade, $addCategoryData, $subjectId,$domainId);
					if($addQuesGrade){
						$this->_helper->flashMessenger->addMessage('Data added successfully');
						$this->_redirect('/admin/questions/categorylist');
					}else{
						$this->view->error = 'Error adding question grade data';
					}


				}else{
					$this->view->error = 'Error adding category data';
				}
			}
                        /*if($addQuesGrade){
                                $getOldCategoryData = $tblCategoryInfo->categoryInfo($oldCategoryId);
                                $oldGradeId = $getOldCategoryData['grade_id'];
                                $oldsubjectId = $getOldCategoryData['subject_id'];
                                $whereFramework = "subject_id = $oldsubjectId";
                                $oldFrameworlData = $tblFramework->getAllframeworks($whereFramework);
                                $oldFrameworkName = $oldFrameworlData[0]['subject_name'];
                                $checkCategoryUsingGradeAndSubject = $this->_categoryTbObj->checkCategoryUsingGradeAndSubject($oldsubjectId,$oldGradeId);
                                if(count($checkCategoryUsingGradeAndSubject) == 1){
                                    $childInfo = $this->_childTbObj->getChildParentInfoGradeWise($oldGradeId,$oldsubjectId);
                                    foreach ($childInfo as $dataChild){
                                         $userId = $dataChild['user_id'];
                                         $insertNotifdata = array(
                                             'user_id' => $userId,
                                             'notification_type' => 'SUBJECT_DELETE',
                                             'description' => strtoupper($oldFrameworkName)." is deleted from grade ".$oldGradeId.".You can add another subject from learning customization.",
                                             'seen_by_user' => 'N',
                                             'deleted' => 'N',
                                             'child_device_id' => 0,
                                             'childe_name' => '',
                                             'child_id' => '',
                                             'created_date' => date('Y-m-d H:i:s')
                                         );
                                         $resnotifi = $tblParentNofic->AddParentNotification($insertNotifdata);
                                     }
                                }
                                $removeSequence	= $tblSequence->DeleteSequence($oldCategoryId);
                                $removeCategory = $tblCategoryInfo->deleteCategory($oldCategoryId);
                                $this->_helper->flashMessenger->addMessage('Data added successfully');
                                $this->_redirect('/admin/questions/categorylist');
                        }else{
                                $this->view->error = 'Error adding Question Grade data';
                        }*/
		}
	}

	public function checkcategorynewAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = $this->getRequest();
		$tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		$tblSubject        =  new Application_Model_DbTable_ChildSubject();
		$category = $request->getParam('category');
		$oldCategoryId = $request->getParam('oldcategoryid');
		$categoryToLower = strtolower($category);
		$checkCategoryExist = $tblCategoryInfo->checkCategoryExistForOldCategory($categoryToLower,$oldCategoryId);
		if($checkCategoryExist==true){
			$verifyArray = array('status' => 'error',
									'message' => "Category already exist",
									'data' => null
			);
			$response = Zend_Json::encode($verifyArray);
			echo $response;
			exit();
		}

		$breakCategory =  explode('.',$category);
		$standard = strtolower($breakCategory[0]);
		$frameWork = $breakCategory[1].'.'.$breakCategory[2];

		$tblStandardInfo = new Application_Model_DbTable_CategoriesStandards();
		$standardDataFetch = array('standard_id','name');
		$fetchAllStandardsData = $tblStandardInfo->fetchAllStandard($standardDataFetch);
		$standardArray =  array();

		for($i=0;$i<count($fetchAllStandardsData);$i++){
                    $standardArray[] = strtolower($fetchAllStandardsData[$i]['name']);
		}
		if(!(in_array($standard,$standardArray))){
                    $verifyArray = array('status' => 'error',
                                        'message' => "Standard does not match",
                                        'data' => null
                                        );
                    $response = Zend_Json::encode($verifyArray);
                    echo $response;
                    exit();

			$verifyArray = array('status' => 'error',
									'message' => "Standard does not match",
									'data' => null
			);
			$response = Zend_Json::encode($verifyArray);
			echo $response;
			exit();

		}else{

			$standardId = '';
			for($i=0;$i<count($fetchAllStandardsData);$i++){
				if($standard==strtolower($fetchAllStandardsData[$i]['name'])){
					$standardId = $fetchAllStandardsData[$i]['standard_id'];
				}
			}
			if($standard=='ccss'){
				if(strtolower($frameWork)=='ela-literacy.ccra'){
					$frameToLower = strtolower($frameWork);
					$getFrameworkData = array('subject_id');
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validateelaliteracyccra($category);
					$domain = $breakCategory[3];
					//$subtopic =  $breakCategory[4];
                                        $nth = $this->_nthstrpos($category, '.', 4, true);
                                        $subtopic = substr($category, $nth+1); 
                                        $grade = $breakCategory[4];
					$dataarray = array('domain' =>$domain,
									'grade' => $grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										'standard' => $breakCategory[0],
										'standardId' =>$standardId,
										'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of else if condition of Framework
				else if(strtolower($breakCategory[1])=='ela-literacy'){
					$frameToLower = strtolower($breakCategory[1]);
					$getFrameworkData = array('subject_id');
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validateelaliteracy($category);
					$domain = $breakCategory[2];
					$grade = 	$breakCategory[3];
					//$subtopic =  $breakCategory[4];
                                        $nth = $this->_nthstrpos($category, '.', 4, true);
                                        $subtopic = substr($category, $nth+1); 
					$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$breakCategory[1],
										'standard' => $breakCategory[0],
										'standardId' =>$standardId,
										'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of if condition of Framework
				else if(strtolower($frameWork)=='math.content'){

					$getFrameworkData = array('subject_id');
					$frameToLower = strtolower($frameWork);
					$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
					$subjectId    = $getFrameWorkId['subject_id'];
					$checkFields = $this->_validatemathcontent($category);

					$grade = 	$breakCategory[3];
					$domain = $breakCategory[4];
					//$subtopic1 =  $breakCategory[5];
					//$subtopic2 =  $breakCategory[6];
					//$subtopic = $subtopic1.'.'.$subtopic2;
                                        $nth = $this->_nthstrpos($category, '.', 5, true);
                                        $subtopic = substr($category, $nth+1);

					$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										 'standard' => $breakCategory[0],
										  'standardId' =>$standardId,
											'subject_id' =>$subjectId); 
					if($checkFields!='success'){
						$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}else{
						$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
						);
						$response = Zend_Json::encode($verifyArray);
						echo $response;
						exit();
					}

				}// end of else if condition of Framework

				else{
					$verifyArray = array('status' => 'error',
									'message' => 'Invalid framework',
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					echo $response;
					exit();
				}
			}else if($standard=='bal'){




				if(validateFrameWork($breakCategory[1]==false)){
					$verifyArray = array('status' => 'error',
									'message' => 'Invalid framework',
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					echo $response;
					exit();
				}

				$getFrameworkData = array('subject_id');
				$frameWork =  $breakCategory[1];
				$frameToLower = strtolower($breakCategory[1]);
				$getFrameWorkId = $tblSubject->getSubjectDataOnFrameworkName($frameToLower,$getFrameworkData);
				$subjectId    = $getFrameWorkId['subject_id'];
				$checkFields = $this->_validatebal($category);

				$grade = 	$breakCategory[2];
				$domain = $breakCategory[3];
				$subtopic1 =  $breakCategory[4];
				$subtopic2 =  $breakCategory[5];
				$subtopic = $subtopic1.'.'.$subtopic2;

				$dataarray = array('domain' =>$domain,
									'grade' =>$grade,
									 'subtopic' => $subtopic,
										'framework' =>$frameWork,
										 'standard' => $breakCategory[0],
										  'standardId' =>$standardId,
											'subject_id' =>$subjectId); 
				if($checkFields!='success'){
					$verifyArray = array('status' => 'error',
									'message' => $checkFields,
									'data' => null
					);
					$response = Zend_Json::encode($verifyArray);
					echo $response;
					exit();
				}else{
					$verifyArray = array('status' => 'success',
									'message' => $checkFields,
									'data' => $dataarray
					);
					$response = Zend_Json::encode($verifyArray);
					echo $response;
					exit();
				}

			}

		}// end of else condition of standard if condition

	}





	/*//function to show details of question
	 public function importdataAction()
	 {

		require(APPLICATION_PATH.'/../library/XLSXReader.php');
		$xlsx = new XLSXReader('Sample_HindiQuestions.xlsx');

		$sheetNames = $xlsx->getSheetNames();
		foreach($sheetNames as $key=>$sheetName)
		{
		$sheet = $xlsx->getSheet($sheetName);
		//array2Table($sheet->getData());
		$data=$sheet->getData();
		$i=1;
		$error ='';
			
		while($i<count($data))
		{



		}die;
		if(!in_array("0", $res))
		{
		echo 'Question addedd successfully for '.$medium;die;
		}
		else
		{
		echo 'error';die;
		}
		}
		$request         = $this->getRequest();
		$tblCategoryInfo = new Application_Model_DbTable_QuestionCategories();
		$tblDomainInfo   = new Application_Model_DbTable_QuestionDomain();
		$tblGrade        =  new Application_Model_DbTable_ChildGrade();
		$tblSubject        =  new Application_Model_DbTable_ChildSubject();
		$tblQuestionGrade = new Application_Model_DbTable_QuestionGrade();
		$domainName   = $request->getPost('domain_name_add');
		$domainCode   = $request->getPost('domain_code_add');
		$category     = $request->getPost('category');
		$category     = strtolower($category);
		$subjectId     = $request->getPost('subject_id_add');
		$standard      = $request->getPost('standard_add');
		$standardCode = $request->getPost('standard_id_add');
		$domainId     = $request->getPost('domain_code_id_add');
		$grade        = $request->getPost('grade_add');
		$description  = $request->getPost('category_desc');
		$gradeDataArray  = array('grades_id');
		$gradeInfo    = $tblGrade->getGradeDataOnGradeName($grade,$gradeDataArray);
		$gradeId = $gradeInfo['grades_id'];
		$subTopicCode        = $request->getPost('sub_topic_add');
		$subTopicName        = $request->getPost('subtopic_name');
		$headLine        = $request->getPost('headlines');
		$checkCategoryExist = $tblCategoryInfo->checkCategoryExist($category);
		if($checkCategoryExist==true){
		$this->view->error = 'Category already exist';
		return false;
		}
		if($domainName!=null){
		$domainNameData = array('code' =>$domainCode,
		'name' =>$domainName);
		$domainId  = $tblDomainInfo->addDomainData($domainNameData);
		}else{
		$domainId = $domainId;
		}

		$date = date('Y-m-d H:i:s');
		if($standard=='bal'){
		if($subjectId==null){
		$framework = $request->getPost('framework_add');
		$frameworkData = array('subject_name'=>$framework,'created_date' => $date);
		$addframework = $tblSubject->addChildSubjectInfo($frameworkData);
		$subjectId = $subjectId;
			
		}
		}


		$categoryDataArray = array(
		'category_code' => $category,
		'standard_id' => $standardCode,
		'domain_id' =>$domainId,
		'subject_id' =>$subjectId,
		'subtopic_code' =>$subTopicCode,
		'subtopic_name' => $subTopicName,
		'headline' => $headLine,
		'description' =>$description,
		'created_date' =>$date
		);

		$addCategoryData = $tblCategoryInfo->addCategory($categoryDataArray);

		if(strpos($grade,'-')){
		$getRangeGrade = explode('-',$grade);
		if($addCategoryData){

		for($i=0;$i<count($getRangeGrade);$i++){

		$quesGradeDataArray = array('grade_id' => $getRangeGrade[$i],
		'category_id' => $addCategoryData
		);

		$addQuesGrade = $tblQuestionGrade->addGradeData($quesGradeDataArray);
		}
		if($addQuesGrade){
		$this->_helper->flashMessenger->addMessage('Data added successfully');
		$this->_redirect('/admin/questions/categorylist');
		}else{
		$this->view->error = 'Error adding Question Grade data';
		}
		}else{
		$this->view->error = 'Error adding Category data';
		}

		}else{
		if($addCategoryData){
		$quesGradeDataArray = array('grade_id' => $gradeId,
		'category_id' => $addCategoryData
		);

		$addQuesGrade = $tblQuestionGrade->addGradeData($quesGradeDataArray);
		if($addQuesGrade){
		$this->_helper->flashMessenger->addMessage('Data added successfully');
		$this->_redirect('/admin/questions/categorylist');
		}else{
		$this->view->error = 'Error adding Question Grade data';
		}


		}else{
		$this->view->error = 'Error adding Category data';
		}
		}

		}*/
	
	//function used to show question log asked to child
	public function questionlogAction()
	{
		
		$request 					= $this->getRequest();
		$tblStandards				= new Application_Model_DbTable_CategoriesStandards();
		$tblQuestion				= new Application_Model_DbTable_ChildQuestion();
		$tblCategory				= new Application_Model_DbTable_QuestionCategories();
		$tblFrameworks				= new Application_Model_DbTable_Framework();
		$tblGrades					= new Application_Model_DbTable_ChildGrade();
		$tblQuestionRequest			= new Application_Model_DbTable_ChildQuestionRequest();
		
		$grades						= $tblGrades->getAllGradeList();
		$this->view->grades			= $grades;
		$frameworkId				= $request->getParam('framework');
		if($frameworkId != ''){
			$whereframe 		= "subject_id = $frameworkId";
			$frameworkData		= $tblFrameworks->fetchRow($whereframe);
			$this->view->frame		= $frameworkData;
		}
		$gradeId 					= $request->getParam('grades');
		if($gradeId != ''){
			$wheregrade 		= "grades_id = $gradeId";
			$gradeData 			= $tblGrades->fetchRow($wheregrade);
			$this->view->grade	= $gradeData;
		}
		$fromDate				= $request->getParam('fromDate');
		$this->view->fromDate	= $fromDate;
		
		$endDate				= $request->getParam('endDate');
		$this->view->endDate	= $endDate;
		$Catagorydata 			= $tblCategory->getCategoryByGradeandFramework($frameworkId,$gradeId);
		
                $categoryId = '';
		foreach ($Catagorydata as $cData){
			if($categoryId == ''){
				$categoryId = $cData['category_id'];
			}
			elseif(!empty($cData['category_id'])){
				$categoryId = $categoryId.' , '.$cData['category_id'];
			}
		}
		$this->view->grade_id			= $gradeId;
		$this->view->framework_id		= $frameworkId;

		if($categoryId == ''){
			$categoryId = '';
			$questionData					= $tblQuestion->getAskedQuestions($categoryId,$fromDate,$endDate,$gradeId);
		}
		if($categoryId){
			$questionData					= $tblQuestion->getAskedQuestions($categoryId,$fromDate,$endDate,$gradeId);
		}
		
		$questionArray = array();
		$i = 0;
		foreach ($questionData as $qData)
		{
		    if(!empty($qData['question_id'])) {
		        $questionArray[$i]['question_id'] = $qData['question_id'];
		    }
		    
		    // Commented image generated for all data
		    // Now image is generated for paged records only in view
// 			if(!empty($qData['question_equation_images']) && $qData['question_equation_images'] != null) {
// 				$questionView = My_Functions::generateImages($qData['question_equation_images'], $qData['question_display']);
// 				$questionArray[$i]['question']						= $questionView;
// 			} else {
// 				$questionArray[$i]['question']						= $qData['question'];
// 			}
// 			if(!empty($qData['option_equation_image_name']) && $qData['option_equation_image_name'] != null) {
// 				$optionView = My_Functions::generateImages($qData['option_equation_image_name'], $qData['option_equation']);
// 				$questionArray[$i]['option']						= $optionView;
// 			} else {
// 				$questionArray[$i]['option']						= $qData['option'];
// 			}
			
		    // Assigned image data to view array
			$questionArray[$i]['question_equation_images'] = $qData['question_equation_images'];
			$questionArray[$i]['question_display'] = $qData['question_display'];
			$questionArray[$i]['question'] = $qData['question'];
			
			$questionArray[$i]['option_equation_image_name'] = $qData['option_equation_image_name'];
			$questionArray[$i]['option_equation'] = $qData['option_equation'];
			$questionArray[$i]['option'] = $qData['option'];
			
			//getting how many times question asked
			//$countAskedQuestion 								= $tblQuestionRequest->getQuestionAskedTime($qData['question_id']);
			//getting how many times question responded with right answer
			//$countQuestionRightTime								= $tblQuestionRequest->getQuestionRightAnswerTime($qData['question_id']);
			//getting how many times question responded with wrong answer
           // $countQuestionWrongTime								= $tblQuestionRequest->getQuestionWrongAnswerTime($qData['question_id']);
            //getting how many times question responded with no answer
            //$countQuestionUnanswerdTime							= $tblQuestionRequest->getQuestionUnAnswerTime($qData['question_id']);
            //getting responce time when question responded with correct answer
           // $countcorrectResponceTime							= $tblQuestionRequest->getCorrectResponceTime($qData['question_id']);
            //getting responce time when question responded with wrong answer
           // $countWrongResponceTime								= $tblQuestionRequest->getWrongResponceTime($qData['question_id']);
            $questionArray[$i]['countAskedQuestion']			= $qData['TotalCount'];
            $questionArray[$i]['countQuestionRightTime']		= $qData['TotalCorrect'];
            $questionArray[$i]['countQuestionWrongTime']		= $qData['TotalWrong'];
            $questionArray[$i]['countQuestionUnanswerdTime']	= $qData['UnAnswered'];
            $questionArray[$i]['countcorrectResponceTime']		= $qData['CorrectAnswerTime'];
            $questionArray[$i]['countWrongResponceTime']		= $qData['WrongAnswerTime'];
            $i++;
		}
		
		$gradelist						= $tblGrades->getAllGradeList();
		$this->view->gradelist			= $gradelist;
		$page							= $this->_getParam('page',1);
		$paginator 						= Zend_Paginator::factory($questionArray);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if(!empty($perPage)){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage 			= $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->questions     		= $paginator;
		$this->view->totalRecord  		= count($questionArray);
		$this->view->currentPage  		= $page;
		$flashMessages 					= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 				= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 					= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success 		= $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}
	
	//function to get ctaegory detail using category id
	public function getcategorydetailAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblCategory		= new Application_Model_DbTable_QuestionCategories();
		$tblCategoryGrade	= new Application_Model_DbTable_QuestionCategoryGrade();
		$request 			= $this->getRequest();
		$categoryId		= $request->getParam('category_id');
		$categryDetail		= $tblCategory->categoryInfo($categoryId);
		$gradeName 			= '';
		$categoryArray      = array();
		$categoryArray['stnd_name']		= $categryDetail['stnd_name'];
		$categoryArray['subject_name']	= $categryDetail['subject_name'];
		$categoryArray['name']			= $categryDetail['name'];
		$categoryArray['code']			= $categryDetail['code'];
		$categoryArray['subtopic_name']	= $categryDetail['subtopic_name'];
		$categoryArray['subject_id']	= $categryDetail['subject_id'];
		$gradeData			= $tblCategoryGrade->getgradesInfoUsingCategory($categoryId);
		if(count($gradeData) == 1)
		{
			$categoryArray['grade_name'] = $gradeData[0]['grade_name'];
		}elseif (count($gradeData) > 1)
		{
			for ($k = 0;$k<count($gradeData);$k++)
			{
				if($k == 0)
				{
					$gradeName = $gradeData[$k]['grade_name'];
				}
				if($k == (count($gradeData)-1))
				{
					$gradeName = $gradeName.'-'.$gradeData[$k]['grade_name'];
				}
			}
			$categoryArray['grade_name'] = $gradeName;
		}
		$domainArray = array('message' => null,
								'status' =>'success', 								  
								'dataCategory' => $categoryArray, 								  
								); 			
		$response = Zend_Json::encode($domainArray);
		echo $response;
		exit();
	}
	
	public function getsubjectlistAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$gradeId = $request->getParam("gradeId");
		$tblGrade = new Application_Model_DbTable_ChildGrade();
		$tblcategrade	= new Application_Model_DbTable_QuestionCategoryGrade();
		try{

			$getSubjectList =  $tblcategrade->getSubjectListOnGrade($gradeId);
			//$getSubjectList =  $tblGrade->getSubjectListOnGrade($gradeId);
			$subCount       =  count($getSubjectList);
			$subjectListFinal = array('subject_list' => $getSubjectList,
					'total_subject' =>$subCount);
			$subjectList = Zend_Json::encode($subjectListFinal);
			echo $subjectList;
			exit();
		}catch(Exception $e){
			return  $e->getMessage();
			exit();
		}
	}
	
	/**
	 * @desc controller function to show question list to be expiring in next 7 days 
	 * @param int standard,framework,grades,domain,search,search_question
	 * @author Suman khatri
	 */	
	public function expiringquestionlistAction()
	{
		$request 					= $this->getRequest();
		$tblquestion				= new Application_Model_DbTable_ChildQuestion();
		$searchBy					= $request->getParam('search');
		$fromDate				= $request->getParam('fromDate');
		$this->view->fromDate	= $fromDate;
		
		$endDate				= $request->getParam('endDate');
		$this->view->endDate	= $endDate;
		
		$search_question= $request->getParam('search_question');
		$this->view->search_question 	= $search_question;
		
		
		$questions					= $tblquestion->getQuestionsForExpiring($fromDate,$endDate);
		
		if($search_question != '')
		{
			$questions					= $tblquestion->getQuestionsbysearchForExpiring($search_question);
		}
	
		
		$page						= $this->_getParam('page',1);
		$paginator 					= Zend_Paginator::factory($questions);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->questions     = $paginator;
		$this->view->totalRecord  = count($questions);
		$this->view->currentPage  = $page;
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}
	
/**
	 * @desc controller function to show question list to be expiring in next 7 days 
	 * @param int standard,framework,grades,domain,search,search_question
	 * @author Suman khatri
	 */	
	public function expiredquestionlistAction()
	{
		$request 					= $this->getRequest();
		$tblquestion				= new Application_Model_DbTable_ChildQuestion();
		$search_question= $request->getParam('search_question');
		$this->view->search_question 	= $search_question;
		
		
		$questions = $tblquestion->getQuestionsForExpired();
		
		if($search_question != '')
		{
			$questions = $tblquestion->getQuestionsbysearchForExpired($search_question);
		}
	
		
		$page						= $this->_getParam('page',1);
		$paginator 					= Zend_Paginator::factory($questions);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->questions     = $paginator;
		$this->view->totalRecord  = count($questions);
		$this->view->currentPage  = $page;
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}
	
	/**
	 * @desc controller function to show question list to be expiring in next 7 days as notification
	 * @param Nill
	 * @author Suman khatri
	 */	
	public function getnotificationAction()
	{
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request 					= $this->getRequest();
		$tblquestion				= new Application_Model_DbTable_ChildQuestion();//create model file for ChildQuestion
		$todayDate 					= date("Y-m-d");//date return today server date and time.
		$endDateWeek 				= date('Y-m-d', strtotime("+6 days")); //get date next sat.
		try{
			$questions					= $tblquestion->getQuestionsForNotification($todayDate,$endDateWeek);
			$queCount       =  count($questions);
			$questionListFinal = array('question_list' => $questions,
					'total_question' => $queCount);
			$questionList = Zend_Json::encode($questionListFinal);
			echo $questionList;
			exit();
		}catch(Exception $e){
			return  $e->getMessage();
			exit();
		}
	}
	
	
	
	public function createimageAction(){
		$request = $this->getRequest();
		$equation = $request->getParam("equation");
		$equation = base64_decode($equation);
		$font = APPLICATION_PATH.'/../public/captcha/monofont.ttf';
		$fileGetPath = APPLICATION_PATH.'/../public/images/equation_image/';
		$newFileName = 'equation_'.time().'.jpg';
		$text = new textPNG();
		$text->msg = $equation;
		$text->font = $font;
		$text->file_path = $fileGetPath;
		$text->file_name = $newFileName;
		$image = $text->draw();
		echo true;
		exit();
		
	}
	
	/**
	 * @desc controller function to show question list which are not appapproved
	 * @param int standard,framework,grades,domain,search,search_question
	 * @author Suman khatri
	 * @return ArrayIterator
	 */	
	public function draftquestionlistAction()
	{
		$request 					= $this->getRequest();
		$tblStandards				= new Application_Model_DbTable_CategoriesStandards();
		$tblquestion				= new Application_Model_DbTable_ChildQuestion();
		$tblcategory				= new Application_Model_DbTable_QuestionCategories();
		$tblframeworks				= new Application_Model_DbTable_Framework();
		$tblgrades					= new Application_Model_DbTable_ChildGrade();
		$tbldomain					= new Application_Model_DbTable_QuestionDomain();
		$tblAdmin					= new Application_Model_DbTable_UserLogin();
		$adminUsers					= $tblAdmin->GetAllAdminUsers();
		$this->view->adminUser		= $adminUsers;
		$standard_id				= $request->getParam('standard');
		$framework_id				= $request->getParam('framework');
		$searchBy					= $request->getParam('search');
		if($framework_id != '')
		{
			$whereframe 		= "subject_id = $framework_id";
			$framework 			= $tblframeworks->fetchRow($whereframe);
			$this->view->frame		= $framework;
		}
		$grade_id 					= $request->getParam('grades');
		if($grade_id != '')
		{
			$wheregrade 		= "grades_id = $grade_id";
			$grade 			= $tblgrades->fetchRow($wheregrade);
			$this->view->grade		= $grade;
		}
		$domain_id 					= $request->getParam('domain');
		if($domain_id != '')
		{
			$wheredoamin 		= "domain_id = $domain_id";
			$domain 				= $tbldomain->fetchRow($wheredoamin);
			$this->view->domain		= $domain;
		}
		$search_question= $request->getParam('search_question');
		$data 			= $tblcategory->getCategory($standard_id,$framework_id,$grade_id,$domain_id);
		foreach ($data as $d)
		{
			if($categoryId == '')
			{
				$categoryId = $d['category_id'];
			}
			else
			{
				$categoryId = $categoryId.' , '.$d['category_id'];
			}
		}
		$this->view->search_question 	= $search_question;
		$this->view->domain_id		 	= $domain_id;
		$this->view->grade_id			= $grade_id;
		$this->view->framework_id		= $framework_id;
		$this->view->standard_id		= $standard_id;
		$this->view->searchBy			= $searchBy;

if($categoryId == '')
		{
			$categoryId = '';
			$questions					= $tblquestion->getDraftQuestionsbyCatGrade($categoryId,$grade_id);
		}
		if($categoryId)
		{
			$questions					= $tblquestion->getDraftQuestionsbyCatGrade($categoryId,$grade_id);
		}
		if($search_question != '')
		{
			$questions					= $tblquestion->getDraftQuestionsbysearch($search_question);
		}
		if(!empty($searchBy)) {
			$questions					= $tblquestion->GetDraftQuestionSearchCreatedandModifiedBy($searchBy);
		}
		
		$standards					= $tblStandards->fetchAll();
		$this->view->standard		= $standards;
		$page						= $this->_getParam('page',1);
		$paginator 					= Zend_Paginator::factory($questions);
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
		if($perPage!=null){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
		}
		$this->view->perpage = $recordsPerPage;
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		$this->view->questions     = $paginator;
		$this->view->totalRecord  = count($questions);
		$this->view->currentPage  = $page;
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}

	/**
	 * @desc controller function to approve question
	 * @param int qId
	 * @author Suman khatri
	 * @return message
	 */	
	public function approvequestionAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$qId = $request->getParam("qId");
                $typeAction = $request->getParam("type");
		$adminInfoSession = new Zend_Session_Namespace('adminInfo');
		$adminLogindata = $adminInfoSession->adminData;
		$adminLoginId			= $adminLogindata->admin_user_id;
		$adminName = $adminLogindata->name;
		$tblQuestion 	= new Application_Model_DbTable_ChildQuestion();
		try{
			$dataqUpdate  	= array('approved_by'	=> $adminName,
                                                'approved_byid'	=> $adminLoginId,
                                                'approved_date'	=> date('Y-m-d H:i:s'),
                                                'date'			=> date('Y-m-d H:i:s'));
                        if($typeAction == 'approve'){
                            $dataqUpdate['is_approved'] = 'Y';
                        }else if($typeAction == 'draft'){
                            $dataqUpdate['is_approved'] = 'N';
                        }
			$whereQuestion  = "bal_question_id = $qId";
			$updateResult   = $tblQuestion->updateQuestion($dataqUpdate, $whereQuestion);
			$updateResultFinal = array('ApprovedId' => $updateResult,
					'message' =>'success');
                        
                        $questionInfo = $tblQuestion->getQuestion($qId);
                        $questionCount = $tblQuestion->getApprovedQuestionCountInSameDomain($qId);
                        
                        if($questionCount == 1) {

                            $dbQuestionCategory = new Application_Model_DbTable_QuestionCategories();
                            $categoryInfoArray = $dbQuestionCategory->categoryInfo($questionInfo->category_id, true);
                            
                            if($questionInfo->grade_id == 0) {
                                foreach ($categoryInfoArray as $categoryInfo) {
                                    $this->_addNotificationForkidIfNewSubjectAdded($categoryInfo['grade_id'], $categoryInfo['subject_name'], $categoryInfo['subject_id'], $categoryInfo['domain_id'], $categoryInfo['name']);
                                }
                            } else {
                                $categoryInfo = $categoryInfoArray[0];
                                $this->_addNotificationForkidIfNewSubjectAdded($questionInfo->grade_id, $categoryInfo['subject_name'], $categoryInfo['subject_id'], $categoryInfo['domain_id'], $categoryInfo['name']);
                            }
                        }
                        
			$subjectList = Zend_Json::encode($updateResultFinal);
			echo $subjectList;
			exit();
		}catch(Exception $e){
			return  $e->getMessage();
			exit();
		}
	}
	
	
	/*********************block for import question by excel sheet*********************************/
	public function importexcelAction() {
            $countErorr = 0;
		$excel = new PhpExcelReader ();
		$data = array ();
		$excel->read ( APPLICATION_PATH.'/../public/'.QUESTION_IMPORT_FILENAME_PATH.QUESTION_IMPORT_FILENAME );
                $questionAddedCount = 0;
		foreach ( $excel->sheets as $sheets ) {
		  	$cell = 1;
			$numRows = $sheets ['numRows'];
			$numCols = $sheets ['numCols'];
			
                for($i = 2; $i <= $numRows; $i++) {
                    $questionAddedCount++;
                    $categoryName = $sheets ['cells'] [$i] [1];
                    $question = $sheets ['cells'] [$i] [2];
                    $difficultyLevel = $sheets ['cells'] [$i] [3];
                    $answerExplanation = $sheets ['cells'] [$i] [4];
                    //$gradeExcel = '1-12';
                    $gradeExcel = $sheets ['cells'] [$i] [5];
                    $rightAnswer = $sheets ['cells'] [$i] [6];
                    $wrongAnswer1 = $sheets ['cells'] [$i] [7];
                    $wrongAnswer2 = $sheets ['cells'] [$i] [8];
                    $wrongAnswer3 = $sheets ['cells'] [$i] [9];

                    // @todo IMP: for maths question to generate equation image check condition if('math.content'.. below
                    $subjectName = 'maths';//$sheets ['cells'] [$i] [2];
                    
                    $refer_book_name = '';//$sheets ['cells'] [$i] [12];
                    $refer_book_chapter = '';//$sheets ['cells'] [$i] [13];
                    
                    $catIdData = $this->_categoryTbObj->getcategoryId ( $categoryName );
                    
    				if (! empty ( $catIdData )) {
    					$tblStandards = new Application_Model_DbTable_CategoriesStandards ();
    					$tblcategory = new Application_Model_DbTable_QuestionCategories ();
    					$tblquestion = new Application_Model_DbTable_ChildQuestion ();
    					$tbloptions = new Application_Model_DbTable_QuestionOptions ();
    					
    					$adminInfoSession = new Zend_Session_Namespace ( 'adminInfo' );
    					$adminLogindata = $adminInfoSession->adminData;
    					$adminLoginId = $adminLogindata->admin_user_id;
    					$adminName = $adminLogindata->name;
    						/**
    					 * *************image path for question********************
    					 */
    					
    					/**
    					 * ************************variables for assigned null***********************
    					 */
    					$questionEquation = '';
    					$questionEquationImage = '';
    					$questionEquationImgName = '';
    					$explanationEquation = '';
    					$explanationEquationImg = '';
    					$explanationEquationImgName = '';
    					$optionEquation = '';
    					$optionEquationImg = '';
    					$optionEquationImgName = '';
    					$wrognEquation = '';
    					$wrognEquationImg = '';
    					$wrognEquationImgName = '';
    					$wrogn1Equation = '';
    					$wrogn1EquationImg = '';
    					$wrogn1EquationImgName = '';
    					$wrogn2Equation = '';
    					$wrogn2EquationImg = '';
    					$wrogn2EquationImgName = '';
    					// $userQuestionIdData = $tblquestion->fetchRow("bal_question_id = '$edit_id'");
    					$createdById = $userQuestionIdData ['created_byid'];
    						$arrEqImageNames = array ();
    						$category_id = $catIdData ['category_id'];
    						if('math.content'== strtolower($subjectName) || 'maths' == strtolower($subjectName)){
    						/**************block for quation text and theire images****************/
    						$strReplacedStringQuestion = $this->replaceEQTagsWithImage ( 'PSVEQ[', $question );
    						$arrImageUrlsQuestion = $this->getEQTags ( 'PSVEQ[', $question );
    						$newFileName = 'question';
    						$arrEqImageNamesQuestion = $this->createdImages($arrImageUrlsQuestion,$newFileName);
    						$strDescQuestion = $strReplacedStringQuestion;
    						$csvEqImagesQuestion = implode ( ',', $arrEqImageNamesQuestion );
    						
    						/**************End block for quation text and theire images****************/
                                                    
                                                    /**************block for Answer Explanation text and theire images****************/
    				$strReplacedStringAnswerExplations = $this->replaceEQTagsWithImage ( 'PSVEQ[', $answerExplanation );
    				$arrImageUrlsAnswerExplations = $this->getEQTags ( 'PSVEQ[', $answerExplanation );
    				$newFileName = 'answerexplations';
    				$arrEqImageNamesAnswerExplations = $this->createdImages($arrImageUrlsAnswerExplations,$newFileName);
    				$strDescAnswerExplations = $strReplacedStringAnswerExplations;
    				$csvEqImagesAnswerExplations = implode ( ',', $arrEqImageNamesAnswerExplations );
    				
    				/**************block for rigth answer text and theire images****************/
    											
    										
    										$strReplacedStringRight = $this->replaceEQTagsWithImage ( 'PSVEQ[', $rightAnswer );
    										$arrImageUrlsQuestionRight = $this->getEQTags ( 'PSVEQ[', $rightAnswer );
    										$newFileName = 'right';
    										$arrEqImageNamesRight = $this->createdImages($arrImageUrlsQuestionRight,$newFileName);
    										$strDescRight = $strReplacedStringRight == 'equation_tags'?'equation_tags':$strReplacedStringRight;
    										$csvEqImagesRight = implode ( ',', $arrEqImageNamesRight );
    										
    										/**************End block for rigth answer text and theire images****************/
    										$strReplacedStringWrong1 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer1 );
    										$arrImageUrlsWrong1 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer1 );
    										$newFileName = 'wrong1';
    										$arrEqImageNamesWrong1 = $this->createdImages($arrImageUrlsWrong1,$newFileName);
    										$strDescWrong1 = $strReplacedStringWrong1 == 'equation_tags'?'equation_tags':$strReplacedStringWrong1;
    										$csvEqImagesWrong1 = implode ( ',', $arrEqImageNamesWrong1 );
    										
    										/**************block for quation text and theire images****************/
    										
    										$strReplacedStringWrong2 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer2 );
    										$arrImageUrlsWrong2 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer2 );
    										$newFileName = 'wrong2';
    										$arrEqImageNamesWrong2 = $this->createdImages($arrImageUrlsWrong2,$newFileName);
    										$strDescWrong2 = $strReplacedStringWrong2  == 'equation_tags'?'equation_tags':$strReplacedStringWrong2;
    										$csvEqImagesWrong2 = implode ( ',', $arrEqImageNamesWrong2 );
    										
    										/**************End block for rigth answer text and theire images****************/
    										
    										$strReplacedStringWrong3 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer3 );
    										$arrImageUrlsWrong3 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer3 );
    										$newFileName = 'wrong3';
    										$arrEqImageNamesWrong3 = $this->createdImages($arrImageUrlsWrong3,$newFileName);
    										$strDescWrong3 = $strReplacedStringWrong3  == 'equation_tags'?'equation_tags':$strReplacedStringWrong3;
    										$csvEqImagesWrong3 = implode ( ',', $arrEqImageNamesWrong3 );
    										/**************End block for rigth answer text and theire images****************/
                                                    
                                                    }
                                                    
    						$difficulty_level = $difficultyLevel?$difficultyLevel:'M';
    						$grade = preg_replace("/[^1-9\-]/s","",$gradeExcel);
                                                    $gradeArr = explode("-", $grade);
                                                    if(count($gradeArr) >1){
                                                        $grade  = 0;
                                                     }
    						$url_of_question = uniqid();
    						
    							
    							/**
    							 * ************************images created*************************
    							 */
    						
    										$dataqInsert = array (
    												'question' => $question,
    												'question_display' => $strDescQuestion,
    												'question_equation_images' => $csvEqImagesQuestion,
    												'question_equation' => $questionEquation,
    												'question_equation_image' => $questionEquationImage,
    												'question_equation_image_name' => $questionEquationImgName,
    												'difficulty_level' => $difficulty_level,
    												'grade_id' => $grade,
    												'category_id' => $category_id,
    												'url_of_question' => $url_of_question,
    												'question_image_url' => $question_image_url,
    												'answer_image_url' => $answer_image_url,
    												'answer_explanation'		=> $answerExplanation,
    												'answer_explanation_equation' =>$strDescAnswerExplations,											
    												'answer_explanation_image_name' =>$csvEqImagesAnswerExplations,
    												'set_question' => $set_question,
    												'refer_book_name' => $refer_book_name,
    												'refer_book_chapter' => $refer_book_chapter,
    												'refer_article_url' => $refer_article_url,
    												'wolframalphaquery' => $wolframalphaquery,
    												'expiry_date' => $expiryDate,
    										        'is_approved' => 'Y',
    												//'created_by' => $adminName,
    										        'created_by' => 'Upload - Abhinav',
    										        'modified_by' => 'Upload - Abhinav',
    												'created_byid' => $adminLoginId,
    										        'modified_byid' => $adminLoginId,
    												'created_date' => date ( 'Y-m-d H:i:s' ),
    										        'approved_by' => 'Upload - Abhinav',
    										        'approved_byid' => $adminLoginId,
    										        'approved_date' => date ( 'Y-m-d H:i:s' ),
    												'date' => date ( 'Y-m-d H:i:s' ),
    										        
    										        
    										        
    										);
                                        //echo '<pre>';var_dump($dataqInsert);exit;
    									$questionId = $tblquestion->addQuestion ( $dataqInsert );
    									
    										
    										
    										
    										
    										
    										
    									$dataOrinsert = array (
    											'option' => $rightAnswer,
    											'question_id' => $questionId,
    											'option_equation' => $strDescRight,
    											'option_equation_image' => $optionEquationImg,
    											'option_equation_image_name' => $csvEqImagesRight,
    											'answer' => 'Y' 
    									);
    									
    									$ranswerId = $tbloptions->addOptionforQuestion ( $dataOrinsert );
    									$dataOw1insert = array (
    											'option' => $wrongAnswer1,
    											'question_id' => $questionId,
    											'option_equation' => $strDescWrong1,
    											'option_equation_image' => $wrognEquationImg,
    											'option_equation_image_name' =>$csvEqImagesWrong1,
    											'answer' => 'N' 
    									);
                                                                            try{
    									$w1answerId = $tbloptions->addOptionforQuestion ( $dataOw1insert );
                                                                            } catch (Exception $e) {
                                                                                print_r($dataOw1insert);
                                                                                exit;
                                                                            }
    									$dataOw2insert = array (
    											'option' => $wrongAnswer2,
    											'question_id' => $questionId,
    											'option_equation' => $strDescWrong2,
    											'option_equation_image' => $wrogn1EquationImg,
    											'option_equation_image_name' => $csvEqImagesWrong2,
    											'answer' => 'N' 
    									);
    									
    									$w2answerId = $tbloptions->addOptionforQuestion ( $dataOw2insert );
    									$dataOw3insert = array (
    											'option' => $wrongAnswer3,
    											'question_id' => $questionId,
    											'option_equation' => $strDescWrong3,
    											'option_equation_image' => $wrogn2EquationImg,
    											'option_equation_image_name' => $csvEqImagesWrong3,
    											'answer' => 'N' 
    									);
    									
    									$w3answerId = $tbloptions->addOptionforQuestion ( $dataOw3insert );
    									//$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( 'Question added successfully' );
    									unset ( $adminInfoSession->questionData );
    								
    							
    						
    					
    				} else {
    					$errorArray[$i] = 'Standard does not exits into table';
    				}
			}
                        break;
		}
		if(!empty($errorArray)){
                    echo "<pre>";
			print_r($errorArray);
                        exit();
		}else{
			//echo "$questionAddedCount questions are inserted into table successfully";
                        $this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( "$questionAddedCount questions are inserted into table successfully" );
			$this->_redirect ( '/admin/questions/draftquestionlist' );
		}
		exit();
	}
	/**
	 * controller function to load images
	 * 
	 * @param
	 *        	str question string
	 * @author Ram Kaushik
	 * @return arrEqTags
	 */
	private function loadimage($strQuestions) {
		$tagName = 'PSVEQ[';
		// $tagName = 'PVEQ[';
		
		$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
		$arrTagStr = explode ( $tagName, $strQuestions );
		
		$arrEqStr = array ();
		$arrImageUrls = array ();
		
		for($i = 1; $i <= count ( $arrTagStr ); $i ++) {
			if ($arrTagStr [$i] && strpos ( $arrTagStr [$i], ']' )) {
				$arrSubTagStr = explode ( ']', $arrTagStr [$i] );
                array_pop($arrSubTagStr);
                array_push ( $arrEqStr, implode(']', $arrSubTagStr));
			}
		}
		
		for($j = 0; $j < count ( $arrEqStr ); $j ++) {
			array_push ( $arrImageUrls, $urlImageProcess . str_replace(' ', '&space;', $arrEqStr[$j]) );
		}
		
		$strDesWithDummyTag = $this->replaceEQTagsWithImage ( $tagName, $strQuestions );
		$arrDesWithDummyTag = explode ( 'equation_tags', $strDesWithDummyTag );
		
		$strTextWithImages = $arrDesWithDummyTag [0];
		
		for($k = 1; $k < count ( $arrDesWithDummyTag ); $k ++) {
			
			if (@fopen ( $arrImageUrls [$k - 1], "r" )) {
				$strTextWithImages .= "<img src='" . $arrImageUrls [$k - 1] . "' />" . $arrDesWithDummyTag [$k];
			} else {
				echo 'Unable to load image, please try again';
				exit ();
			}
		}
		return $strTextWithImages;
	}
	
    /**
	 * @desc controller function to load images
	 * @param str question string
	 * @author Ram Kaushik
	 * @return arrEqTags
	 */
	
	public function loadimageAction(){
		 
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$strQuestions = $_POST['description'];
		/* $tagName = 'PSVEQ[';
		 
		$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
		$arrTagStr = explode($tagName, $strQuestions);
		$arrEqStr = array();
		$arrImageUrls = array();
		 
		for($i=1; $i<=count($arrTagStr); $i++){
			if($arrTagStr[$i] && strpos($arrTagStr[$i], ']')){
				$arrEqDesc = str_split($arrTagStr[$i]);
				$intPosition = 0;
				for($p=0; $p<count($arrEqDesc); $p++){
					if($arrEqDesc[$p] == ']'){
						$intPosition = $p;
					}
				}
				
				for($m=0; $m<$intPosition; $m++){
					$strFinal .= $arrEqDesc[$m];
				}
				
				array_push($arrEqStr, $strFinal);
			}
		}
		
		for($j=0; $j<count($arrEqStr); $j++){
			array_push($arrImageUrls, $urlImageProcess.  str_replace(' ', '&space;', $arrEqStr[$j]));
		}
		
		$strDesWithDummyTag = $this->replaceEQTagsWithImage($tagName, $strQuestions);
		$arrDesWithDummyTag = explode('equation_tags', $strDesWithDummyTag);
	
		$strTextWithImages = $arrDesWithDummyTag[0];
	
		for($k=1; $k<count($arrDesWithDummyTag); $k++){
	
			if(@fopen($arrImageUrls[$k-1], "r")){
				$strTextWithImages .= "<img src='".$arrImageUrls[$k-1]."' />".$arrDesWithDummyTag[$k];
			}else{
				echo 'Unable to load image, please try again';exit;
			}
		}
	 */
		echo $this->_questionWithImage($strQuestions);exit;
	}
	
	/*
        public function loadimageAction(){
             
             $this->_helper->layout()->disableLayout();
	     	 $this->_helper->viewRenderer->setNoRender();
             
             $strQuestions = $_POST['description'];
             $tagName = 'PSVEQ[';
             
             $urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
             $arrTagStr = explode($tagName, $strQuestions);
             $arrEqStr = array();
             $arrImageUrls = array();
             
             for($i=1; $i<=count($arrTagStr); $i++){
                 if($arrTagStr[$i] && strpos($arrTagStr[$i], ']')){
                     $arrSubTagStr = explode(']', $arrTagStr[$i]);
                     array_push($arrEqStr, $arrSubTagStr[0]);
                 }
             }
             
            for($j=0; $j<count($arrEqStr); $j++){
                 array_push($arrImageUrls, $urlImageProcess.urlencode($arrEqStr[$j]));
             }
             
            $strDesWithDummyTag = $this->replaceEQTagsWithImage($tagName, $strQuestions);
            $arrDesWithDummyTag = explode('equation_tags', $strDesWithDummyTag);
            
            $strTextWithImages = $arrDesWithDummyTag[0];
            
            for($k=1; $k<count($arrDesWithDummyTag); $k++){
                
                if(@fopen($arrImageUrls[$k-1], "r")){
                    $strTextWithImages .= "<img src='".$arrImageUrls[$k-1]."' />".$arrDesWithDummyTag[$k];
                }else{
                    echo 'Unable to load image, please try again';exit;
                }
             }
            
            echo nl2br($strTextWithImages);exit;
        }
			*/
        /**
         * controller function to get equation tags
         *
         * @param
         *        	str question string
         * @author Ram Kaushik
         * @return arrEqTags
         */
        
    private function createdImages($arrImageUrls, $FileName)
    {
        $arrEqImageNames = array();
        $s3 = new My_Service_Amazon_S3();
        for ($k = 0; $k < count($arrImageUrls); $k ++) {
            if (empty($arrImageUrls[$k])) {
                continue;
            }
            
            $newFileName = $FileName . $k . '_' . time() . '_' . rand(11111, 99999) .  '.gif';
            $s3->saveFile($arrImageUrls[$k], 'equation/' . $newFileName);

            array_push($arrEqImageNames, $newFileName);
        }
        return $arrEqImageNames;
    }

    /***************function for removed old images from the equestion***************/
        private function removedEquestionsImages($ImagesArrayName){
        	$s3 = new My_Service_Amazon_S3();
        	$oldImageNameQuestion = explode(',', $ImagesArrayName);
        	foreach ($oldImageNameQuestion as $oldImageNameQuestionA){
                    $s3->delete('equation/'.$oldImageNameQuestionA);
        	}	
        }      
        
        
        private function getEQTags($tagName, $strQuestions) {
        	$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
        	$arrTagStr = explode ( $tagName, $strQuestions );
        
        	$arrEqStr = array ();
        	$arrImageUrls = array ();
        
        	for($i = 1; $i <= count ( $arrTagStr ); $i ++) {
        		if (!empty($arrTagStr[$i]) && strpos ( $arrTagStr [$i], ']' )) {
                    $arrSubTagStr = explode ( ']', $arrTagStr [$i] );
                    array_pop($arrSubTagStr);
        			array_push ( $arrEqStr, implode(']', $arrSubTagStr));
        		}
        	}
        
        	for($j = 0; $j < count ( $arrEqStr ); $j ++) {
        		array_push ( $arrImageUrls, $urlImageProcess . str_replace(' ', '&space;', $arrEqStr[$j]) );
        	}
        
        	return $arrImageUrls;
        }
        
        /**
         * controller function to replace eq tag with img tag
         *
         * @param
         *        	tagName, str question string
         * @author Ram Kaushik
         * @return arrEqTags
         */
        private function replaceEQTagsWithImage($tagName, $strQuestions) {
        	$strWithImgLoc = "";
        	
        	$arrTagStr = explode($tagName, $strQuestions);
        	
        	for($i=0; $i<count($arrTagStr); $i++) {
        		if(strpos($arrTagStr[$i], ']')){
        			$arrEqDesc = str_split($arrTagStr[$i]);
        			$intPointer = 0;
        			for($k=0; $k<count($arrEqDesc);$k++){
        				if($arrEqDesc[$k] == ']'){
        					$intPointer = $k;
        				}	
        			}
        			$strPost = "";
        			for($m=$intPointer+1; $m<count($arrEqDesc); $m++){
        				$strPost .= $arrEqDesc[$m];
        			}
        			
        			$strWithImgLoc .= 'equation_tags'.$strPost;
        		}else{
        			$strWithImgLoc .= $arrTagStr[$i];
        		}
        	}
        	return $strWithImgLoc;
        }
        
        /*
        private function replaceEQTagsWithImage($tagName, $strQuestions) {
        	$strWithImgLoc = "";
        
        	$arrTagStr = explode ( $tagName, $strQuestions );
        
        	for($i = 0; $i < count ( $arrTagStr ); $i ++) {
        		if (strpos ( $arrTagStr [$i], ']' )) {
        			$arrDataChunk = explode ( ']', $arrTagStr [$i] );
        			$strWithImgLoc .= 'equation_tags' . $arrDataChunk [1];
        		} else {
        			$strWithImgLoc .= $arrTagStr [$i];
        		}
        	}
        	return $strWithImgLoc;
        }
        */
        
       /**
	 * @desc function to get grade info of a standard
	 * @param int standardid
	 * @return ArrayObject
         * @author Suman Khatri on 21 April 2014
	 */
	public function getgradeusingstandardAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$request 	= $this->getRequest();
		$standard_id 	= $request->getParam('standard_id');
		$datagrade		= $tblcategory->getgradesInfoUsingStandard($standard_id);
		if($datagrade != '')
		{
			$gradeArray = array('message' => null,
									'status' =>'success', 								  
									'datagrade' => $datagrade, 								  
									'countgrade' => count($datagrade)); 			
			$response = Zend_Json::encode($gradeArray);
			echo $response;
			exit();
		}
	}
        
        /**
	 * @desc function to get subject info of a standard and grade
	 * @param int standardId,gradeId
	 * @return ArrayIterator
         * @author Suman Khatri on 21 April 2014
	 */
	public function getsubjectinfousingstandardandgradeAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$tblcategory	= new Application_Model_DbTable_QuestionCategories();
		$request 	= $this->getRequest();
		$standardId 	= $request->getParam('standardId');
                $gradeId 	= $request->getParam('gradeId');
		$datasubject		= $tblcategory->getsubjectInfoUsingStandardAndGrade($standardId,$gradeId);
		if($datasubject != ''){
			$subjectArray = array('message' => null,
									'status' =>'success', 								  
									'data' => $datasubject, 								  
									'count' => count($datasubject)); 			
			$response = Zend_Json::encode($subjectArray);
			echo $response;
			exit();
		} else {
                        $subjectArray = array('message' => null,
									'status' =>'blank', 								  
									'data' => null, 								  
									'count' => 0); 			
			$response = Zend_Json::encode($subjectArray);
			echo $response;
			exit();
                }
	}
        
        private function sendPushToAllDevices($subjectId, $subjectName) {

        // send push to all devices
        $objectParent = new Application_Model_Parents();
        $objectParent->sendPushToAllDevices(NULL, array(
            'process_code' => 'new subject',
            'data' => array(
                'subject_id' => $subjectId,
                'subject_name' => $subjectName
            )
        ));
    }

}