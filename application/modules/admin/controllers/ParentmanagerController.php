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
 * @uses       	ParentManagerController
 * @category   	ParentManager
 * @package    	Zend_Application
 * @subpackage 	ParentManager
 * @author 		Suman khatri
 */
class Admin_ParentmanagerController extends Zend_Controller_Action {
    /*
     * function call during initialization
     */

    protected $_parent;

    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
        $this->_parent = new Application_Model_Parents();
    }

    /*
     * function for when admin page request then redirect on login page for authentication
     */

    public function indexAction() {
        $this->_redirect('admin/login/login');
    }

    /**
     * @desc controller function to handle parent list 
     * @param varchar search
     * @author Suman khatri
     */
    public function parentlistAction() {
        try {
            $request = $this->getRequest();
            $tblParentInfo = new Application_Model_DbTable_ParentRegistration(); //creates object for model file
            $childObj = new Application_Model_Child();
            $childQuesObj = new Application_Model_DbTable_ChildInfo();
            $tblQuestionRequest = new Application_Model_DbTable_ChildQuestionRequest();
            
            $searchData = $request->getParam('search'); //getting parameter named search
            if (empty($searchData)) {
                //fetch data without any condition
                $parentData = $tblParentInfo->GetAllParent();
            } else {
                //fetch data according to the search condition
                $parentData = $tblParentInfo->GetSearchedParent($content = str_replace(' ', '', $searchData));
                $this->view->search = $searchData;
            }

            // get childs of parent and prepare data array
            $parentChildData = array();
            $i = 0;
            foreach ($parentData as $parentId){
                $childData = $childObj->getAllChildOfParent($parentId['parent_id'], array(), 'created_date', 'DESC');
                $childData = $childData->toArray();

                $parentId['parent_created_date'] = $parentId['created_date'];

                //loop through each child
                if(count($childData) > 0){
                    
                    foreach($childData as $childDataRow){
                        $childQuestionData = $childQuesObj->getChildinfoForParent($parentId['parent_id'], NULL, $childDataRow['child_id']);

                        $lastQuestionAskedDate = $tblQuestionRequest->getLastQuestionAsked($childDataRow['child_id']);

                        $childQuestionData[0]['child_created_date'] = $childQuestionData[0]['created_date'];
                        $parentChildData[$i] = array_merge($parentId, $childQuestionData[0]) ;
                        $parentChildData[$i]['last_question_date'] = (count($lastQuestionAskedDate) > 0) ? $lastQuestionAskedDate[0]['request_date'] : '' ;
                        $i++;
                    }
                } else {
                    $parentChildData[$i] = $parentId;
                    $i++;
                }
            }
            
            $perPage = $request->getParam('perpage'); //getting parameter named perpage
            if ($perPage != null) {
                $recordsPerPage = $perPage; //set record per page according the parameter
            } else {
                $recordsPerPage = PER_PAGE; //set record per page with default value if perpage not found
            }
            $this->view->perpage = $recordsPerPage; //assigns $recordsPerPage variable to view file
            $totalRecords = count($parentChildData); //count of records found
            //pagination start
            $page = $this->_getParam('page', 1); //set the page
            $paginator = Zend_Paginator::factory($parentChildData);
            $paginator->setItemCountPerPage($recordsPerPage);
            $paginator->setCurrentPageNumber($page);
            //pagination end
            $this->view->parentData = $paginator; //assign records to the view file
            $this->view->totalRecord = $totalRecords; //assign no. of total record to the view file
            $this->view->currentPage = $page; //assign currnet page no. to the view file
            $this->view->counter = (($page - 1) * $recordsPerPage) + 1;
            
            $flashMessages = $this->_helper->flashMessenger->getMessages();
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $flashMessages = $flashMessenger->getMessages();
            if (is_array($flashMessages) && !empty($flashMessages)) {
                $this->view->success = $flashMessages[0];
                $flashMessenger->addMessage('');
            }
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    /**
     * @desc controller function to handle change status of parent 
     * @param varchar status,int parent userid
     * @author Suman khatri
     */
    public function changestatusAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        try {
            $request = $this->getRequest();
            $tblUserInfo = new Application_Model_DbTable_ParentRegistration(); //creates object for model file ParentRegistration
            $tblParentInfo = new Application_Model_DbTable_ParentInfo();
            $statusData = $request->getParam('status'); //getting parameter named status
            $userId = $request->getParam('parent'); //getting parameter named parent
            $date = date('Y-m-d H:i:s'); //create date
            $updateData = array('is_active' => $statusData, 'modified_date' => $date); //data to be updated for parent 
            $updateResult = $tblUserInfo->updateUserInfo($updateData, $userId);
            if ($updateResult == 1) {
                $userData = $tblUserInfo->fetchDetail($userId); //fetches user email info from bal_user table
                $userInfo = $tblParentInfo->fetchUser($userId); //fetched user name info from bal_parent table
                $userEmail = $userData['email'];
                $userName = $userInfo['first_name'] . " " . $userInfo['middle_name'] . " " . $userInfo['last_name'];
                if ($statusData == 'N') {
                    $actionPerformed = "deactivated";
                    $actionTobePerformed = "cannot access myfinny.com account anymore.";
                } elseif ($statusData == 'Y') {
                    $actionPerformed = "activated";
                    $actionTobePerformed = "can access myfinny.com account with your credential.<br /><br />";
                }

                
                $serverUrl = new Zend_View_Helper_ServerUrl();
                $baseUrl = new Zend_View_Helper_BaseUrl(); 
                $parentImage = $serverUrl->serverUrl().$baseUrl->baseUrl('/images/parent_small.png');
                
                if (!empty($userInfo['parent_image'])) {
                    
                    $parentImage = AWS_S3_URL . 'parent/thumb/' . $userInfo['parent_image'];
                    
                }        

                /*                 * ****************sending mail section********* */
                $mail = new My_Mail();
                $mail->setSubject('Your myfinny account status');
                $template = new Zend_View();
                $template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
                $template->assign('name', $userName);
                $template->assign('status', $actionPerformed);
                $template->assign('profile_picture', $parentImage);
                $html = $template->render('status.phtml');
                $mail->setBodyHtml($html);
                $mail->addTo($userEmail);
                $response = $mail->send();
                /*                 * ****************sending mail section********* */
                $this->_helper->flashMessenger->addMessage('Status updated successfully');
                //$this->_redirect('/admin/parentmanager/parentlist');
                //mail functionality end
                $resData = array('message' => 'success');
                $response = Zend_Json::encode($resData);
                echo $response;
                exit();
            } else {
                $resData = array('message' => 'error');
                $response = Zend_Json::encode($resData);
                echo $response;
                exit();
            }
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    /**
     * @desc controller function to handle reset password functionality of parent by admin 
     * @param int parent userid
     * @author Suman khatri
     */
    public function resetpasswordAction() {
        //$this->_helper->layout->disableLayout();
        $request = $this->getRequest();
        $userId = $request->getParam('id'); //getting parameter named id
        $this->view->userId = $userId; //assign userId to the view file
        $tblUserInfo = new Application_Model_DbTable_ParentRegistration(); //creates object for model file ParentRegistration
        try {
            if ($request->isPost()) {
                $passWord = $request->getPost('passWord'); //getting parameter named passWord
                $confirmPassword = $request->getPost('confirmPassword'); //getting parameter named confirmPassword
                $userId = $request->getPost('userId'); //getting parameter named userId
                $date = date('Y-m-d H:i:s'); //create date
                if ($passWord == '') {//validate blank password
                    $this->view->error = 'Please enter password';
                }
                if (validateMinLength($passWord, '8') == false) {//validate minlength of password
                    $this->view->error = 'Please enter password of min 8 characters';
                }
                if (validateMaxLength($passWord, '16') == false) {//validate maxlength of password
                    $this->view->error = 'Please enter password of max 16 characters';
                }
                if ($confirmPassword == '') {//validate blank confirmpassword
                    $this->view->error = 'Please enter confirm password';
                }
                if ($confirmPassword != $passWord) {//validate match of password and confirm password
                    $this->view->error = 'Confirm password does not match';
                }
                $updateDataArray = array('password' => md5($passWord), 'modified_date' => $date); //data to be updated for parent
                $chnagePassword = $tblUserInfo->updateUserInfo($updateDataArray, $userId); //updation of new password
                if ($chnagePassword) {
                    $userData = $tblUserInfo->fetchDetail($userId); //fetches user email info from bal_user table
                    $userDetails = $this->_parent->getParentData($userId, 'parData');
                    
                    $serverUrl = new Zend_View_Helper_ServerUrl();
                    $baseUrl = new Zend_View_Helper_BaseUrl(); 
                    $parentImage = $serverUrl->serverUrl().$baseUrl->baseUrl('/images/parent_small.png');

                    if (!empty($userDetails['parent_image'])) {

                        $parentImage = AWS_S3_URL . 'parent/thumb/' . $userDetails['parent_image'];

                    }


                    $userName = $userDetails ['first_name'] . " " . $userDetails ['middle_name'] . " " . $userDetails ['last_name'];
                    $url = HOST_NAME . 'finny_parent_login';
                    $mail = new My_Mail();
                    $mail->setSubject('Reset password by Myfinny.com administrator');
                    $template = new Zend_View();
                    $template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
                    $template->assign('name', $userName);
                    $template->assign('email', $userData['email']);
                    $template->assign('password', $passWord);
                    $template->assign('link', $url);
                    $template->assign('profile_picture', $parentImage);
                    $html = $template->render('resetpassword.phtml');
                    $mail->setBodyHtml($html);
                    $mail->addTo($userData['email']);
                    $response = $mail->send();



                    //mail functionality end
                    $this->_helper->flashMessenger->addMessage('Password updated successfully');
                    $this->_redirect('/admin/parentmanager/parentlist');
                } else {
                    $this->view->error = 'Error while updating password';
                }
            }
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $flashMessages = $flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success = $flashMessages[0];
            $flashMessenger->addMessage('');
        }
    }

    /**
     * @desc controller function to handle child list of particular parent
     * @param int parentId
     * @author Suman khatri
     */
    public function childlistAction() {
        try {
            $request = $this->getRequest();
            $tblChildInfo = new Application_Model_DbTable_ChildInfo(); //creates object for model file child info
            $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
            $tblParentNoti = new Application_Model_DbTable_ParentNotifications();
            $parId = $request->getParam('parid'); //getting parameter named parid
            $childList = $tblChildInfo->getChildinfoForParent($parId);
            $childInfo = array();
            $phoneArray = array();
            $i = 0;
            foreach ($childList as $cList) {
                $childInfo[$i]['child_id'] = $cList['child_id'];
                $childInfo[$i]['name'] = $cList['name'];
                $childInfo[$i]['grade_name'] = $cList['grade_name'];
                $childInfo[$i]['image'] = $cList['image'];
                $childInfo[$i]['gender'] = $cList['gender'];                
                $childInfo[$i]['coppa_accepted'] = $cList['coppa_accepted'];                
                $childInfo[$i]['totalquestion'] = 0;
                if($cList['totalquestion'] != NULL){
                    $childInfo[$i]['totalquestion'] = $cList['totalquestion'];
                }
                $childInfo[$i]['totalquizquestion'] = 0;
                if($cList['totalquizquestion'] != NULL){
                    $childInfo[$i]['totalquizquestion'] = $cList['totalquizquestion'];
                }                
                $deviceInfo = $tblChildDeviceRel->getChildAssociateDevices($cList['child_id'],$cList['parent_id']);
                unset($phoneArray);
                $j = 0;
                foreach ($deviceInfo as $dInfo) {
                    if ($dInfo['device_removed'] == 'N') {
                        $phoneArray[$j]['child_device_id'] = $dInfo['device_id'];
                        $phoneArray[$j]['phone_number'] = $dInfo['device_name'];
                        if ($dInfo['device_monitored'] == 'N') {
                            $phoneArray[$j]['phone_number'] = $phoneArray[$j]['phone_number'] . " ( Inactive )";
                        }
                        if (!empty($dInfo['device_key']) && $dInfo['device_monitored'] == 'Y' && $dInfo['device_removed'] == 'N') {
                            $lockInfo = $tblParentNoti->GetNotificationForDevice($dInfo['device_id'],$cList['child_id']);
                            if (!empty($lockInfo) && $lockInfo['status'] == null && $lockInfo['status'] == '') {
                                $phoneArray[$j]['lock_status'] = 1;
                                $phoneArray[$j]['notification_id'] = $lockInfo['notification_id'];
                            } else {
                                $phoneArray[$j]['lock_status'] = 0;
                            }
                        }
                        $j++;
                    } else {
                        continue;
                    }
                }
                $childInfo[$i]['phoneArray'] = $phoneArray;
                $i++;
            }
            $this->view->childInfo = $childInfo;
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }

    /**
     * @desc controller function to handle unlock child device
     * @param int device_id,notification_id
     * @author Suman khatri
     */
    public function unlockdeviceAction() {
        $this->_helper->viewRenderer->setNoRender();
        try {
            $request = $this->getRequest();
            $deviceId = $request->getParam('device_id');
            $notificationId = $request->getParam('notification_id');
            $childId = $request->getParam('childId');
            $tblDeviceInfo = new Application_Model_DbTable_DeviceInfo();
            $tblChildInfo = new Application_Model_DbTable_ChildInfo();
            $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
            $getChildDeviceIfo = $tblDeviceInfo->fetchRow("device_id = $deviceId");
            $tblParentInfo = new Application_Model_DbTable_ParentInfo();
            $childDeviceName = $getChildDeviceIfo->device_name;
            $childInfo = $tblChildInfo->getChildInfo($childId);
            $childName = $childInfo->name;
            $childGender = $childInfo->gender;
            $parId = $childInfo->parent_id;
            $whereparent = "parent_id = $parId";
            $parentInfo = $tblParentInfo->fetchRow($whereparent);
            $userId = $parentInfo['user_id'];
            $registeredId = $getChildDeviceIfo->registered_id;
            if ($getChildDeviceIfo->device_lock_status == 'UNLOCK') {
                $notificationArray = array('message' => 'Device already unlocked', 'deviceStatus' => '1', 'status' => 'success');
                $response = Zend_Json::encode($notificationArray);
                echo $response;
                exit();
            } else if (!empty($registeredId)) {
                $deviceStatus = 'Device Unlock';
                $deviceMessage = 'Unlock device';
                $sendNotificationData = array('process_code' => $deviceStatus, 'message' => $deviceMessage, 'notification_id' => $notificationId);
                $gcm = new My_GCM();
                $result = $gcm->send_notification(array($registeredId), $sendNotificationData);
                if ($result != FALSE) {
                    $insertNotifdata = array(
                        'user_id' => $userId,
                        'notification_type' => 'ADMINUNLOCK',
                        'description' => $childDeviceName . " is unlocked by admin",
                        'seen_by_user' => 'N',
                        'deleted' => 'N',
                        'child_device_id' => $deviceId,
                        'childe_name' => $childName,
                        'created_date' => date('Y-m-d H:i:s'),
                        'child_id' => $childId
                    );
                    $resnotifi = $tblParentNofic->AddParentNotification($insertNotifdata);
                    $notificationArray = array('message' => 'Request to unlock device is sent successfully', 'status' => 'success');
                    $response = Zend_Json::encode($notificationArray);
                    echo $response;
                    exit();
                } else {
                    $notificationArray = array('message' => 'Error while sending request to unlock device', 'status' => 'error');
                    $response = Zend_Json::encode($notificationArray);
                    echo $response;
                    exit();
                }
            } else {
                $notificationArray = array('message' => 'Registeration id not found to send request to unlock device', 'status' => 'error');
                $response = Zend_Json::encode($notificationArray);
                echo $response;
                exit();
            }
        } catch (Exception $e) {
            $notificationArray = array('message' => $e->getMessage());
            $response = Zend_Json::encode($notificationArray);
            echo $response;
            exit();
        }
    }
    
     /**
     * @desc controller function to get virtual child list
     */
    
    public function virtualchildrenAction(){
        try {
            $request = $this->getRequest();
            $tblChildInfo = new Application_Model_DbTable_ChildInfo(); //creates object for model file child info
            $tblChildDeviceRel = new Application_Model_DbTable_ChildDeviceRelationInfo();
            $tblParentDeviceRel = new Application_Model_DbTable_ParentDeviceRelationInfo();
            
            
            $searchData = $request->getParam('search'); //getting parameter named search
            if (empty($searchData)) {
                //fetch data without any condition
                $childList = $tblChildInfo->getChildinfoForParent(0);
            } else {
                //fetch data according to the search condition
                $childList = $tblChildInfo->getChildinfoForParent(0,$content = str_replace(' ', '', $searchData));
                $this->view->search = $searchData;
            }
            
            
            
            
            //Zend_Debug::dump($childList); exit;
            $childInfo = array();
            $i = 0;
            foreach ($childList as $cList) {
                $childInfo[$i]['child_id'] = $cList['child_id'];
                $childInfo[$i]['name'] = $cList['name'];
                $childInfo[$i]['grade_name'] = $cList['grade_name'];
                $childInfo[$i]['image'] = $cList['image'];
                $childInfo[$i]['gender'] = $cList['gender'];
                $childInfo[$i]['created'] = $cList['created_date'];
                $childInfo[$i]['totalquestion'] = 0;
                if($cList['totalquestion'] != NULL){
                    $childInfo[$i]['totalquestion'] = $cList['totalquestion'];
                }
                $childInfo[$i]['totalquizquestion'] = 0;
                if($cList['totalquizquestion'] != NULL){
                    $childInfo[$i]['totalquizquestion'] = $cList['totalquizquestion'];
                }
                                    
                $status = 'Removed';
                $deviceInfo = $tblChildDeviceRel->getVirtualChildAssociateDevices($cList['child_id']);
                
                if($deviceInfo['is_associated'] == 1) {
                    $status = 'Active';
                } else {
                    $deviceCurrentAssoc = $tblParentDeviceRel->checkDeviceExistOrNotInParentDeviceRelation($deviceInfo['device_id']);
                    
                    if($deviceCurrentAssoc->parent_id != 0) {
                        $status = 'Registered';
                    }
                }
                
                $childInfo[$i]['status'] = $status;
                $i++;
            }
            
            //exit;
            
            $perPage = $request->getParam('perpage'); //getting parameter named perpage
            if ($perPage != null) {
                $recordsPerPage = $perPage; //set record per page according the parameter
            } else {
                $recordsPerPage = PER_PAGE; //set record per page with default value if perpage not found
            }
            $this->view->perpage = $recordsPerPage; //assigns $recordsPerPage variable to view file
            $totalRecords = count($childInfo); //count of records found
            //pagination start
            $page = $this->_getParam('page', 1); //set the page
            $paginator = Zend_Paginator::factory($childInfo);
            $paginator->setItemCountPerPage($recordsPerPage);
            $paginator->setCurrentPageNumber($page);
            
            
            $this->view->totalRecord = $totalRecords; //assign no. of total record to the view file
            $this->view->childInfo = $paginator; //assign records to the view file
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }
    
    
    /**
     * @desc controller function to delete parent account from system
     * @param string email id of parent accounts to be deleted
     * @author Shailendra
     */
    public function deleteparentAction() {
        $request = $this->getRequest();
        try {
            if ($request->isPost()) {
                $parentEmail = $request->getPost('parentEmail');
                $password = $request->getPost('password');
                
                // validate password to delete parent
                if(strcmp($password, DELETE_PARENT_KEY) !== 0){
                    $this->view->error = 'Password to delete parent account does not match';
                    $this->view->parentEmail = $parentEmail;
                    return ;
                }
                
                $parentModel    = new Application_Model_Parents();
                $parentEmails   = explode(',', str_replace(' ', '',$parentEmail));
                $emailsNotFound = array();

                foreach ($parentEmails as $parentEmail){
                    if($parentModel->deleteParent(NULL, $parentEmail)){
                        $parentModel->addDeleteParent($parentEmail);
                    } else {
                        $emailsNotFound[] = $parentEmail;
                    }
                }
                
                if(!empty($emailsNotFound)){
                    $this->view->error = 'Email(s) not found: '. implode(', ', $emailsNotFound);
                } else {
                    $this->_helper->flashMessenger->addMessage('Account(s) deleted successfully');
                    $this->_redirect('/admin/parentmanager/parentlist');
                }
                    
            }
        
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
        }
    }
}