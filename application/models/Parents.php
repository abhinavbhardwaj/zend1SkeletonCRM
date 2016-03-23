<?php

class Application_Model_Parents extends Zend_Loader_Autoloader {
    /*
     * defined all object variables that are used in entire class
     */

    private $_tblParentInfo;
    private $_tblCmsInfo;
    private $_tblUser;
    private $_tblChildInfo;
    private $_tblSafeNumber;
    private $_tblParentEmails;
    private $_tblChangePassCode;
    private $_tblVideo;
    private $_tblimages;
    private $_parentDeviceRelation;

    /*
     * function for create all model table object used this object to call model table functions
     */

    public function __construct() {
        //create object of Application_Model_DbTable_CmsInfo class
        $this->_tblCmsInfo = new Application_Model_DbTable_CmsInfo ();
        //create object of Application_Model_DbTable_ParentInfo class
        $this->_tblParentInfo = new Application_Model_DbTable_ParentInfo ();
        //create object of Application_Model_DbTable_ParentRegistration class
        $this->_tblUser = new Application_Model_DbTable_ParentRegistration ();
        //create object of Application_Model_DbTable_ChildInfo class
        $this->_tblChildInfo = new Application_Model_DbTable_ChildInfo ();
        //create object of Application_Model_DbTable_SafeNumbers class
        $this->_tblSafeNumber = new Application_Model_DbTable_SafeNumbers ();
        //create object of Application_Model_DbTable_ParentEmail class
        $this->_tblParentEmails = new Application_Model_DbTable_ParentEmail ();
        //create object of Application_Model_DbTable_ChangePassword class
        $this->_tblChangePassCode = new Application_Model_DbTable_ChangePassword ();
        //create object of Application_Model_DbTable_Video class
        $this->_tblVideo = new Application_Model_DbTable_Video ();
        //create object of Application_Model_DbTable_Images class
        $this->_tblimages = new Application_Model_DbTable_Images();
        //create object of Application_Model_DbTable_ParentDeviceRelationInfo class
        $this->_parentDeviceRelation = new Application_Model_DbTable_ParentDeviceRelationInfo();
    }

    /**
     * Function to get parent id
     * 
     * @param
     *        	userId
     * @author suman khatri on 13th November 2013
     * @return parentId or ArrayObject
     */
    public function getParentData($userId, $typeOfData) {
        $parId = false;
        $parentDetail = $this->_tblParentInfo->fetchUser($userId); // fetches parent detail
        if ($typeOfData == 'parId') {
            if (!empty($parentDetail)) {
                $parId = (int) $parentDetail->parent_id; // gets parent id from parent detail
            }
        } else if ($typeOfData == 'parData') {
            $parId = $parentDetail;
        }
        return $parId; // returnd either parent id or parent data
    }

    /**
     * Function to get parent id
     * 
     * @param
     *        	userId
     * @author suman khatri on 13th November 2013
     * @return ArrayIterator
     */
    public function getParentDataByEmailId($emaiId) {
        $parentDetail = $this->_tblUser->fetchUserByDetailByEmail($emaiId); // fetches parent detail
        return $parentDetail; // returnd either parent id or parent data
    }

    /**
     * Function to get all child of a parent
     * 
     * @param
     *        	$childId
     * @author suman khatri on 18th November 2013
     * @return array
     */
    public function getAllChild($childId, $parentId = 0) {
        if($parentId == 0)
        return $this->_tblChildInfo->fetchAll("child_id = '$childId'");
        else
            return $this->_tblChildInfo->fetchAll("parent_id = '$parentId'");
    }

    /**
     * Function to check safe number title is exist with any number or not
     * 
     * @param
     *        	$safeNumberTitle,$safeNumberId,$parentId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function checkNumberExistByTitle($safeNumberTitle, $safeNumberId, $parentId) {
        // creates object of model file SafeNumbers
        $newTitle = strtolower($safeNumberTitle); // converting $safeNumberTitle to lowercase
        // checking whether new title is exist or not in DB with any number
        if (!empty($safeNumberId) && $safeNumberId != null) {
            $checkTitle = $this->_tblSafeNumber->existTitleById($newTitle, $safeNumberId);
        }
        if (!empty($parentId) && $parentId != null) {
            $checkTitle = $this->_tblSafeNumber->existTitle($newTitle, $parentId);
        }
        return $checkTitle; // return result
    }

    /**
     * Function to check safe number is exist with any other record of parent
     * 
     * @param
     *        	$safeNumber,$safeNumberId,$parentId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function checkNumberExistByPhone($safeNumber, $safeNumberId, $parentId) {
        // checking whether phone number is exist or not in DB with any other record
        if (!empty($safeNumberId) && $safeNumberId != null) {
            $checkPhone = $this->_tblSafeNumber->existPhoneById($safeNumber, $safeNumberId);
        }
        // checking whether phone number is exist or not in DB with parent's safe number
        if (!empty($parentId) && $parentId != null) {
            $checkPhone = $this->_tblSafeNumber->existPhone($safeNumber, '', $parentId);
        }
        return $checkPhone; // return result
    }

    /**
     * Function to get child phone info using parentId
     * 
     * @param
     *        	$parentId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getChildPhoneInfoUsingParentId($parentId) {
        // creates object of model file ParentInfo
        $getChildInfo = $this->_tblParentInfo->GetChildInfoByParentId($parentId); // feches child phone info
        return $getChildInfo; // return arrat
    }

    /**
     * Function to update safenumber
     * 
     * @param
     *        	$safeNumberTitle,$safeNumberTitle,$safeNumberId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function updateSafeNumber($safeNumberTitle, $safeNumber, $code, $safeNumberId) {
        // data to be updated
        $updatePhoneNumberData = array(
            'title' => $safeNumberTitle,
            'number' => $safeNumber,
            'country_code' => $code
        );
        // updates safe number
        $updatePhoneNumber = $this->_tblSafeNumber->updateSafeNumberData($updatePhoneNumberData, $safeNumberId);
        return $updatePhoneNumber; // returns result
    }

    /**
     * Function to REMOVE safenumber
     * 
     * @param
     *        	$safeNumberId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function removeSafeNumber($safeNumberId) {
        $removeSadfeNumber = $this->_tblSafeNumber->removeSafeNumber($safeNumberId); // updates safe number
        return $removeSadfeNumber; // returns result
    }

    /**
     * Function to get filtered parent data
     * 
     * @param
     *        	$parentId
     * @author suman khatri on 18th November 2013
     * @return ArrayObject
     */
    public function getFilteredParentData($parentId) {
        $fetchDataArray = array(
            'first_name',
            'middle_name',
            'last_name',
            'parent_type',
            'parent_image',
            'phone_number',
            'bal_users.email'
        );
        $fetchParentData = $this->_tblParentInfo->getParentdata($fetchDataArray, $parentId); // fetches filtered data
        return $fetchParentData; // returns array
    }

    /**
     * Function to add safenumber
     * 
     * @param
     *        	$parentId,$safeNumberTitle,$phoneNumber,$date
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function addSafeNumber($parentId, $safeNumberTitle, $phoneNumber, $code) {
        // data to be insert
        $addPhoneNumberData = array(
            'parent_id' => $parentId,
            'title' => $safeNumberTitle,
            'number' => $phoneNumber,
            'country_code' => $code
        );
        $addPhoneNumber = $this->_tblSafeNumber->addSafeNumber($addPhoneNumberData); // add data into DB
        return $addPhoneNumber; // returns result
    }

    /**
     * Function to get filtered safe number of paren
     * 
     * @param
     *        	$fetchDataArray,$parentId
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    /* public function getFilteredParentSafeNumber($fetchSafeFieldArray, $parentId) {
      // fetches filtered data
      $getParentSafeNumber = $this->_tblSafeNumber->fetchSafeNumberData ( $fetchSafeFieldArray, $parentId );
      return $getParentSafeNumber; // returns array
      } */

    /**
     * Function to check parent profile is exist or not
     * 
     * @param
     *        	userId
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function checkParentData($userId) {
        $result = $this->_tblParentInfo->isExistsParentData($userId); // checks parentprofile is exist or not
        return $result; // returnd result
    }

    /**
     * Function to add parent info into db
     * 
     * @param
     *        	$parentfName,$parentmName,$parentlName,$phoneNumber,$parentType,$userId,$date,$fileName
     * @author suman khatri on 20th November 2013
     * @return last insert id
     */
    public function addParentData($parentfName, $parentmName, $phoneNumber, $parentType, $userId, $date, $fileName) {
        // data to be insert
        $parentInfo = array(
            'first_name' => $parentfName,
            'middle_name' => $parentmName,
            'phone_number' => $phoneNumber,
            'parent_type' => $parentType,
            'user_id' => $userId,
            'created_date' => $date
        );
        if (!empty($fileName) && $fileName != null) {
            $parentInfo ['parent_image'] = $fileName;
        }
        $res = $this->_tblParentInfo->addParent($parentInfo); // insert record into db
        return $res; // return last insert id
    }

    /**
     * Function to add parent email info into db
     * 
     * @param
     *        	$userEmail,$res,$verificationCode,$date
     * @author suman khatri on 20th November 2013
     * @return last insert id
     */
    public function addParentEmail($userEmail, $res, $verificationCode, $date) {
        // data to be insert
        $emailData = array(
            'email' => $userEmail,
            'parent_id' => $res,
            'verification_code' => $verificationCode,
            'verified' => 'Y',
            'created_date' => $date
        );
        $addEmail = $this->_tblParentEmails->addEmailVerifyData($emailData); // insert record into DB
        return $addEmail; // return last insert id
    }

    /**
     * Function to get parent registration info
     * 
     * @param
     *        	$userId
     * @author suman khatri on 13th November 2013
     * @return ArrayObject
     */
    public function getParentRegistartionInfo($userId) {
        $userdetail = $this->_tblUser->fetchDetail($userId); // fetches parent registration info
        return $userdetail; // returns array
    }

    /**
     * Function to update parent info into db
     * 
     * @param
     *        	$parentfName,$parentmName,$parentlName,$phoneNumber,$parentType,$userId,$date,$fileName
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function updateParentData($parentfName, $parentmName, $displayName, $phoneNumber, $userId, $date, $imageFileName, $parType, $timezone) {
        // data to be update
        $updatedData = array(
            'first_name' => $parentfName,
            'middle_name' => $parentmName,
            'display_name' => $displayName,
            'phone_number' => $phoneNumber,
            'modified_date' => $date,
            'parent_image' => $imageFileName,
            'parent_type' => $parType,
            'timezone_id' => $timezone
        );
        
        $res = $this->_tblParentInfo->updateParent($updatedData, $userId); // updates record into db
        return $res; // return result
    }

    /**
     * Function to update parent registration info
     * 
     * @param
     *        	$newPasswordUpdate,$date,$userId
     * @author suman khatri on 13th November 2013
     * @return result
     */
    public function updateParentRegistartionInfo($newPasswordUpdate, $date, $userId, $userName) {
        // data to be update
        if (!empty($newPasswordUpdate) && $newPasswordUpdate != null) {
            $updatedData = array(
                'password' => $newPasswordUpdate,
                'modified_date' => $date
            );
        }
        if (!empty($userName) && $userName != null) {
            $updatedData = array(
                'user_name' => $userName,
                'modified_date' => $date
            );
        }
        $res = $this->_tblUser->updateUserInfo($updatedData, $userId); // updates parent registration info
        return $res; // returns $res
    }

    /**
     * Function to check existance of username
     * 
     * @param $userName, $userId        	
     * @author suman khatri on 13th November 2013
     * @return result
     */
    public function checkExistanceOfUsername($userName, $userId) {
        // creates object of model file ParentRegistratio
        $res = $this->_tblUser->isExistsUsernameusingId($userName, $userId); // check existance of username
        return $res; // returns $res
    }

    /**
     * Function to update parent image into db
     * 
     * @param
     *        	$parentfName,$parentmName,$parentlName,$phoneNumber,$parentType,$userId,$date,$fileName
     * @author suman khatri on 20th November 2013
     * @return result
     */
    public function updateParentImage($fileName, $userId, $date) {
        // data to be update
        $updatedData = array(
            'parent_image' => $fileName,
            'modified_date' => $date
        );
        $res = $this->_tblParentInfo->updateParent($updatedData, $userId); // updates record into db
        return $res; // return result
    }

    /**
     * Function to check parent Email-id already exist or not
     * 
     * @param
     *        	$emialid
     * @author Dharmendra Mishra on 19th November 2013
     * @return boolean true|false
     */
    public function checkParentEmailId($emailId) {
        /*
         * Call model function to check email id exist or not into bal_users tables
         */
        $checkUser = $this->_tblUser->isExistsEmail($emailId);
        /*
         * Call model function to check email id exist or not into bal_parent_emails tables
         */
        $checkEmail = $this->_tblParentEmails->isExistsEmail($emailId);
        /*
         * if condition to check where parent email id exist or not if yes then return true
         */
        if ($checkUser == true || $checkEmail == true) {
            return true;
        }
    }

    /**
     * Function to check parent Email-id already exist or not
     * 
     * @param
     *        	$emialid,$userId
     * @author Suman Khatri on 25th November 2013
     * @return boolean true|false
     */
    public function checkParentEmailIdUsingUserId($emailId, $userId) {
        /*
         * Call model function to check email id exist or not into bal_users tables
         */
        $checkEmail = $this->_tblUser->isExistsEmailUsingUserId($emailId, $userId);
        /*
         * Call model function to check email id exist or not into bal_parent_emails tables
         */
        /*
         * if condition to check where parent email id exist or not if yes then return true
         */
        if ($checkEmail == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to check parent User Name already exist or not
     * 
     * @param
     *        	$userName
     * @author Dharmendra Mishra on 19th November 2013
     * @return boolean true|false
     */
    public function checkParentUserName($userName) {
        /*
         * Call isExistsUserName function which are difine defined in model files
         */
        $checkUserName = $this->_tblUser->isExistsUsername($userName);
        return $checkUserName;
    }

    /**
     * Function to add parent
     * 
     * @param
     *        	array
     * @author Dharmendra Mishra on 19th November 2013
     * @return int last row inserted into.
     */
    public function addUserData($parentData) {
        /*
         * Call function to Add Parent Data into table
         */
        $result = $this->_tblUser->addUserData($parentData);
        return $result;
    }

    /**
     * Function to change password
     * 
     * @param
     *        	array
     * @author Dharmendra Mishra on 19th November 2013
     * @return int last row inserted into.
     */
    public function changeParentPassword($chnagePassRqstData) {
        return $this->_tblChangePassCode->addPassRequest($chnagePassRqstData);
    }

    /**
     * Function to check parent email verifyed or not
     * 
     * @param
     *        	verificationCode && ParentId
     * @author Dharmendra Mishra on 19th November 2013
     * @return boolean true|false
     */
    public function parentVerificationCode($verficationCode, $parId, $verifyEmail) {
        if (!empty($verifyEmail)) {
            return $this->_tblUser->verifyMail($parId, $verficationCode);
        } else {
            return $this->_tblUser->isverifiedEmail($verficationCode, $parId);
        }
    }

    /**
     * Function to check parent change password email verifyed or not
     * 
     * @param
     *        	verificationCode && ParentId
     * @author Dharmendra Mishra on 19th November 2013
     * @return boolean true|false
     */
    public function checkChangePasswordEmailVerify($verficationCode, $userId) {
        $fetchRequestData = $this->_tblChangePassCode->fetchRequestData($verficationCode, $userId);
        return $fetchRequestData;
    }

    /**
     * Function to check parent update change password
     * 
     * @param
     *        	verificationCode && ParentId
     * @author Dharmendra Mishra on 19th November 2013
     * @return boolean true|fals 
     */
    public function updateParentChangePasswordVerify($updateRequestData, $userId, $verficationCode) {
        return $this->_tblChangePassCode->updateRequestData($updateRequestData, $userId, $verficationCode);
    }

    /**
     * Function to update parent password
     * 
     * @param
     *        	array && ParentId
     * @author Dharmendra Mishra on 20th November 2013
     * @return boolean true|false
     */
    public function updateParentChangePassword($updateDataArray, $userId) {
        $chnagePassword = $this->_tblUser->updateUserInfo($updateDataArray, $userId);
        return $chnagePassword;
    }

    /**
     * Function to get page data
     * 
     * @param
     *        	string page title
     * @author Dharmendra Mishra on 20th November 2013
     * @return string
     */
    public function getPageData($pageTitle) {
        $getAboutUsData = $this->_tblCmsInfo->getPageData($pageTitle);
        return $getAboutUsData;
    }

    /**
     * Function to get Video data
     * 
     * @param
     *        	string page title
     * @author Dharmendra Mishra on 20th November 2013
     * @return string
     */
    public function getVideo() {
        $data = $this->_tblVideo->getVideo();
        return $data;
    }

    /**
     * Function to update parent email info into db
     * 
     * @param
     *        	$emailId,$verificationCode,$todayDate,$parId,$userId
     * @author suman khatri on 25th November 2013
     * @return last insert id
     */
    public function updateParentEmail($emailId, $verificationCode, $todayDate, $parId, $userId) {
        // data to be update
        $emailData = array(
            'email' => $emailId,
            'verification_code' => $verificationCode,
            'verified' => 'N',
            'modified_date' => $todayDate
        );
        $updateEmail = $this->_tblParentEmails->updateParentEmail($parId, $emailData); // update record into DB
        $updateData = array('email' => $emailId,
            'modified_date' => $todayDate);
        $updateuserInfo = $this->_tblUser->updateUserInfo($updateData, $userId);
        return $updateuserInfo; // return last insert id
    }

    /*     * ********
     * function for fetch images lists from db
     */

    public function getIamgesForFronEnd() {
        return $this->_tblimages->getAllImages();
    }

    /*     * *********8function to fetch all safenumbers 
     * @param parent id int
     * @return Array
     */

    public function getSafeNumbersList($parentId, $type) {

        $data = $this->_tblSafeNumber->fetchSafeNumberData($parentId, null);
        foreach ($data as &$value) {
            $value['code'] = $value['country_code'];
            $value['isparent'] = 0;
            unset($value['parent_id']);
            unset($value['country_code']);
            unset($value['created_date']);
        }

        return $data;
    }

    /*     * *************function for check safe number allready exist or not*************** */

    public function checkSafeNumber($safeNumber, $countryCode, $parentId, $safeNumberId) {
        return $this->_tblSafeNumber->existPhone($safeNumber, $countryCode, $parentId, $safeNumberId);
    }

    /*     * ********functions for check safe number title****************************** */
    /*     * *************function for check safe number allready exist or not*************** */

    public function checkSafeTitle($title, $parentId, $safeeditId) {
        return $this->_tblSafeNumber->existTitle($title, $parentId, $safeeditId);
    }

    /*     * ***********************function to fetch safe numbner list row************** */

    public function getSafeNumberListRow($safeNumberId) {
        return $this->_tblSafeNumber->fetchRowSafeNumber($safeNumberId);
    }

    /*     * ***
     * function for delete safenumbers
     * @param int |safenumber id
     * @return array
     */

    public function deleteSafeNumber($safeNumberId) {
        return $this->_tblSafeNumber->removeSafeNumber($safeNumberId);
    }

    public function updateAllSafeNumberes($parentId) {
        return $this->_tblSafeNumber->updateSafeNumberList($parentId);
    }

    public function removeSafeNumberLists($parentId) {
        return $this->_tblSafeNumber->removeSafeNumberLists($parentId);
    }

    /**
     * Function to update parent info into db
     * 
     * @param
     *        	$phoneNum, $parentType, $userId, $todayDate
     * @author suman khatri on 28th November 2013
     * @return result
     */
    public function updateParentTypeAndPhone($phoneNum, $parentType, $userId, $todayDate) {
        // data to be update
        $updatedData = array(
            'parent_type' => $parentType,
            'phone_number' => $phoneNum,
            'modified_date' => $todayDate
        );
        $res = $this->_tblParentInfo->updateParent($updatedData, $userId); // updates record into db
        return $res; // return result
    }

    /*     * ***********function for checking numberes is already exist or not as a parent ************ */

    public function checkPhoneNumberRecord($safeNumber, $parId) {
        return $this->_tblParentInfo->checkPhoneExist($safeNumber, $parId);
    }

    /*     * *********8function to fetch all safenumbers
     * @param parent id int
     * @return Array
     */

    public function getparentNumbersList($parentId) {
        $data[] = $this->_tblParentInfo->getParentdata('', $parentId);
        return $data;
    }

    /**
     * @desc Function to check safe number is exist with any other record of parent
     * @param $safeNumber,$safeNumberId,$parentId
     * @author suman khatri on 18th November 2013
     * @return result
     */
    public function checkNumberExistInSafeNumber($safeNumber, $safeNumberId, $parentId) {
        //creates object of model file SafeNumbers
        $tblSafeNumber = new Application_Model_DbTable_SafeNumbers ();
        //checking whether phone number is exist or not in DB with any other record
        if (!empty($safeNumberId) && $safeNumberId != null) {
            $checkPhone = $tblSafeNumber->existPhoneById($safeNumber, $safeNumberId);
        }
        //checking whether phone number is exist or not in DB with parent's safe number
        if (!empty($parentId) && $parentId != null) {
            $checkPhone = $tblSafeNumber->existPhone($safeNumber, '', $parentId, '');
        }
        return $checkPhone; // return result
    }

    public function getParentDataUsingUniqueId($uniqueId) {
        $parent = new Application_Model_DbTable_ParentInfo();
        return $parent->getParentDataUsingUniqueId($uniqueId);
    }

    public function getTotalFinnyParent() {
        $data = $this->_tblParentInfo->getTotalFinnyParent();
        $totalParentEmailVerified = (int) $data['totalParentEmailVerified'];
        $totalParentEmailNottVerified = (int) $data['totalParentEmailNottVerified'];
        if ((!empty($totalParentEmailNottVerified) && $totalParentEmailNottVerified != 0) ||
                (!empty($totalParentEmailVerified) && $totalParentEmailVerified != 0)) {
            $cols = array(
                'cols' => array(
                    array('id' => '', 'label' => 'Parents', 'pattern' => '', 'type' => 'string'),
                    array('id' => '', 'label' => 'Count', 'pattern' => '', 'type' => 'number')
                ),
                'rows' => array(
                    array('c' => array(
                            array('v' => 'Verified Parents', 'f' => null),
                            array('v' => $totalParentEmailVerified, 'f' => null))
                    ),
                    array('c' => array(
                            array('v' => 'Non Verified Parents', 'f' => null),
                            array('v' => $totalParentEmailNottVerified, 'f' => null))
                    )
                )
            );
            return json_encode($cols);
        } else {
            return false;
        }
    }

    /**
     * Function to get count of parent having no child
     * 
     * @param
     *        	NILL
     * @author suman khatri on 16th April 2014
     * @return ArrayObject
     */
    public function getAllParentwithNoChild() {
        $allData = $this->_tblParentInfo->getAllParentwithNoChild();
        return $allData;
    }

    /**
     * Function to get all previous unused change password request
     * @param $userId,$createdDate
     * @author Suman Khatri on 20 August 2014
     * @return ArrayIterator
     */
    public function getAllPreviousRequest($userId, $createdDate) {
        return $this->_tblChangePassCode->getAllPreviousRequest($userId, $createdDate);
    }

    /**
     * Function to ecpire previous unused change password request
     * @param $requestData,$requestId
     * @author Suman Khatri on 20 August 2014
     * @return ArrayIterator
     */
    public function expirePreviousRequests($requestData, $requestId) {
        return $this->_tblChangePassCode->expirePreviousRequests($requestData, $requestId);
    }

    /**
     * Function to get parentId using accessToken and deviceId
     * @param $accessToken,$deviceId,$returnType
     * @author Suman Khatri on 9th October 2014
     * @return parentId/ArrayObject
     */
    public function getParentIdUsingAccessTokenAndDeviceId($accessToken, $deviceId, $returnType) {
        //getting parentId and return it
        return $this->_parentDeviceRelation->getParentIdUsingAccessTokenAndDeviceId($accessToken, $deviceId, $returnType);
    }

    /**
     * Function to get all registered device unfo using ParentId
     * @param $parentId
     * @author Suman Khatri on 12th October 2014
     * @return ArrayIterator
     */
    public function getAllregisteredDeviceInfoOfParent($parentId, $deviceId = null) {
        //getting all registered device info and returns
        return $this->_parentDeviceRelation->getAllregisteredDeviceInfoOfParent($parentId, $deviceId);
    }

    /**
     * Function to check parent type and number is set or not
     * @param $userId
     * @author Suman Khatri on 14th October 2014
     * @return true / flase
     */
    public function checkParentTypeIsSetOrNot($userId) {
        // fetches parent detail
        $parentDetail = $this->_tblParentInfo->fetchUser($userId);
        if (!empty($parentDetail)) {
            $parId = $parentDetail->parent_id; // gets parent id from parent detail
        }
        //fetch filtered parent data
        $fetchParentData = $this->getFilteredParentData($parId); // 
        if (empty($fetchParentData ['parent_type']) || empty($fetchParentData ['phone_number'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function to check parent email is verified or not
     * @param string $emailId emai id
     * @param string $passWord password
     * 
     * @author suman khatri on October 22 2014
     * @return true / flase
     */
    public function checkUserEmailVerification($emailId, $passWord) {
        return $this->_tblUser->checkUserEmailVerification($emailId, $passWord);
    }

    /**
     * send push to all devices of parent
     */
    public function sendPushToAllDevices($parentId, $data, $deviceId = NULL) {
        $devicesInfo = $this->getAllregisteredDeviceInfoOfParent($parentId, $deviceId);
        foreach ($devicesInfo as $deviceData) {
            $gcm = new My_GCM ();
            $res = $gcm->send_notification(array($deviceData['registered_id']), $data);
        }
        return TRUE;
    }

    public function addParentNotificationForTrophy($subjectId, $points, $streak, $userId, $trophytitle, $deviceId, $childName, $childId) {
        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        if (!empty($subjectId)) {
            $childSubject = new Application_Model_DbTable_ChildSubject();
            $subjectInfo = $childSubject->getSubjectDataOnSubjectId($subjectId);
            $subjectName = $subjectInfo['subject_name'];

            if (strtolower($subjectName) == 'math.content') {
                $subjectName = 'math';
            }

            if (!empty($points)) {
                $notiData = number_format($points) . " coins";
            } else if (!empty($streak)) {
                $notiData = number_format($streak) . " streak";
            }
            $subjectText = "";
            if(isset($subjectName) && $subjectName != "" && $subjectName != NULL) {
                $subjectText = "in " . $subjectName . " ";
            }
            $notification = "gained " . $trophytitle . " Trophy " . $subjectText . "for " . $notiData;
        } else {
            if (!empty($points)) {
                $notiData = number_format($points) . " coins";
            } else if (!empty($streak)) {
                $notiData = number_format($streak) . " streak";
            }
            $notification = "gained " . $trophytitle . " Trophy for " . $notiData;
        }

        $insertNotifdata = array(
            'user_id' => $userId,
            'notification_type' => 'TROPHY',
            'description' => $notification,
            'seen_by_user' => 'N',
            'deleted' => 'N',
            'child_device_id' => $deviceId,
            'childe_name' => $childName,
            'child_id' => $childId,
            'created_date' => date('Y-m-d H:i:s')
        );
        $resnotifis = $tblParentNofic->AddParentNotification($insertNotifdata);
    }

    public function updateUserData($updatedData, $userId) {
        $res = $this->_tblUser->updateUserInfo($updatedData, $userId);
        return $res;
    }

    /**
     * @desc Function to delete parent
     * @param int  childId
     * @author Abhinav Bhardwaj on January 03 2016
     * @return int response
     */
    public function deleteParent($parentId, $parentEmail = null){
          $response         =   $this->_tblUser->deleteData($parentId, $parentEmail); 
          return $response;
    } 
    
    /**
     * @desc Function to add deleted parent in table
     * @param string $parentEmail
     * @author Shailendra on Feb 16, 2016
     * @return int response
     */
    public function addDeleteParent($parentEmailId){
        $tblParentDeleted = new Application_Model_DbTable_ParentsDeleted();
        return $tblParentDeleted->insert(array('parent_email' => $parentEmailId));
    }
}