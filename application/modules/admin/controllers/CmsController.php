<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Cms
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses       CmsController
 * @category   Cms
 * @package    Zend_Application
 * @subpackage Cms
 */  
class Admin_CmsController extends Zend_Controller_Action
{
	//private variables
	private $_cmsInfo;
	/*
	 * function call during initialization
	 */
	public function init()
	{	 
		parent::init();
		$layout = Zend_Layout::getMvcInstance();//Create object
		 $layout->setLayout('admin',true);//set layout admin
		 require_once APPLICATION_PATH.'/../library/functions.php';
		 $this->_cmsInfo = new Application_Model_Cms (); // creates object of class parent
	}
	/*
	 * function for when admin page request then redirect on login page for authentication
	 */
	public function indexAction()
	{
		$this->_redirect('admin/login/login');
	}
	public function aboutusAction()
	{
		try{
		$request = $this->getRequest();
		
		$pageTitle = 'About Us';
		$tblCmsInfo = new Application_Model_DbTable_CmsInfo();
		$flashMessages 	  	 				 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
				$this->view->success = $flashMessages[0];
		}
		if($request->isPost())
		{
			$pageTitle = $request->getPost('page_title');
			if(validateNotNull($pageTitle)==false){
				$this->view->error = 'Please enter page title';
				return false;
			}
			$description = $request->getPost('about_desc'); 
			if(validateNotNull($description)==false){
				$this->view->error = 'Please enter description';
				return false;
			}
			
			$aboutId = $request->getPost('about_id'); 
			$updateData =  array('title' =>$pageTitle,'description' =>$description); 
			$updateAboutData = $tblCmsInfo->updatePageData($aboutId,$updateData);
			$this->_helper->getHelper('FlashMessenger')
										->addMessage('Data updated successfully');
			$this->_redirect('admin/cms/aboutus');
		}
			
			$getAboutUsData = $tblCmsInfo->getPageData($pageTitle);
			$this->view->aboutData = $getAboutUsData;		
		}catch(Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	
	public function privacypolicyAction()
	{
	try{
		$request = $this->getRequest();
		$tblCmsInfo = new Application_Model_DbTable_CmsInfo();
		$pageTitle = 'Privacy Policy';
		$getPrivacyData = $tblCmsInfo->getPageData($pageTitle);
		$this->view->privacyData = $getPrivacyData; 
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
				$this->view->success = $flashMessages[0];
		}
		if($request->isPost())
		{
			$pageTitle = $request->getPost('page_title');
			if(validateNotNull($pageTitle)==false){
				$this->view->error = 'Please enter page title';
				return false;
			}
			$description = $request->getPost('privacy_desc');
			if(validateNotNull($description)==false){
				$this->view->error = 'Please enter description';
				return false;
			}
			
			$privacyId = $request->getPost('privacy_id');
			$updateData =  array('title' =>$pageTitle,'description' =>$description);
			$updateAboutData = $tblCmsInfo->updatePageData($privacyId,$updateData);
			$this->_helper->getHelper('FlashMessenger')
										->addMessage('Data updated successfully');
			$this->_redirect('admin/cms/privacypolicy');
			
		}
		
		}catch(Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	
	public function supportAction()
	{
	try{
		$request = $this->getRequest();
		$tblCmsInfo = new Application_Model_DbTable_CmsInfo();
		$pageTitle = 'Support';
		$getSupportData = $tblCmsInfo->getPageData($pageTitle);
		$this->view->supportData = $getSupportData; 
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
				$this->view->success = $flashMessages[0];
		}
		if($request->isPost())
		{
			$pageTitle = $request->getPost('page_title');
			if(validateNotNull($pageTitle)==false){
				$this->view->error = 'Please enter page title';
				return false;
			}
			$description = $request->getPost('support_desc');
			if(validateNotNull($description)==false){
				$this->view->error = 'Please enter description';
				return false;
			}
			
			$supportId = $request->getPost('support_id');
			$updateData =  array('title' =>$pageTitle,'description' =>$description);
			$updateAboutData = $tblCmsInfo->updatePageData($supportId,$updateData);
			$this->_helper->getHelper('FlashMessenger')
										->addMessage('Data updated successfully');
			$this->_redirect('admin/cms/support');
			
		}
		
		}catch(Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	public function eulaAction()
	{
	try{
		$request = $this->getRequest();
		$tblCmsInfo = new Application_Model_DbTable_CmsInfo();
		$pageTitle = 'EULA';
		$geteulaData = $tblCmsInfo->getPageData($pageTitle);
		$this->view->eulaData = $geteulaData; 
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
				$this->view->success = $flashMessages[0];
		}
		if($request->isPost())
		{
			$pageTitle = $request->getPost('page_title');
			if(validateNotNull($pageTitle)==false){
				$this->view->error = 'Please enter page title';
				return false;
			}
			$description = $request->getPost('eula_desc');
			if(validateNotNull($description)==false){
				$this->view->error = 'Please enter description';
				return false;
			}
			
			$eulaId = $request->getPost('eula_id');
			$updateData =  array('title' =>$pageTitle,'description' =>$description);
			$updateeulaData = $tblCmsInfo->updatePageData($eulaId,$updateData);
			$this->_helper->getHelper('FlashMessenger')
										->addMessage('Data updated successfully');
			$this->_redirect('admin/cms/eula');
			
		}
		
		}catch(Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	
	
	/**
	 * function to handle faqlist
	 * @param nill
	 * @return data
	 * created by suman on 30th January 2014
	 */	
	public function faqlistAction(){
		$request = $this->getRequest(); //creating object to get request
		$perPage = $request->getParam('perpage'); //getting param perpage
		if(!empty($perPage)){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
	
		}
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$type = $flashMessages[1];
			if($type == 'success'){
				$this->view->success = $flashMessages[0];
			}else{
				$this->view->error = $flashMessages[0];
			}
		}
		$faqData = $this->_cmsInfo->getAllFaqData(); //getting all faq data
		$totalRecords	= count($faqData);
		$page			=$this->_getParam('page',1);
		$paginator 		= Zend_Paginator::factory($faqData);
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		// assigning the data to view file
		$this->view->faqData     = $paginator;
		$this->view->totalRecord  = $totalRecords;
		$this->view->currentPage  = $page;
		$this->view->perpage = $recordsPerPage;
	}
	
	/**
	 * function to add or update faq data
	 * @param nill
	 * @return Id
	 * created by suman on 31st January 2014
	 */	
	public function faqAction()
	{
		$request = $this->getRequest(); //creating object to get request
		$faqId = $request->getParam('id'); //getting param id
		$type = $request->getParam('type'); //getting param id
		//echo $faqId;die;
		$faqId = base64_decode($faqId); // decodes id
		$adminInfoSession = new Zend_Session_Namespace('adminInfo'); //creates instance of session
		//echo $faqId;die;
		//getting data of faq if id is not null and not empty
		if(!empty($faqId) && $faqId != null){
                        $this->view->headTitle(ADMIN_EDITFAQ);
			$faqData = $this->_cmsInfo->getFaqData($faqId); // getting faq data
			$this->view->faqData = $faqData; // assigning faq data to view file
                        
		}
		if($type == 'Add' || $type == ''){
                        $this->view->headTitle(ADMIN_ADDFAQ);
			$faqData = $this->_cmsInfo->getAllFaqData();
			if(count($faqData) >= 100){
				$this->_helper->getHelper('FlashMessenger')->addMessage("Maximum 100 FAQ's can be added");
				$this->_helper->getHelper('FlashMessenger')
							->addMessage("error");
			$this->_redirect('admin/cms/faqlist');
			}
		}
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
				$this->view->error = $flashMessages[0];
		}
		try{
			//if requestis post
			if($request->isPost())
			{
				//print_r($request->getParams());die;
				$adminInfoSession->faqData	= $request->getPost();
				$faqIdEdit = $request->getPost('faqId');
				if (empty($faqIdEdit)){
					$faqIdEdit = null;
				}
				$sortOrder = $request->getPost('sortOrder'); //geting param sortOrder
				//validates null value for sortOrder
				if(validateNotNull($sortOrder)==false){
					$errorMessage = 'Please enter sort order';
				}
				//validates numeric value of sortOrder
				if(validateNumber($sortOrder) == false){
					$errorMessage = 'Please enter numeric value for sort order';
				}
				//validates duplicacy of sortOrder
				if($this->_cmsInfo->checkSortOrderExistance($sortOrder,$faqIdEdit) == true){
					$errorMessage = 'Sort order already exist';
				}
				
				$quesTion = $request->getPost('quesTion');//geting param quesTion
				//validates null value for quesTion
				if(validateNotNull($quesTion)==false){
					$errorMessage = 'Please enter question';
				}
				//validates duplicacy of quesTion
				if($this->_cmsInfo->checkExistanceofQuestion($quesTion,$faqIdEdit)==true){
					$errorMessage = 'Question is already exist';
				}
				$ansWer = $request->getPost('ansWer');//geting param ansWer
				//validates null value for ansWer
				if(validateNotNull($ansWer)==false){
					$errorMessage = 'Please enter answer';
				}
				if(!empty($errorMessage) && $errorMessage != null){
					$this->_helper->getHelper('FlashMessenger')
							->addMessage($errorMessage);
					if(!empty($faqIdEdit) && $faqIdEdit != null){
						$url = 'admin/cms/faq/id/'.base64_encode($faqIdEdit);
					}else{
						$url = 'admin/cms/faq';
					}
					$this->_redirect($url);
				}
				$data =  array('question' =>$quesTion,'answer' =>$ansWer,'sort_order' => $sortOrder);
				if(!empty($faqIdEdit) && $faqIdEdit != null){
					$updatefaqData = $this->_cmsInfo->updateFaqData($faqIdEdit,$data);
					unset($adminInfoSession->faqData);
					$messAge = 'Data updated successfully';
				}else{
					$updatefaqData = $this->_cmsInfo->addFaqData($data);
					unset($adminInfoSession->faqData);
					$messAge = 'Data added successfully';
				}
				$this->_helper->getHelper('FlashMessenger')
							->addMessage($messAge);
				$this->_helper->getHelper('FlashMessenger')
				->addMessage("success");
				$this->_redirect('admin/cms/faqlist');
			}
		}catch(Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	
/**
	 * function to add or update faq data
	 * @param nill
	 * @return Id
	 * created by suman on 31st January 2014
	 */	
	public function deletefaqAction()
	{
		$request = $this->getRequest(); //creating object to get request
		$faqData = $this->_cmsInfo->getAllFaqData(); //getting all faq data
		$totalRecords	= count($faqData);
		if($totalRecords <= 1){
			$messAge = "There should be at least one question in FAQ";
			$this->_helper->getHelper('FlashMessenger')
							->addMessage($messAge);
				$this->_helper->getHelper('FlashMessenger')
				->addMessage("error");
				$this->_redirect('admin/cms/faqlist');
		}else{
			$faqId = $request->getParam('id'); //getting param id
			try{
				$delete	= $this->_cmsInfo->deleteFAQData($faqId);
				if($delete){
					$this->_helper->getHelper('FlashMessenger')
					->addMessage('FAQ deleted successfully');
					$this->_helper->getHelper('FlashMessenger')
					->addMessage("success");
					$this->_redirect('admin/cms/faqlist');
				}
			}catch (Exception $ex) {
				$this->_helper->getHelper('FlashMessenger')
				->addMessage(' Error: '.$ex->getMessage());
				$this->_helper->getHelper('FlashMessenger')
					->addMessage("error");
				$this->_redirect('admin/cms/faqlist');
			}
		}
	}
	
	
	/**
	 * Survey link action to save survey link
	 * shown on user's Dashboard
	 * @return NULL
	 */
	
	public function surveyAction()
	{
	    try{
	        $request                = $this->getRequest();
	        $tblCmsInfo             = new Application_Model_DbTable_CmsInfo();
	        $pageTitle              = 'Survey';
	        $surveyData             = $tblCmsInfo->getPageData($pageTitle);
	        $this->view->surveyData = $surveyData;
	        $flashMessages 	  	    = $this->_helper->flashMessenger->getMessages();
	        
	        if(is_array($flashMessages) && !empty($flashMessages)){
	            $this->view->success = $flashMessages[0];
	        }
	        
	        if($request->isPost()){
	            $surveyLink = trim($request->getPost('survey_link'));
	            
	            if(validateNotNull($surveyLink) == false){
	                $this->view->error = 'Please enter survey link';
	                return false;
	            }

	            $pageId     = $request->getPost('page_id');
	            $updateData =  array('description' =>$surveyLink);
	            $updateeulaData = $tblCmsInfo->updatePageData(
	                    $request->getPost('page_id'),
	                    array('description' =>$surveyLink)
	                    );
	            
	            $this->_helper->getHelper('FlashMessenger')->addMessage('Data updated successfully');
	            $this->_redirect('admin/cms/survey');
	        }
	
	    } catch(Exception $e){
	        $this->view->error = $e->getMessage();
	    }
	}
}