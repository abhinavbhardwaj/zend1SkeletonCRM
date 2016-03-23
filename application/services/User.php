<?php

/**
 * @category  Service_User
 * @package   User
 * @author    Suman Khatri <suman.khatri@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_User
{

    /**
     * defined all object variables that are used in entire class
     */
    private $_objectParent;
    private $_objectDeviceInfo;
    private $_objectChild;
    private $_objectApps;

    /**
     * construct funtion
     */
    public function __construct()
    {
        //including functions.php
        include_once APPLICATION_PATH . '/../library/functions.php';
        //including validate.php
        include_once APPLICATION_PATH . '/../library/validate.php';
        // creates object of class Parents
        $this->_objectParent = new Application_Model_Parents();
        // creates object of class DeviceInfo
        $this->_objectDeviceInfo = new Application_Model_DeviceInfo();
        // creates object of class Child
        $this->_objectChild = new Application_Model_Child();
        //creates object of class Apps
        $this->_objectApps = new Application_Model_Apps();
    }

    /**
     * @desc Function to validate registration variables 
     * @param array  $data Data to be valiate
     * @param string $type Type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     */
    private function _validateRegistration($data, $type)
    {
        $messageE = '';
        $errorTypeE = false;
        $errorTypeN = false;
        // getting param ConfrPassWord
        $ConfrPassWord = $data['ConfrPassWord'];
        // getting param firstName
        $firstName = trim($data['firstName']);
        // getting param lastName
        //$lastName = trim($data['lastName']);
        $lastName = '';
        // getting param emailId
        $emailId = trim(strtolower($data['emailId']));
        // getting param rPassWord
        $rPassWord = $data['rPassWord'];
        // Block to validate fields
        $message = validateFirstName($firstName);
        //Block to validat email
        if (empty($message) && $message == null) {
            $message = validateEmailId($emailId);
            $errorTypeE = true;
        }
        //End block to validat email
        //Block to validat password
        if (empty($message) && $message == null) {
            $message = validatePassword($rPassWord);
        }
        //End block to validat password         
        //Block to validate confpassword
        if (empty($message) && $message == null) {
            if (validateNotNull($ConfrPassWord) == false) {
                $message = "Please enter Confirm Password";
            }
        }
        if (empty($message) && $message == null) {
            if ($rPassWord != $ConfrPassWord) {
                $message = "Password and Confirm Password are not matched";
                $errorTypeE = true;
            }
        }
        // Block to check Email already exist or not
        $checkEmail = $this->_objectParent->checkParentEmailId($emailId);
        if ($checkEmail) { // check if emailid exist or not
            $messageE = "Email address already exist";
            $errorTypeE = true;
        } // End block to check Email already exist or not
        // retruns array if any field is not validate
        if (!empty($messageE)) {
            $message = $messageE;
        }
        if (!empty($message) && $message != null) {
            $messageArray = array(
                'message' => $message,
                'status' => 'error',
                'errorTypeE' => $errorTypeE,
                'errorTypeN' => $errorTypeN
            );
        } else {
            $messageArray = null;
        }
        return $messageArray;
    }
    /**
     * @desc Function to validate registration variables 
     * @param array  $data Data to be valiate
     * @param string $type Type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     */
    private function _validateParentChildRegistration($data, $type)
    { 
        $messageE = '';
        $errorTypeE = false;
        $errorTypeN = false;
        // getting param ConfrPassWord
        $ConfrPassWord = $data['ConfrPassWord'];
        // getting param firstName
        $firstName = trim($data['firstName']);
        
        // getting param kid name
        $kidName = trim($data['kid_f_name']);
        
        $lastName = '';
        // getting param emailId
        $emailId = trim(strtolower($data['emailId']));
        // getting param rPassWord
        $rPassWord = $data['rPassWord'];
         // Block to validate fields
        $message = validateFirstName($firstName);
        // Block to validate fields
        $message = validateKidFirstName($kidName);
        //Block to validat email
        if (empty($message) && $message == null) {
            $message = validateEmailId($emailId);
            $errorTypeE = true;
        }
        //End block to validat email
        //Block to validat password
        if (empty($message) && $message == null) {
            $message = validatePassword($rPassWord);
        }
        //End block to validat password         
        //Block to validate confpassword
        if (empty($message) && $message == null) {
            if (validateNotNull($ConfrPassWord) == false) {
                $message = "Please enter Confirm Password";
            }
        }
        if (empty($message) && $message == null) {
            if ($rPassWord != $ConfrPassWord) {
                $message = "Password and Confirm Password are not matched";
                $errorTypeE = true;
            }
        }
        // Block to check Email already exist or not
        $checkEmail = $this->_objectParent->checkParentEmailId($emailId);
        if ($checkEmail) { // check if emailid exist or not
            $messageE = "Email address already exist";
            $errorTypeE = true;
        } // End block to check Email already exist or not
        // retruns array if any field is not validate
        if (!empty($messageE)) {
            $message = $messageE;
        }
        if (!empty($message) && $message != null) {
            $messageArray = array(
                'message' => $message,
                'status' => 'error',
                'errorTypeE' => $errorTypeE,
                'errorTypeN' => $errorTypeN
            );
        } else {
            $messageArray = null;
        }
        return $messageArray;
    }    
    /**
     * @desc Function to send mail
     * @param array $data             data to be send 
     * @param int   $parId            parent Id
     * @param int   $verificationCode verification code
     * 
     * @author suman khatri on October 08 2014
     * @return array
     */
    private function _sendMail($data, $parId, $verificationCode, $childId=NULL)
    {
        $messageArray = array();

        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();

        $token          =   $this->_objectChild->createCoppaReminderToken($childId);
        $url            =   $serverUrl->serverUrl() . $baseUrl->baseUrl('/auth/verify-parent-child-registration/parId/' . $parId . '/verifycode/' . $verificationCode."/childId/".$childId.'/token/'.$token);

        // getting param firstName
        $firstName = trim($data['firstName']);
        // getting param lastName
        //$lastName = trim($data['lastName']);
        $emailId = trim($data['emailId']); // getting param emailId
        $rPassWord = $data['rPassWord']; // getting param rPassWord
        //Delete account Link:
        $deleteAccount = "mailto:".ADMIN_MAIL_ADDRESS."?subject=Finny App - Request to remove my account&reply-to=$emailId&body=Please remove my account username:$emailId";
        $mail = new My_Mail();
        $mail->setSubject('Account Confirmation');
        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
        $template->assign('name', ucwords($firstName));
        $template->assign('email', $emailId);
        $template->assign('password', $rPassWord);
         $template->assign('deleteAccount', $deleteAccount);
        $template->assign('verify_link', $url);
        $html = $template->render('parentChildVerify.phtml');

        $mail->setBodyHtml($html);
        $mail->addTo($emailId);
        $mail->setDefaultFrom(COPPA_MAIL_FROM, EMAIL_FROM_NAME );
        $mail->setDefaultReplyTo(ADMIN_MAIL_FROM, EMAIL_FROM_NAME);
        try {
            $mail->send(); // call send mail function
        } catch (Exception $e) {
            $messageArray = array(
                'message' => $e->getMessage(),
                'status' => 'error'
            );
        }
        return $messageArray;
    }

    /**
     * @desc Function to handle registration process
     * @param array  $data data to be register
     * @param string $type type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     */
    public function registration(array $data, $type)
    {
        /* DECLARED */
        $accessToken = '';
        $status = array();
        $validate = $this->_validateRegistration($data, $type);
        if (empty($validate)) { //if data is validate
            // Generate the code for verrification of parent email
            $verificationCode = substr(rand(), 0, 9);
            // getting param emailId
            $emailId = trim(strtolower($data['emailId']));
            // getting param firstName
            $firstName = trim($data['firstName']);
            // getting param rPassWord
            $rPassWord = $data['rPassWord'];
            // Convert password into md5
            $encodedPassword = md5($rPassWord);
            $todayDate = todayZendDate(); // Call function to get zend today date
            // Array varibles to insert data into table
            $userData = array(
                'email' => $emailId,
                'password' => $encodedPassword,
                'pin' => null,
                'verification_code' => $verificationCode,
                'created_date' => $todayDate,
            );
            // Call function to add data into table
            $userId = $this->_objectParent->addUserData($userData);
            if ($userId) { // if check to data inserted into db or not
                // add parent data into db
                $parentId = $this->_objectParent->addParentData(
                        $firstName, null, null, '', $userId, $todayDate, null
                );
                if ($parentId) { //if parent id is not blank sned mail
                    // add parent verified email info into db
                    $addEmail = $this->_objectParent->addParentEmail(
                            $emailId, $parentId, $accessToken, $todayDate
                    );
                    //send mail for email varification
                    $mailStatus = $this->_sendMail(
                            $data, base64_encode($userId), $verificationCode
                    );
                    if (empty($mailStatus)) {
                        //if mail is sent succefully return success message
                        $succesMessage = 'You are registered successfully and '
                                . 'activation link has been sent to your Email '
                                . 'address. Please check your SPAM folder in case'
                                . ' email is marked spam.';
                        $status = array(
                            'message' => $succesMessage,
                            'status' => 'success'
                        );
                        if ($type == 'mobile') {
                            unset($status['status']);
                            $status['status_code'] = STATUS_SUCCESS;
                        }

                        $response = Zend_Json::encode($status);
                        return $response;
                    } else { //if mail is not sent then return error message
                        $status['message'] = $mailStatus;
                        if ($type == 'mobile') {
                            $status['status_code'] = STATUS_SYSTEM_ERROR;
                        }
                        if ($type == 'web') {
                            $status['status'] = 'error';
                        }
                        $response = Zend_Json::encode($mailStatus);
                        return $response;
                    }
                } else { //else parent id is null then return error message
                    $status = array(
                        'message' => 'There is some problem in registration'
                    );
                    if ($type == 'web') {
                        $status['status'] = 'error';
                    }
                    if ($type == 'mobile') {
                        unset($validate['status']);
                        $status['status_code'] = STATUS_SYSTEM_ERROR;
                    }
                    $response = Zend_Json::encode($status);
                    return $response;
                }
            }
        } else { //else data is not validate then return error message
            if ($type == 'mobile') {
                unset($validate['errorTypeE']);
                unset($validate['errorTypeN']);
                unset($validate['status']);
                $validate['status_code'] = STATUS_ERROR;
            }
            $response = Zend_Json::encode($validate);
            return $response;
        }
    }
    /**
     * @desc Function generate randam password for the user
     *  
     * @author abhinav bhardwaj on October 27 2015
     * @return string
     */    
    private function _generatePassword() {
            $chars          =   "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $Number         =   "1234567890";
            $spChars        =   "!@#$*";
            $tmpPassword    =       "";
            $tmpPassword    .=   substr( str_shuffle( $chars ), 0, 4 );
            $tmpPassword    .=   substr( str_shuffle( $Number ), 0, 2 );
            $tmpPassword    .=   substr( str_shuffle( $spChars ), 0, 2 );
            $password        =   str_shuffle( $tmpPassword );
            return $password;
        }
    /**
     * @desc Function to handle Parent Child registration process
     * @param array  $data data to be register
     * @param string $type type of request
     * 
     * @author abhinav bhardwaj on October 27 2015
     * @return array
     */
    public function parentChildRegistration(array $data, $type)
    {
                
        if(empty($data['rPassWord'])){
            $data['rPassWord']          =  $this->_generatePassword();
            $data['ConfrPassWord']      =  $data['rPassWord'];
        }
        
        if(empty($data['firstName'])){
            $data['firstName']          =   $data['emailId'];
        }
            
    
        /* DECLARED */
        $accessToken = '';
        $status = array();
        $validate = $this->_validateParentChildRegistration($data, $type);
        if (empty($validate)) { //if data is validate
            // Generate the code for verrification of parent email
            $verificationCode   =       substr(rand(), 0, 9);
            // getting param emailId
            $emailId            =       trim(strtolower($data['emailId']));
            // getting param firstName
            $firstName          =       trim($data['firstName']);
            // getting param rPassWord
            $rPassWord          =       $data['rPassWord'];
            // Convert password into md5
            $encodedPassword    =       md5($rPassWord);
            $todayDate          =       todayZendDate(); // Call function to get zend today date
            
            //kid data
            $kidFname           =       trim($data['kid_f_name']);
            $gradeId            =       trim($data['grade']);
           
            $coppa_required     =       trim($data['coppa_required']);
            $dateOfBirth        =       NULL;
            $deviceId           =       trim($data['device_key']);
            
            // Array varibles to insert data into table
            $userData = array(
                'email' => $emailId,
                'password' => $encodedPassword,
                'pin' => null,
                'verification_code' => $verificationCode,
                'created_date' => $todayDate,
            ); 
            // Call function to add data into table
                $userId         =       $this->_objectParent->addUserData($userData);
            if ($userId) { // if check to data inserted into db or not
                // add parent data into db
                $parentId       =       $this->_objectParent->addParentData(
                        $firstName, null, null, '', $userId, $todayDate, null
                );
                if ($parentId) {
                    //if parent has been register successfully add his child
                    $childService   =   new Application_Service_Kid();
                    $childId        =   $childService->addChildAndInitiateData($kidFname, $gradeId, $parentId, $coppa_required, $dateOfBirth);
                    $childInfoArray = $this->_objectChild->getChildInfoArray($childId);
                    
                    if ($coppa_required) {
                        $this->_objectChild->resetCoppaReminder($childId);
                            $mailStatus = $this->_sendMail(
                                    $data, base64_encode($userId), $verificationCode, base64_encode($childId)
                            );
                    }
                    else{
                        //send mail for email varification
                            $mailStatus = $this->_sendMail(
                                    $data, base64_encode($userId), $verificationCode, base64_encode($childId)
                            );
                    }
        
                        $childService->assignParentAppsToKid($parentId, $childId, null);
                        $this->_objectChild->sendPushOnAddOrUpdateKid($parentId, $childInfoArray, 'add', $childId, $deviceId);
                        
                        //if parent id is not blank send mail
                        // add parent verified email info into db
                        $addEmail = $this->_objectParent->addParentEmail(
                                $emailId, $parentId, $accessToken, $todayDate
                        );

                    if (empty($mailStatus)) {
                        //if mail is sent succefully return success message
                        $succesMessage =  str_replace("{{email}}" , '<strong>' . $emailId . '</strong>' , PARENT_CHILD_SUCCESS_MESSAGE) ;
                        $status = array(
                            'message'       => $succesMessage,
                            'status'        => 'success',
                            'user_data'     => array('email' => $emailId,
                                                    'password' => $data['rPassWord']),
                           'children_list' => $childInfoArray
                        );
                        if ($type == 'mobile') {
                            unset($status['status']);
                            $status['status_code'] = STATUS_SUCCESS;
                        }

                        $response = Zend_Json::encode($status);
                        return $response;
                    } else { //if mail is not sent then return error message
                        $status['message'] = $mailStatus;
                        if ($type == 'mobile') {
                            $status['status_code'] = STATUS_SYSTEM_ERROR;
                        }
                        if ($type == 'web') {
                            $status['status'] = 'error';
                        }
                        $response = Zend_Json::encode($mailStatus);
                        return $response;
                    }
                } else { //else parent id is null then return error message
                    $status = array(
                        'message' => PARENT_CHILD_ERROR_MESSAGE
                    );
                    if ($type == 'web') {
                        $status['status'] = 'error';
                    }
                    if ($type == 'mobile') {
                        unset($validate['status']);
                        $status['status_code'] = STATUS_SYSTEM_ERROR;
                    }
                    $response = Zend_Json::encode($status);
                    return $response;
                }
            }
        } else { //else data is not validate then return error message
            if ($type == 'mobile') {
                unset($validate['errorTypeE']);
                unset($validate['errorTypeN']);
                unset($validate['status']);
                $validate['status_code'] = STATUS_ERROR;
            }
            $response = Zend_Json::encode($validate);
            return $response;
        }
    }

    /**
     * @desc Function to validate login variables
     * @param array  $data data to be validate
     * @param string $type type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     * */
    private function _validateLogin(array $data, $isMobile)
    {
        $typeError = null;
        $emailIdKey = $isMobile ? 'emailId' : 'emailIdLogin';
        $emailId = !empty($data[$emailIdKey]) ? $data[$emailIdKey] : '';
        $passWord = !empty($data['passWord']) ? $data['passWord'] : '';

        $message = null;
        if (empty($message)) {
            $message = validateEmailId($emailId);
            $typeError = 'email';
        }

        if (empty($message) && !validateNotNull($passWord)) {
            $message = "Please enter Password";
            $typeError = 'password';
        }

        if ($isMobile) {
            if (empty($message) && empty($data['device_key'])) {
                $message = "Device key can't be null or empty";
            }
            if (empty($message) && empty($data['deviceName'])) {
                $message = "Device name can't be null or empty";
            }
            if (empty($message) && empty($data['regID'])) {
                $message = "Registration id can't be blank";
            }
        }

        $messageArray = NULL;
        if (!empty($message)) {
            $messageArray = array(
                'message' => !$isMobile ? 'Invalid email or password' : $message,
                'status_code' => STATUS_ERROR,
                'access_token' => NULL,
                'typeError' => !empty($typeError) ? $typeError : NULL
            );
        }

        return $messageArray;
    }

    /**
     * @desc Function to handle login process
     * @param array  $data data required for login
     * @param string $type type of request
     * 
     * @author suman khatri on October 08 2014
     * @return array
     * */
    public function login(array $data, $type)
    {
        $isMobile = $type == 'mobile';
        $validate = $this->_validateLogin($data, $isMobile);
        if (!empty($validate)) {
            return $validate;
        }

        $emailIdKey = $isMobile ? 'emailId' : 'emailIdLogin';
        $email = trim(strtolower($data[$emailIdKey]));
        $password = $data['passWord'];

        $checkVerifaction = TRUE;
        
        if(isset($data['checkVerifaction']) && ($data['checkVerifaction']==0))
           $checkVerifaction = FALSE;
      
        $validateAuth = $this->validateCredentials($email, $password, $checkVerifaction );
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            $validateAuth['access_token'] = NULL;
            return $validateAuth;
        }
        $user = $validateAuth['user'];

        if (!$isMobile) {
            Zend_Auth::getInstance()->getStorage()->write($user);
            return array(
                'message' => 'Logged in successfully',
                'status' => 'success'
            );
        }

        $parentData = $this->_objectParent->getParentData($user->user_id, 'parData');
        $childCount = $this->_objectChild->childExistWithParent($parentData->parent_id, null);
        $userData = $this->_objectParent->getParentRegistartionInfo($user->user_id);
        $accessToken = $this->_paireDeviceWithParent($data, $parentData->parent_id);

        $objApps = new Application_Service_Apps($data['device_key'], $accessToken);
        $objApps->reset();

        return array(
            'message' => 'Logged in successfully',
            'childExist' => $childCount ? 'Y' : 'N',
            'status_code' => STATUS_SUCCESS,
            'access_token' => $accessToken,
            'pin' => $userData['pin']
        );
    }

    /**
     * @desc function to pair device with parent
     * @param array $data
     * @param int $parentId
     * @return string
     */
    private function _paireDeviceWithParent($data, $parentId)
    {
        $deviceId = $this->_objectDeviceInfo->addOrUpdateDeviceData($data);

        // add device and parent data in bal_parent_device_relation
        $accessToken = substr(rand(), 0, 9);
        $addParentDeviceData = array(
            'parent_id' => $parentId,
            'device_id' => $deviceId,
            'access_token' => $accessToken
        );
        $this->_objectDeviceInfo->addParentDeviceInfo($addParentDeviceData);

        return $accessToken;
    }

    /**
     * @desc function to get paired device info
     * @param string $deviceKey devicekey of device
     *
     * @author suman khatri on October 08 2014
     * @return array
     */
    public function forgotpassword($deviceKey)
    {
        //check if device is exist in bal_device_info table or not
        $deviceInDeviceInfo = $this->_objectDeviceInfo
                ->checkDeviceExistOrNotInDeviceInfo($deviceKey);
        //if device is exist in DB
        if (!empty($deviceInDeviceInfo) && $deviceInDeviceInfo != null) {
            //check if device is exist in bal_parent_device_relation table or not
            $deviceInParentDeviceRelation = $this->_objectDeviceInfo
                    ->checkDeviceExistOrNotInParentDeviceRelation($deviceInDeviceInfo);
            return $deviceInParentDeviceRelation;
        } else {
            return false;
        }
    }

    public function verifyDeviceAssociation($data)
    {
        $objAuth = new Application_Service_User_AuthDevice();
        $validate = $objAuth->authenticate($data['device_key'], $data['access_token']);
        if ($validate['status_code'] == STATUS_ERROR) {
            return $validate;
        }
        $parId = $validate['parentId'];

        $email = trim(strtolower($data['emailId']));
        $password = $data['passWord']; // getting param passWord

        $message = validateEmailId($email);
        if (empty($message) && !validateNotNull($password)) {
            $message = "Please enter Password";
        }

        if (empty($message)) {
            $validateAuth = $this->validateCredentials($email, $password);
            if ($validateAuth['status_code'] == STATUS_ERROR) {
                $message = $validateAuth['message'];
            } else {
                $user = $validateAuth['user'];
                $parentData = $this->_objectParent->getParentData($user->user_id, 'parData');
                if ($parentData->parent_id !== $parId) {
                    $message = 'This device is not associated with you';
                }
            }
        }

        if (empty($message)) {
            $response = array('status_code' => STATUS_SUCCESS, 'message' => 'success');
        } else {
            $response = array('status_code' => STATUS_ERROR, 'message' => $message);
        }

        return $response;
    }

    /**
     * Remember me service function
     */
    public function rememberMe($userData)
    {

        if (empty($userData)) {
            return;
        }

        //echo "<pre>"; print_r($userData); exit;
        $userRememberMeTokenObj = new Application_Model_UserRememberMeToken();
        $tokenData = $userRememberMeTokenObj->getUserTokenData($userData['emailIdLogin']);
        if (empty($tokenData)) {
            /* GENERATE FIRST TOKEN FOR THIS USER */
            $addData['user_email'] = $userData['emailIdLogin'];
            $addData['user_identifier'] = md5($userData['emailIdLogin']);
            $tokenStr = md5(substr($userData['emailIdLogin'], 0, strpos($userData['emailIdLogin'], '@')));
            $addData['user_token'] = $addData['user_identifier'] . '@' . $tokenStr . time();
            $addData['created_date'] = date("Y-m-d H:i:s");
            $result = $userRememberMeTokenObj->saveUserTokenData($addData);
        } else {
            /* REPLACE TOKEN WITH A NEW ONE FOR THIS USER */
            $tokenStr = md5(substr($userData['emailIdLogin'], 0, strpos($userData['emailIdLogin'], '@')));
            $addData['user_token'] = $tokenData['user_identifier'] . '@' . $tokenStr . time();
            $addData['updated_date'] = date("Y-m-d H:i:s");
            $result = $userRememberMeTokenObj->updateUserTokenData($addData, $userData['emailIdLogin']);
        }

        if ($result) {
            /* ASSIGN COOKIE TO USER */
            setcookie(md5('remember_me'), $addData['user_token'], time() + (10 * 365 * 24 * 60 * 60), "/");
        }
    }

    public function validateCredentials($email, $password, $checkVerifaction = TRUE)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable();

        $authAdapter->setTableName('bal_users');
        $authAdapter->setIdentityColumn('email');
        $authAdapter->setCredentialColumn('password');
        $select = $authAdapter->getDbSelect();
        $select->where("is_active = 'Y'");

        $authAdapter->setIdentity($email);
        $authAdapter->setCredential(md5($password));
        $result1 = $auth->authenticate($authAdapter);
        if (!$result1->isValid()) {
            return array(
                'message' => 'Invalid email or password',
                'status' => 'error',
                'status_code' => STATUS_ERROR
            );
        }

        $auth->clearIdentity();
        if($checkVerifaction)//here we are by passing the varifaction email check if request is from auto login
        $select->where("email_verifiied = 'Y'");
        $result2 = $auth->authenticate($authAdapter);
        if (!$result2->isValid()) {
            return array(
                'message' => 'You have not yet confirmed your account. Please check your email '. $email .' and click "Confirm" to continue.',
                'status' => 'error',
                'status_code' => STATUS_ERROR
            );
        }

        $auth->clearIdentity();
        return array(
            'status' => 'success',
            'status_code' => STATUS_SUCCESS,
            'user' => $authAdapter->getResultRowObject()
        );
    }

}
