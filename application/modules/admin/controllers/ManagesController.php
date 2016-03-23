<?php
/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Questions
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 
   
 */


class Admin_ManagesController extends Zend_Controller_Action
{

	protected $_adminObj;

	public function init()
	{
		parent::init();
		$layout = Zend_Layout::getMvcInstance();//Create object
		$layout->setLayout('admin',true);//set layout admin
		require_once APPLICATION_PATH.'/../library/functions.php';
		$this->_adminObj = new Application_Model_DbTable_UserLogin();
	}


   public function indexAction()
	{

	}
	/*
	 * function to add category
	 */
	 public function listAction()
	{
	
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
		}
		$request 					= $this->getRequest();
		$searchData = $request->getParam('seachdata');
		$perPage = $request->getParam('perpage'); //echo $perPage; die;
	
		
		if(!empty($perPage)){
			$recordsPerPage = $perPage;
		}else{
			$recordsPerPage = PER_PAGE;
	
		}
		$this->view->perpage = $recordsPerPage;
		
		$adminObj = new Application_Model_DbTable_UserLogin(); 
		
		$data = $adminObj->getList($searchData);
		
		$totalRecords	= count($data);
		$page			=$this->_getParam('page',1);
		$paginator 		= Zend_Paginator::factory($data);
		$paginator->setItemCountPerPage($recordsPerPage);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->Data     = $paginator;
		
		$this->view->totalRecord  = $totalRecords;
		
		$this->view->currentPage  = $page;
		$this->view->searchdata = $searchData;
		
		
	}

    public function addAction(){
	$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
	
		if(is_array($flashMessages) && !empty($flashMessages)){
			
			if($flashMessages[0]=='User already exists' || $flashMessages[0]=='Email already exists' || 
			$flashMessages[0]=='Phone already exists' )
			   $this->view->error = $flashMessages[0];
			else   
			$this->view->success = $flashMessages[0];
		}
		$request         = $this->getRequest();
		$adminObj = new Application_Model_DbTable_UserLogin(); 
		if($request->isPost()){
					
			$name   = $request->getPost('name');
			$username   = $request->getPost('username');
			$password     = $request->getPost('password');
			$email     = $request->getPost('email');
			$phone     = $request->getPost('phone');
			$is_active     = 'Y';
			$image     = basename($request->getPost('image_file_name'));
			$type=2;
			$pass=$password;
			$password=md5($password);
			
			$check_username=$adminObj->check('username',$username);
			$flage=0;
			if($check_username!=0)
			 {
			 $this->view->error = 'User already exists';
			 $flage=1;
			 }
			$check_email=$adminObj->check('email',$email); 
			if($check_email!=0)
			 {
			 $this->view->error = 'Email already exists';
			 $flage=1;
			 }
			 $phoneCheck=trim($phone);
			$check_phone=$adminObj->check('phone',$phoneCheck); 
			if($check_phone!=0)
			 {
			 $this->view->error = 'Phone already exists';
			 $flage=1;
			 }
			 
			 $this->view->name=$name;
			 $this->view->username=$username;
			 $this->view->password=$password;
			 $this->view->email=$email;
			 $this->view->phone=$phone;
			 $this->view->image=$image;
			
			if($flage==0)
			{
			if(empty($image))
			$image='no_image.jpeg';
			$DataArray = array('name' => $name,
									 'username' => $username,
									 'password' =>$password,
									 'email' =>$email,
									 'phone' =>$phone,
									 'image' =>$image,
									 'is_active' =>$is_active,
									 'type' =>$type,
									 'created_date' =>date('Y-m-d H:i:s')
									 
										
			);
			
			 $addData = $adminObj->add($DataArray);
			
				if($addData){
				
                                        $adminImage = $this->view->baseUrl('images/no-image.jpg');
                                        if(!empty($image)){
                                            $adminImage = AWS_S3_URL.'admin/'.$image;
                                        }
					$url = HOST_NAME . 'admin/login/login';
					$mail = new My_Mail();
					$mail->setSubject('Your account is created by Myfinny.com administrator');
					$template = new Zend_View();
                                        
					$template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
					$template->assign('name', $name);
					$template->assign('username', $username);
					$template->assign('password', $pass);
					$template->assign('link', $url);
					$template->assign('profile_picture', $adminImage);
					$html = $template->render('adminreg.phtml');
                                        
					$mail->setBodyHtml($html);
					$mail->addTo($email);
					$response = $mail->send();
				    
				    	$this->_helper->flashMessenger->addMessage('Admin added successfully');
						$this->_redirect('/admin/manages/list');


				}else{
					$this->view->error = 'Error adding data';
				}
			 }	
			}

		}
	

	 
	
	public function editAction()
	{
	    
		$flashMessages 	  	 = $this->_helper->flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			
			if($flashMessages[0]=='User already exists' 
					|| $flashMessages[0]=='Email already exists' 
							|| $flashMessages[0]=='Phone already exists' )
			   $this->view->error = $flashMessages[0];
			else   
			$this->view->success = $flashMessages[0];
		}
		$adminObj = new Application_Model_DbTable_UserLogin(); 
		$request 		   = $this->getRequest();
		$admin_user_id 	   = $request->id; 
		$getData   = $adminObj->getParticular($admin_user_id); 
		
		$this->view->Data = $getData; 
	
		if($request->isPost()){
			$name   = $request->getPost('name');
			$username   = $request->getPost('username');
			//$password     = $request->getPost('password');
			//$oldpassword     = $request->getPost('oldpassword');
			$email     = $request->getPost('email');
			$phone     = $request->getPost('phone');
			$image     = basename($request->getPost('image_file_name'));
			$type=2;
			//$mail_pass=$password;
			//if($oldpassword==$password)
			// {
			// $password=$oldpassword;
			// $mail_pass='';
			// }
			//else
			 //$password=md5($password); 
			
			$check_username=$adminObj->check2('username',$username,$admin_user_id);
			$flage=0;
			if($check_username!=0)
			 {
			 $this->view->error = 'User already exists';
			 $flage=1;
			 }
			$check_email=$adminObj->check2('email',$email,$admin_user_id); 
			if($check_email!=0)
			 {
			 $this->view->error = 'Email already exists';
			 $flage=1;
			 }
			 $phoneCheck=trim($phone);
			$check_phone=$adminObj->check2('phone',$phoneCheck,$admin_user_id); 
			if($check_phone!=0)
			 {
			 $this->view->error = 'Phone already exists';
			 $flage=1;
			 }
			 
			 $this->view->name=$name;
			 $this->view->username=$username;
			 //$this->view->password=$password;
			 $this->view->email=$email;
			 $this->view->phone=$phone;
			 $this->view->image=$image;
			
			
			if($flage==0)
			{
			if(empty($image))
			$image='no_image.jpeg';
			$DataArray = array(		 
									 'email' =>$email,
									 'phone' =>$phone,
									 'image' =>$image,
									 'type' =>$type
			);
			
			try{
				$updateData = $adminObj->edit($DataArray,$admin_user_id);
				/////Email Code////
		
			
			/*  if($updateData) {
			      
					
					$name=$getData['name'];
					$username=$getData['username'];
					$email		= $getData['email'];
					$mail		= new Zend_Mail();
			 			$subject	= 'Your Account Is '.$msg;
						if($mail_pass!='')
						 $pass='Password='.$mail_pass.'<br>';
						else
						 $pass=''; 
			 			$message	= '	<table>
			 			<tr>
			 			<td>
			 			Hello '.$name.',<br><br>
				  		
			 			Your Account Information is changed by Myfinny.com administrator<br><br>
						Name='.$name.'<br>
						Username='.$username.'<br>'.$pass.'
						
						Email='.$email.'<br>
						Phone='.$phone.'<br>
						<br>
			 			Regards,<br>
			 			support@myfinny.com
			 			
			 			</td>
			 			</tr>
			 			</table>';
			 			$mail = new Zend_Mail();
	 					$mail->setBodyHtml($message);
						$mail->setFrom('support@myfinny.com', 'Myfinny Team');
	 					$mail->addTo($email);
	 					$mail->setSubject('Your Account Information is changed by Myfinny.com administrator');
						$response	=	$mail->send();
			 			
					
				 }	 */
				 
		/////Email Code////
				
				
				
				$this->_helper->flashMessenger->addMessage('Data updated successfully');
				$this->_redirect('/admin/manages/list');
				
			}catch(Exception $e){
			
				$this->view->error = $e->getMessage();
			}
			}
			
		}
		
	}
	public function resetpasswordAction(){

		//$this->_helper->layout->disableLayout();
		$request        		= $this->getRequest();
		$userId					= $request->getParam('id');//getting parameter named id
		$this->view->userId		= $userId; //assign userId to the view file
		try {
			if($request->isPost()) {
				$passWord   		= $request->getPost('newPassword');//getting parameter named passWord
				$confirmPassword   	= $request->getPost('confirmPassword');//getting parameter named confirmPassword
				$userId				= $request->getPost('userId');//getting parameter named userId
				$date = date('Y-m-d H:i:s');//create date
				if($passWord=='') {//validate blank password
					$this->view->error 	= 'Please enter password';
				}
				if(validateMinLength($passWord,'8')==false) {//validate minlength of password
					$this->view->error 	= 'Please enter Password of min 8 characters';
				}
				if(validateMaxLength($passWord,'16')==false) {//validate maxlength of password
					$this->view->error = 'Please enter Password of max 16 characters';
				}
				if($confirmPassword=='') {//validate blank confirmpassword
					$this->view->error = 'Please enter Confirm password';
				}
				if($confirmPassword!=$passWord) {//validate match of password and confirm password
					$this->view->error = 'Confirm password does not match';
				}
				$updateDataArray 		= array('password' => md5($passWord));//data to be updated for parent
				$chnagePassword 		= $this->_adminObj->updateAdmin($updateDataArray, $userId);
				//if($chnagePassword) {
					$userData		   	= $this->_adminObj->getParticular($userId);//fetches user email info from bal_user table
					$adminImage = $this->view->baseUrl('images/no-image.jpg');
                                        if(!empty($userData['image'])){
                                            $adminImage = AWS_S3_URL.'admin/'.$userData['image'];
                                        }
					$url = HOST_NAME . 'admin/login/login';
					$mail = new My_Mail();
					$mail->setSubject('Reset password by Myfinny.com administrator');
					$template = new Zend_View();
					$template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
					$template->assign('name', $userData['name']);
					$template->assign('username', $userData['username']);
					$template->assign('password', $passWord);
					$template->assign('link', $url);
					$template->assign('profile_picture', $adminImage);
					$html = $template->render('resetpassword.phtml');
					$mail->setBodyHtml($html);
					$mail->addTo($userData['email']);
					$response = $mail->send();
					
					
					
					//mail functionality end
					$this->_helper->flashMessenger->addMessage('Password updated successfully');
					$this->_redirect('/admin/manages/list');
				//} else {
					//$this->view->error = 'Error while updating password';
				//}
			}
		} catch(Exception $e) {
			$this->view->error = $e->getMessage();
		}
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)) {
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
		
	}
    
        /**
         * upload admin image (Ajax Preview)
         */
        public function uploadimageAction()
        {

            $ext = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
            $fileName = preg_replace("/[^A-Za-z0-9]/", '', $_FILES['images']['name']) . '_' . time() . '.' . $ext;
            
            $s3 = new My_Service_Amazon_S3();
            $s3->save(My_Thumbnail::getThumbnail($_FILES["images"]["tmp_name"], $ext, 100, 100), 'admin/' . $fileName);
            echo AWS_S3_URL . 'admin/' . $fileName;
            exit();
        }

    public function activeAction()
	{
	$this->_helper->layout->disableLayout(); 
	$adminObj = new Application_Model_DbTable_UserLogin();
	$request 				= $this->getRequest();
	$id				= $request->getParam('id');
	$status				= $request->getParam('status');
	
	if($status=='Y')
	 {
	  $status='N';
	  $msg='Deactivated';
	 }
	else
	 {
	  $status='Y' ; 
	  $msg='Activated';
	 }
	
	$DataArray = array('is_active' =>$status);
	
	$updateData = $adminObj->edit($DataArray,$id);
	
	
		/////Email Code////
		
			
			 if($updateData) {
			 	
			 	
			 	
			     $getData   = $adminObj->getParticular($id); 
                             $adminImage = $this->view->baseUrl('images/no-image.jpg');
                             if(!empty($getData['image'])){
                                $adminImage = AWS_S3_URL.'admin/'.$getData['image'];
                             }
					$name=$getData['name'];
					$email		= $getData['email'];

					
					/******************sending mail section**********/
					$mail = new My_Mail();
					$mail->setSubject('Your myfinny account status');
					$template = new Zend_View();
					$template->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/emails/');
					$template->assign('name', $name);
					$template->assign('status',$msg);
					$template->assign('profile_picture', $adminImage);
					$html = $template->render('status.phtml');
					$mail->setBodyHtml($html);
					$mail->addTo($email);
					$response = $mail->send();
					/******************sending mail section**********/
					
				 }	
				 
		/////Email Code////
	$this->_helper->flashMessenger->addMessage('Admin '.strtolower($msg).' successfully');
	$resData = array('message' => 'success');
	$response = Zend_Json::encode($resData);
	echo $response;
	exit();
	//$this->_redirect('/admin/manages/list');
	}

}
