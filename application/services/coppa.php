<?php

include realpath(dirname(__FILE__) . '/config.php');
try {
    $dbCoppaReminder = new Application_Model_Child();
    $childList = $dbCoppaReminder->getChildListToRemind();
    foreach ($childList as $child) {

        if (!empty($child->child_dob) && (time() > strtotime('+13 years', strtotime($child->child_dob)))) {
            $objCoppa = new Application_Service_Coppa($child->parent_id, $child->child_id);
            $objCoppa->setNotRequired();
            continue;
        }

        if ($child->coppa_accepted) {
            continue;
        }

        if ($child->reminder_count >= 3) {
            continue;
        }

        if (strtotime($child->last_reminder) > (time() - 3 * 24 * 60 * 60)) {
            continue;
        }

        $dbCoppaReminder->incrementReminderCount($child->child_id);
        $objCoppa = new Application_Service_Coppa($child->parent_id, $child->child_id);
        $objCoppa->send();
    }
} catch (Exception $exc) {
    mail('ashwini.agarwal@a3logics.in', $exc->getMessage(), $exc->getTraceAsString());
    exit;
}
