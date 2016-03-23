<?php

/**
 * Application_Service_User_ResetPassword File Doc Comment
 * PHP version 5
 * 
 * @category   User
 * @package    User
 * @subpackage To_Add/Update/login/registartion_Info_Of_User
 * @author     Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 * @license    A3logics India (pvt) Ltd
 * @link       http://www.a3logics.com
 * 
 */
class Application_Service_User_ResetPassword
{

    /**
     * 
     */
    private $_objectParent;

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
    }

    public function sendResetPasswordLink($rawEmail, $type)
    {

        $email = trim(strtolower($rawEmail));
        $validate = $this->validateEmail($email);
        if (!empty($validate)) {

            $status['status_code'] = STATUS_ERROR;
            $status['status'] = 'error';
            $status['message'] = $validate['message'];
            return $status;
        }
        $userInfo = $this->_objectParent->getParentDataByEmailId($email);
        $userId = $userInfo->user_id;

        // generate passcode
        $url = $this->generateResetPasswordUrl($userId);

        $mail = new My_Mail();
        $mail->setSubject('Retrieve Forgot Password');
        $mail->setBodyHtml($this->getEmailContent($userId, $url));
        $mail->addTo($email);

        try {
            $success = $mail->send();
            if ($success) {
                $message = 'Change password request has been sent to your Email '
                        . 'address. Please check your SPAM folder in case email is '
                        . 'marked spam.';
                $status['status_code'] = STATUS_SUCCESS;
                $status['status'] = 'success';
                $status['message'] = $message;
            } else {

                $status['status_code'] = STATUS_ERROR;
                $status['status'] = 'error';
                $status['message'] = "Some error while sending mail";
            }
        } catch (Exception $e) {

            $status['status_code'] = STATUS_SYSTEM_ERROR;
            $status['status'] = 'error';
            $status['message'] = $e->getMessage();
        }
        return $status;
    }

    public function validateEmail($email)
    {

        $error = FALSE;

        //Block to validat email
        if (!$error && empty($email)) {
            $errorMessage = 'Please enter Email';
            $error = TRUE;
        }

        $result = validateEmail($email);
        if (!$error && $result == false) {
            $errorMessage = 'Please enter valid Email';
            $error = TRUE;
        }

        // check if email exist ot not
        $checkEmailExist = $this->_objectParent->checkParentEmailId($email);
        if (!$error && !$checkEmailExist) {
            $errorMessage = 'Email address does not exist';
            $error = TRUE;
        }

        $userInfo = $this->_objectParent->getParentDataByEmailId($email);
        if (!$error && ($userInfo == null)) {
            $errorMessage = 'Email address does not exist';
            $error = TRUE;
        }

        if ($error) {
            return array('success' => FALSE, 'message' => $errorMessage);
        } else {
            return null;
        }
    }

    public function generateResetPasswordUrl($userId)
    {

        // Call function for get today date
        $createdDate = todayZendDate();
        $this->expirePreviousTokens($userId, $createdDate);

        $changePasscode = substr(md5(rand()), 0, 7);

        // call function to get expiry date after one day
        $expiryDate = expiryData(1);

        $chnagePassRqstData = array(
            'user_id' => $userId,
            'verify_code' => $changePasscode,
            'created_date' => $createdDate,
            'expiry_date' => $expiryDate
        );

        // function to add change password data
        $this->_objectParent->changeParentPassword($chnagePassRqstData);

        $encodedUserId = base64_encode($userId);

        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();

        return $serverUrl->serverUrl() . $baseUrl->baseUrl('resetpassword/userId/' . $encodedUserId . '/changecode/' . $changePasscode);
    }

    public function expirePreviousTokens($userId, $createdDate)
    {

        $getAllPreviousRequest = $this->_objectParent->getAllPreviousRequest($userId, $createdDate);
        foreach ($getAllPreviousRequest as $allRequests) {
            $requestId = $allRequests['request_id'];
            $createdDateNew = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -2 days"));
            $expiryDateNew = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " -1 days"));
            $updateRequestData = array(
                'created_date' => $createdDateNew,
                'expiry_date' => $expiryDateNew
            );
            $this->_objectParent->expirePreviousRequests($updateRequestData, $requestId); // call function to add change password data
        }
    }

    public function getEmailContent($userId, $url)
    {

        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();

        $parentImage = $serverUrl->serverUrl() . $baseUrl->baseUrl('/images/parent_small.png');

        $userDetails = $this->_objectParent->getParentData($userId, 'parData');

        if (!empty($userDetails['parent_image'])) {

            $parentImage = AWS_S3_URL . 'parent/thumb/' . $userDetails['parent_image'];
        }


        $userName = $userDetails ['first_name'] . " " . $userDetails ['middle_name'] . " " . $userDetails ['last_name'];

        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
        $template->assign('name', $userName);
        $template->assign('profile_picture', $parentImage);
        $template->assign('reset_link', $url);

        return $template->render('forgot.phtml');
    }

}
