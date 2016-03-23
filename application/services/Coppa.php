<?php

/**
 * General COPPA oprations
 * 
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class Application_Service_Coppa
{

    /**
     *
     * @var Application_Model_Parents
     */
    protected $_dbParent;

    /**
     *
     * @var Application_Model_Child
     */
    protected $_dbChild;

    /**
     *
     * @var Array
     */
    public $parentData;

    /**
     *
     * @var Int
     */
    public $parentId;

    /**
     *
     * @var Int
     */
    public $childId;

    /**
     *
     * @param Int $parentId
     * @param Int $childId
     * @throws Exception
     */
    public function __construct($parentId, $childId)
    {
        if (empty($parentId) || empty($childId)) {
            throw new Zend_Exception('Parent id and child id cannot be null');
        }

        $this->parentId = $parentId;
        $this->childId = $childId;
        $this->_dbParent = new Application_Model_Parents();
        $this->_dbChild = new Application_Model_Child();
        $this->parentData = $this->_dbParent->getFilteredParentData($parentId);
    }

    /**
     * Send COPPA consent
     * @return boolean
     */
    public function send()
    {
        /* get data */
        $childInfo = $this->_dbChild->getChildBasicInfo($this->childId);
        $html = $this->getHtmlEmail(); 

        /* send email */
        $mail = new My_Mail();
        //$mail->setSubject('Please Accept COPPA consent for '. $childInfo['name']);
        $mail->setSubject('Child Confirmation');
        $mail->setBodyHtml($html);
        $mail->addTo($this->parentData['email']);
        $mail->setReplyTo(COPPA_MAIL_REPLY_TO, EMAIL_FROM_NAME );
        $mail->setFrom(COPPA_MAIL_FROM, EMAIL_FROM_NAME );
        /* end send email */

        return $mail->send();
    }

    /**
     * Send Mail after accepting COPPA consent
     * @return boolean
     */
    public function coppaConfirmationMail()
    {
        /* get data */
       // $childInfo = $this->_dbChild->getChildBasicInfo($this->childId);
        $html = $this->getConfirmationHtmlEmail(); 

        /* send email */
        $mail = new My_Mail();
        $mail->setSubject('Account Confirmation Complete');
        $mail->setBodyHtml($html);
        $mail->addTo($this->parentData['email']);
        $mail->setReplyTo(COPPA_MAIL_REPLY_TO, EMAIL_FROM_NAME );
        $mail->setFrom(COPPA_MAIL_FROM, EMAIL_FROM_NAME );
        /* end send email */

        return $mail->send();
    }
    /**
     * Accept Coppa Consent
     * @return boolean
     */
    public function accept()
    {
        $this->_dbChild->addOrUpdateChildBasicInfo($this->childId, array(
            'coppa_accepted_once'   => TRUE,
            'coppa_accepted' => TRUE,
            'track_location' => TRUE
        ));
        $this->sendPushNotification();

        $this->_dbChild->removeCoppaReminder($this->childId);
        return TRUE;
    }

    /**
     * Revoke Coppa Consent
     * @return boolean
     */
    public function revoke()
    {
        $childInfoArray = $this->_dbChild->getChildInfoArray($this->childId);
        $this->_dbChild->addOrUpdateChildBasicInfo($this->childId, array(
            'coppa_accepted' => FALSE,
            'track_location' => FALSE,
            'image' => NULL,
            'dob' => NULL,
            'gender' => NULL
        ));

        if (!empty($childInfoArray['image'])) {
            if (file_exists(APPLICATION_PATH . '/../public/uploads/child/' . $childInfoArray['image'])) {
                unlink(APPLICATION_PATH . '/../public/uploads/child/' . $childInfoArray['image']);
            }
            if (file_exists(APPLICATION_PATH . '/../public/uploads/child/thumb/' . $childInfoArray['image'])) {
                unlink(APPLICATION_PATH . '/../public/uploads/child/thumb/' . $childInfoArray['image']);
            }
        }

        $this->sendPushNotification();

        $this->_dbChild->resetCoppaReminder($this->childId);
        return TRUE;
    }

    /**
     * Check if coppa is already accepted by Kid
     * @return boolean
     */
    public function isCoppaAccepted()
    {
        return $this->_dbChild->isCoppaAccepted($this->childId);
    }

    public function isValidReminderToken($token)
    {
        return $this->_dbChild->isValidReminderToken($this->childId, $token);
    }

    /**
     * Send Push Notification to all devices
     * @param boolean $accepted
     * @return boolean
     */
    public function sendPushNotification()
    {
        $childInfoArray = $this->_dbChild->getChildInfoArray($this->childId);
        $this->_dbParent->sendPushToAllDevices($this->parentId, array(
            'process_code' => 'coppa',
            'childId' => $this->childId,
            'message' => 'coppa updated',
            'data' => array(
                'status_code' => STATUS_SUCCESS,
                'children_list' => $childInfoArray
            )
        ));

        return TRUE;
    }

    /**
     * Get COPPA consent html
     * @return string
     */
    public function getHtmlEmail()
    {
        $token = $this->_dbChild->createCoppaReminderToken($this->childId);

        $url = SERVER_URL . HOST_NAME . '/coppa-consent/accept/id/' . $this->childId . '/token/' . $token;

        $childInfo = $this->_dbChild->getChildBasicInfo($this->childId);

        $parentName = $this->parentData['first_name'] . " " . $this->parentData['last_name'];
        $parentImage = '/images/parent_small.png';
        if (!empty($this->parentData['parent_image'])) {
            $parentImage = '/uploads/parents/thumbs/' . $this->parentData['parent_image'];
        }

        /* get consent email content */
        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
        $template->assign('child_name', $childInfo['name']);
        $template->assign('name', $parentName);
        $template->assign('profile_picture', $parentImage);
        $template->assign('link', $url);

        return $template->render('coppa-consent.phtml');
    }
    
   /**
     * Get COPPA consent html
     * @return string
     */
    public function getConfirmationHtmlEmail()
    {
        $url = SERVER_URL . HOST_NAME . '/myaccount' ;

        $childInfo = $this->_dbChild->getChildBasicInfo($this->childId);

        $parentName = $this->parentData['first_name'] . " " . $this->parentData['last_name'];
        $parentImage = '/images/parent_small.png';
        if (!empty($this->parentData['parent_image'])) {
            $parentImage = '/uploads/parents/thumbs/' . $this->parentData['parent_image'];
        }

        /* get consent email content */
        $template = new Zend_View();
        $template->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
        $template->assign('child_name', $childInfo['name']);
        $template->assign('name', $parentName);
        $template->assign('profile_picture', $parentImage);
        $template->assign('link', $url);

        return $template->render('coppa-consent-confirmation.phtml');
    }

    /**
     * Accept Coppa Consent
     * @return boolean
     */
    public function setNotRequired()
    {
        $this->_dbChild->addOrUpdateChildBasicInfo($this->childId, array(
            'coppa_required' => FALSE,
            'track_location' => TRUE
        ));
        $this->sendPushNotification();

        $this->_dbChild->removeCoppaReminder($this->childId);
        return TRUE;
    }

}
