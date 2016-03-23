<?php

/**
 * This class is plugin class for check authentication and access all the API's.
 *
 * @author Ashwini Agarwal
 *
 */
class My_Controller_Plugin_ApiAuthentication extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        if ($module != 'mobile') {
            return TRUE;
        }

        $apiKey = $request->getHeader('Api-Key');
        $hashKey = $request->getHeader('Hash-Key');

        if (API_KEY !== $apiKey) {
            $request->setControllerName('error');
            $request->setActionName('unauthorise');
            return TRUE;
        }

        $passHash = password_hash($apiKey, PASSWORD_BCRYPT, array('salt' => API_SALT));

        if ($hashKey !== $passHash) {
            $request->setControllerName('error');
            $request->setActionName('unauthorise');
            return TRUE;
        }

        return TRUE;
    }

}
