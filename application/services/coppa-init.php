<?php

include realpath(dirname(__FILE__) . '/config.php');
try {
    $dbCoppaReminder = new Application_Model_Child();
    $childList = $dbCoppaReminder->getChildListToRemind();
    $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
    foreach ($childList as $child) {

        
        $dbCoppaReminder->incrementReminderCount($child->child_id);
        $objCoppa = new Application_Service_Coppa($child->parent_id, $child->child_id);
        
        $insertNotifdata = array(
            'user_id' => $child->parent_id,
            'notification_type' => 'COPPA',
            'description' => 'consent is revoked',
            'seen_by_user' => 'N',
            'deleted' => 'N',
            'child_device_id' => 0,
            'childe_name' => $child->child_name,
            'child_id' => $child->child_id,
            'created_date' => date('Y-m-d H:i:s')
        );
        $resnotifis = $tblParentNofic->AddParentNotification($insertNotifdata);
        
        $objCoppa->send();
    }
} catch (Exception $exc) {
    mail('ashwini.agarwal@a3logics.in', $exc->getMessage(), $exc->getTraceAsString());
    exit;
}
