<?php

/**
 * This class is plugin class for check authentication and access in whole application.
 * This plugin check that user is logged in or not.
 * If user is not logged in redirect it to login page.
 *
 * @notice Please dont chage in this class it is accessed in whole application
 * @author Sunil Khanchandani
 *
 */
class My_Controller_Plugin_CheckAuthentication extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $cName = strtolower($request->getControllerName());
        $aName = strtolower($request->getActionName());
        $mName = strtolower($request->getModuleName());

        if ($mName == 'default') {
            $urlHelper = new Zend_View_Helper_Url();
            if (in_array($cName, array('index', 'page', 'contact', 'auth', 'error', 'question', 'landing'))) {
                return TRUE;
            }

            if (!Zend_Auth::getInstance()->hasIdentity()) {
                /**/
                $visitor = 0;
                $rememberCookie = $this->getRequest()->getCookie(md5('remember_me'));
                if (isset($rememberCookie) && $rememberCookie != '') {
                    $userRememberMeTokenObj = new Application_Model_UserRememberMeToken();
                    $userData = $userRememberMeTokenObj->authenticateToken($rememberCookie);

                    if (!empty($userData)) {
                        $auth = Zend_Auth::getInstance();
                        $auth->getStorage()->write($userData);
                    } else {
                        $visitor = 1;
                    }
                } else {
                    $visitor = 1;
                }

                if ($visitor) {
                    //$request->setControllerName('auth');
                    //$request->setActionName('login');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                    $request->setParam('redirecturi', urlencode($urlHelper->url()));
                    return TRUE;
                }
            }

            $userInfo = Zend_Auth::getInstance()->getIdentity();

            $dbParent = new Application_Model_Parents();
            $parId = (int) $dbParent->getParentData($userInfo->user_id, 'parId');

            if (empty($parId) && ($parId !== 0)) {
                $request->setControllerName('auth');
                $request->setActionName('logout');
                return TRUE;
            }

            $dbChild = new Application_Model_Child();
            $childs = $dbChild->getAllChildOfParent($parId);
            if (count($childs) == 0) {
                if (($cName == 'child') || ($cName == 'myaccount') || ($cName == 'notifications')) {
                    return TRUE;
                }
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $redirector->gotoUrl('child/add');
                exit;

                /* $request->setControllerName('child');
                  $request->setActionName('index');
                  return TRUE; */
            }
        } else if ($mName == 'admin') {
            $adminInfoSession = new Zend_Session_Namespace('adminInfo');
            if (!isset($adminInfoSession->adminData) && !in_array($aName, array('forgotpassword', 'resetpassword'))) {
                $request->setModuleName('admin')
                        ->setControllerName('login')
                        ->setActionName('login')
                        ->setDispatched(true);
            } elseif (in_array($aName, array('forgotpassword', 'resetpassword'))) {
                return true;
            } else {
                $controller = $this->getRequest()->getControllerName();

                $access = array('index', 'questions', 'login', 'admin');

                $restrictedactions = array('deletecategory');
                if ($adminInfoSession->adminData->type == 2) {
                    if (!in_array($controller, $access)) {
                        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                        $redirector->gotoUrl('admin/index/denied');
                    } else {
                        $action = $this->getRequest()->getActionName();
                        if (in_array($action, $restrictedactions)) {

                            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                            $redirector->gotoUrl('admin/index/denied');
                        }
                    }
                }
            }
        }
    }

}