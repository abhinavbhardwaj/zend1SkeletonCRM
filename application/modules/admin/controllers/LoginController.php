<?php
/**
 * @category     Admin modules
 * @package    CRM
 * @subpackage Login
 * @copyright  Copyright (c) Abhinav Bhardwaj 
 * @Library    Zend FrameWork 1.11
 * @version    CRM 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses LoginController
 * @category Login
 * @package Zend_Application
 * @subpackage Login
 */
class Admin_LoginController extends Zend_Controller_Action {
	/*
	 * function call during initialization
	 */
	protected $_tblobjectParent;
	protected $_tblUser;
	public function init() {
		parent::init ();
		$layout = Zend_Layout::getMvcInstance (); // Create object
		$layout->setLayout ( 'auth', true ); // set layout auth
		$this->_tblobjectParent = new Application_Model_Parents ();
		$this->_tblUser = new Application_Model_DbTable_UserLogin();
		require_once (APPLICATION_PATH . '/../library/functions.php');
	}
	public function indexAction() { /* Redirect to Login Page */
		$this->_redirect ( 'admin/login/login' );
	}
	/*
	 * This is action function used in login page.
	 */
	public function loginAction() {
	 
        $this->view->headTitle(ADMIN_LOGIN); 
		$adminInfoSession = new Zend_Session_Namespace ( 'adminInfo' );
		$request = $this->getRequest ();
		$flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$flashMessages = $flashMessenger->getMessages ();
		if (is_array ( $flashMessages ) && ! empty ( $flashMessages )) {
			$this->view->message = $flashMessages [0];
			if (isset ( $flashMessages [1] ) && $flashMessages [1] != '') {
				$this->view->success = $flashMessages [1];
				$flashMessages [1] = '';
			}
			$flashMessages [0] = '';
		}
		
		if (isset ( $adminInfoSession->adminData )) {
			$this->_redirect ( 'admin/index/dashboard' );
		}
		if ($request->isPost ()) {
			
			$userName = $request->getPost ( 'username' );
			$password = $request->getPost ( 'password' );
			if (trim ( $userName ) != '' && trim ( $password ) != '') {
				$tblUserLogin = new Application_Model_DbTable_UserLogin ();
				$checkUserExist = $tblUserLogin->existUser ( $userName, $password );
                                
				if (empty($checkUserExist)) {
					$this->view->errorMsg = 'User does not exist';
					return false;
                                }else if($checkUserExist['is_active'] == 'N'){
                                    $this->view->errorMsg = 'Your account hasbeen deactivated by administrator';
					return false;
                                } else {
					
					$fetchUserData = $tblUserLogin->fetchUserdata ( $userName );
					$adminInfoSession->adminData = $fetchUserData;
					$this->_redirect ( 'admin/index/dashboard' );
				}
			} else {
				$this->view->errorMsg = 'User name or password can not be blank';
				return false;
			}
		}
	}
	/*
	 * function for reset password when user forget password
	 */
	public function forgotpasswordAction() {
                $this->view->headTitle(ADMIN_FORGOTPASS);
		$tblUser = new Application_Model_DbTable_UserLogin();
		$flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$flashMessages = $this->_helper->flashMessenger->getMessages ();
		if (is_array ( $flashMessages ) && ! empty ( $flashMessages )) {
			$this->view->message = $flashMessages [0];
			$flashMessages [0] = '';
		}
		
		$request = $this->getRequest ();
		$auth = Zend_Auth::getInstance ();
		$email = "";
		try {
			if ($request->isPost ()) {
				$email = $request->getPost ( 'email' );
				if (! empty ( $email )) {
					$userInfo = $tblUser->fetchUserdataByEmail($email);
					if ($userInfo != null) {
						if($userInfo['is_active'] != 'N'){
						$password = substr ( md5 ( rand () ), 0, 7 );
						$fullName = ucwords ( $userInfo['name'] );
						$userId = $userInfo['admin_user_id'];
						$chnagePasscode = substr(md5(rand()), 0, 7);
                                                $adminImage = $this->view->baseUrl('images/no-image.jpg');
						if(!empty($userInfo['image'])){
							$adminImage = AWS_S3_URL.'admin/'.$userInfo['image'];
						}
						
						$expiryDate = expiryData(1); // call function to get expiry date after one day
						$createdDate = todayZendDate(); // Call function for get today date
						$chnagePassRqstData = array(
								'user_id' => $userId,
								'verify_code' => $chnagePasscode,
								'created_date' => $createdDate,
								'expiry_date' => $expiryDate
						);
						$addrequest = $this->_tblobjectParent->changeParentPassword($chnagePassRqstData); // call function to add change password data
						$url = $this->view->serverUrl().HOST_NAME . '/admin/login/resetpassword/userId/' . base64_encode($userId) . '/changecode/' . $chnagePasscode;
						
						/*                         * ****************sending mail using my lib class******** */
						$mail = new My_Mail();
						$mail->setSubject('Retrieve Forgot Password');
						$template = new Zend_View();
						$template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
						$template->assign('name', $fullName);
						$template->assign('reset_link', $url);
						 $template->assign('profile_picture', $adminImage);
						$html = $template->render('forgot.phtml');
						
						$mail->setBodyHtml($html);
						$mail->addTo($email);
						$response = $mail->send();
						/* $mail = new Zend_Mail ();
						$subject = "All Rental Houses admin password has been reset";
						$message = '	<table>
			 			<tr>
			 			<td>
			 			Dear ' . $fullName . ',<br><br>
				  		On you request admin password has been reset.<br>
			 			Now your password is : <b>' . $password . '</b><br><br>
			 			Thanks,<br>
			 			All Rental Houses Team
			 			</td>
			 			</tr>
			 			</table>';
						$mail = new Zend_Mail ();
						$mail->setBodyHtml ( $message );
						$mail->addTo ( $email );
						$mail->setSubject ( 'All Rental Houses Forget Password Retriew.' );
						$response = $mail->send (); */
						if ($response) {
							//$res = $tblUser->editPassword ( $email, $password );
							$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ('Your password has been reset and sent to your e-mail address.') ;
							$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( 'Success' );
							$this->_redirect ( 'admin/login/login' );
						} else {
							$this->view->errorMsg = 'Sending mail failed';
							//$this->_redirect ( 'admin/login/login' );
						}
						}else{
							$this->view->errorMsg = 'Your account hasbeen deactivated by administrator';
						}	
					} else {
						$this->view->errorMsg = 'Email address does not exist';
						//$this->_redirect ( 'admin/login/login' );
					}
				} else {
					$this->view->errorMsg = 'Please enter email address.';
				}
			}
		} catch ( Exception $ex ) {
			$this->view->errorMsg = $ex->getMessage ();
		}
		$this->view->email = $email;
	}
	
public function resetpasswordAction(){
        $this->view->headTitle(ADMIN_RESETPASS);
	// $this->_helper->layout()->setLayout('loginlayout');
	try {
		$request = $this->getRequest();
		$userId = $request->getParam('userId');
		$userId = base64_decode($userId);
		$this->view->userId = $userId;
		$message = '';
		if (!$request->isPost()) {
			$verficationCode = $request->getParam('changecode');
	
			/*                 * **************block to check about link********************* */
	
			if (empty($verficationCode)) {
				$message = "Request code does not exist";
			}
			$fetchRequestData = $this->_tblobjectParent->checkChangePasswordEmailVerify($verficationCode, $userId);
	
			if (empty($fetchRequestData)) {
				$message = "Request does not exist";
			} else {
				$verified = $fetchRequestData->used;
				$todayDate = todayZendDate();
				$expiryDate = $fetchRequestData->expiry_date;
				$urlExipryDate = strtotime(formatDate($expiryDate, 1)); // case 1 for date formate like "Y-m-d H:i:s"
				$hours = getHours($todayDate);
				if (strtotime($expiryDate) <= strtotime($todayDate)) { // check verify expired or not
					// check coupon expired or not
					$message = 'Link is expired. Make a new request.';
				}
				if ($verified == 'Y') { // if to check verify or not
					$message = 'Link has already been used. Make a new request.';
				} // end if
			}
	
			/**
			 * **********************Block to verify the code with the particular parent ***************************
			 */
			if (!empty($message)) {
				$this->view->errorMsg = $message;
			}
			/*                 * ************************dgdgdg********************************* */
		}
		if ($request->isPost()) {
			$verficationCode = $request->getPost('verficationCode');
			$userId = $request->getPost('userId');
			$newPassword = $request->getPost('password');
			$confPassword = $request->getPost('confirmpassword');
			if ($newPassword != $confPassword) {
				$message = "New password and confirm password do not match";
			}
			if (empty($verficationCode)|| empty($userId)) {
				$message = "Request code does not exist";
			}if(!empty($verficationCode) && !empty($userId)){
				$fetchRequestData = $this->_tblobjectParent->checkChangePasswordEmailVerify($verficationCode, $userId);
			}
	
			if (empty($fetchRequestData)) {
				$message = "Request does not exist";
			} else {
				$verified = $fetchRequestData->used;
				$todayDate = todayZendDate();
				$expiryDate = $fetchRequestData->expiry_date;
	
				$urlExipryDate = strtotime(formatDate($expiryDate, 1)); // case 1 for date formate like "Y-m-d H:i:s"
				$hours = getHours($todayDate);
	
	
				if (strtotime($expiryDate) <= strtotime($todayDate)) { // check verify expired or not
					// check coupon expired or not
					$message = 'Link is expired. Make a new request.';
				}
				if ($verified == 'Y') { // if to check verify or not
					$message = 'Link has already been used. Make a new request.';
				} // end if
			}
	
			/**
			 * **********************Block to verify the code with the particular parent ***************************
			 */
			if (!empty($message)) {
			$this->view->errorMsg = $message;
			} else {
				$updateRequestData = array(
						'used' => 'Y'
				);
				// call function to update data into db
				$updateRequestData = $this->_tblobjectParent->updateParentChangePasswordVerify($updateRequestData, $userId, $verficationCode);
				if ($updateRequestData) {
					$data = array('password'=>md5($newPassword));
					$this->_tblUser->updateAdmin($data, $userId);
					$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( 'Password has been changed successfully' );
					$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( 'Success' );
					$this->_redirect ( 'admin/login/login' );
				}
			}
		}
	} catch (Exception $e) {
		$this->view->errorMsg = $e->getMessage();
	}
		$this->view->userId = $userId;
		$this->view->verficationCode = $verficationCode;
	}
	
	
	
	
	
	
	/*
	 * function logout from the admin
	 */
	public function logoutAction() {
		$adminInfoSession = new Zend_Session_Namespace ( 'adminInfo' );
		$adminInfoSession->unsetAll ();
		$this->_helper->redirector ( 'login' ); // back to login page
	}
	/**
	 * function for change password
	 */
	public function changepasswordAction() {
		$tblUserLogin = new Application_Model_DbTable_UserLogin ();
		$layout = Zend_Layout::getMvcInstance (); // Create object
		$layout->setLayout ( 'admin', true ); // set layout admin
		$adminInfoSession = new Zend_Session_Namespace ( 'adminInfo' );
		$adminData = $adminInfoSession->adminData;
		$userId = $adminData->admin_user_id;
		$password = $adminData->password;
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$oldPassword = $request->getPost ( 'oldPassword' );
			$newPassword = $request->getPost ( 'newPassword' );
			$confirmPassword = $request->getPost ( 'confirmPassword' );
			if ($password == md5 ( $oldPassword )) {
				$newPasswordEncr = md5 ( $newPassword );
				$data = array (
						'password' => $newPasswordEncr 
				);
				$tblUserLogin->updateAdmin ( $data, $userId );
				//$this->view->success = 'Your password has been changed successfully';
				$this->_helper->flashMessenger->addMessage('Your password has been changed successfully');
				$this->_helper->flashMessenger->addMessage('success');
				$this->_redirect ( 'admin/index/dashboard' );
			} else {
				$this->view->error = 'Current password is not correct';
			}
		}
	}
}  