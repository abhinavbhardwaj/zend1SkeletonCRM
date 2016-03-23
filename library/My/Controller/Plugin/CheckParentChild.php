<?php
/**
 * This class is plugin class for check parent and child if exists in system
 * for web services. It will be called before every web service.
 * @author Shailendra Chauhan
 */
class My_Controller_Plugin_CheckParentChild extends Zend_Controller_Plugin_Abstract
{

    /**
     * function preDispatch to check Parent/ Child exists
     * @param Object $request Current request which is running
     * @return json
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        $mName = strtolower($request->getModuleName());
        $deviceKey = $request->getParam('device_key');
        $accessToken = $request->getParam('access_token');
        $childId = $request->getParam('childId');

        // If not mobile module return
        if ($mName != 'mobile') {
            return;
        }
        
        // If empty device id and access token return
        if (empty($deviceKey) || empty($accessToken)) {
            return;
        }
        
        $returnData = array();
        // Check parent exists
        $dbDevice = new Application_Model_DeviceInfo();
        $parentInfo = $dbDevice->verifyDeviceKeyAndAccessToken($deviceKey, $accessToken);
        
        if (empty($parentInfo['parentId'])) {
            $returnData["status_code"] = NO_PARENT;
            $returnData["message"] = "Parent not exists";
            echo Zend_Json::encode($returnData);
            die();
        }
        
        // Check child exists
        if (! empty($childId)) {
            
            $dbChild = new Application_Model_Child();
            $childInfo = $dbChild->getChildBasicInfo($childId);
            
            if (empty($childInfo)) {
                $returnData["status_code"] = NO_CHILD;
                $returnData["message"] = "Child not exists";
                echo Zend_Json::encode($returnData);
                die();
            }
        }
        return ;
    }
}