<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initAppAutoload()
    {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/default/controllers/helpers');

        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory(array(
            'default' => APPLICATION_PATH . '/modules/default/controllers',
            'admin' => APPLICATION_PATH . '/modules/admin/controllers', 
        ));

        $request = new Zend_Controller_Request_Http();
        $router = $front->getRouter();
        $router->route($request);
        $front->setRequest($request);
        $module = $request->getModuleName();

        Zend_Layout::startMvc(array(
            'layoutPath' => APPLICATION_PATH . '/modules/' . $module . '/layouts/scripts'
        ));

        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerModule($module);
        $front->registerPlugin($plugin);
    }

    function _initRoutersSetup()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', APPLICATION_ENV);

        /* @var $router Zend_Controller_Router_Rewrite */
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addConfig($config, 'routes');
    }

    function _initViewHelpers()
    {
        $view = new Zend_View();
        $view->doctype('XHTML1_TRANSITIONAL');
        $view->setEncoding('UTF-8');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendHttpEquiv('X-UA-Compatible', 'IE=edge');
        $view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1');

        $view->headTitle("CRM")->setSeparator(' | ');
    }

    protected function _initSetConstants()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/constants.ini', APPLICATION_ENV);

        $constant = $config->get('constant');
        foreach ($constant as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }

    protected function _initSetupDB()
    {
        $this->bootstrap('db');
    }

    protected function _initSetupAuth()
    {
        $module = $front = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('finny' . APPLICATION_ENV . $module));
    }

    protected function _initSetupSession()
    {
        $module = $front = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();

        Zend_Session::setOptions(array('name' => 'finny.session' . APPLICATION_ENV . $module));

        $flash = new Zend_Controller_Action_Helper_FlashMessenger();
        $flash->setNamespace('finny.flash' . APPLICATION_ENV . $module);
    }

    protected function _initTimezone()
    {

        $user_id = NULL;
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (Zend_Auth::getInstance()->hasIdentity()) {

            $user = Zend_Auth::getInstance()->getIdentity();
            $user_id = $user->user_id;
        } elseif ($request->getModuleName() == 'mobile') {

            //get device data if exist
            $objUserAuth = new Application_Service_User_AuthDevice();
            $user_id = $objUserAuth->getUserId($request->getParam('device_key'), $request->getParam('access_token'));
        }

        $objTimezone = new Application_Model_Timezone();
        $timezone = $objTimezone->getUserTimezone($user_id);
        date_default_timezone_set($timezone['value_php']);

        $time = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone($timezone['value_php']));
        $timezoneOffset = $time->format('P');

        $db = Zend_Db_Table::getDefaultAdapter();
        $query = $db->query('SET SESSION time_zone = ?', $timezoneOffset);
        $query->execute();
    }

}