<?php

/**
 * Function to create ramdom password for users
 * @param $minlength, $maxlength, $uselower, $usespecial, $usenumbers
 */
function generatePassword($minlength, $maxlength, $uselower, $usespecial, $usenumbers) {
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($uselower)
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    if ($usenumbers)
        $charset .= "0123456789";
    if ($usespecial)
        $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
    if ($minlength > $maxlength)
        $length = mt_rand($maxlength, $minlength);
    else
        $length = mt_rand($minlength, $maxlength);
    for ($i = 0; $i < $length; $i++)
        $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
    return $key;
}

/* * ************Block for validation of different type of fileds******************************* */

/*
 * Validate Email
 * @param Email address
 */

function validateEmail($email) {
    $emailVal = new Zend_Validate_EmailAddress();
    if (!($emailVal->isValid($email))) {
        return false;
    } else {
        return true;
    }
}

/*
 * Validate name
 * @param Name
 */

function validateName($name) {
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $name)) {
        return false;
    } else {
        return true;
    }
}

/*
 * Validate phone number
 * @param phone number
 */

function validatePhone($phone) {
    if (!preg_match('/^[0-9]+$/', $phone)) {
        return false;
    } else {
        return true;
    }
}

/* * *************validate phone new********** */

function validatePhoneNew($phone) {
    $special = false;
    if (strrpos($phone, '\(')) {
        $special = true;
    } else if (strrpos($phone, '\)')) {
        $special = true;
    } else if (strrpos($phone, '\-')) {
        $special = true;
    }
    if ($special) {
        $reg = '/^\([2-9]{1}[0-9]{2}\)\s[2-9]{1}[0-9]{2}\-[0-9]{4}$/';
        if (!preg_match($reg, $phone)) {
            return false;
        } else {
            return true;
        }
    } else {
        $reg = '/^[2-9]{1}[0-9]{2}[2-9]{1}[0-9]{2}[0-9]{4}$/';
        if (!preg_match($reg, $phone)) {
            return false;
        } else {
            return true;
        }
    }
}

/*
 * Validate not null for field
 * @param fieldname
 */

function validateNotNull($fieldName) {
    $nullVal = new Zend_Validate_NotEmpty();
    if (!($nullVal->isValid($fieldName))) {
        return false;
    } else {
        return true;
    }
}

/*
 * Validate not null for field
 * @param fieldname
 */

function validateNoSpace($fieldName) {     
    if (preg_match('/\s/',$fieldName)) {
        return false;
    } else {
        return true;
    }
}


/*
 * validate max length
 * @param field, maxlength
 */

function validateMaxLength($field, $maxLegth) {
    if (strlen($field) > $maxLegth) {
        return false;
    } else {
        return true;
    }
}

/*
 * validate min length
 * @param field, minlength
 */

function validateMinLength($field, $minLegth) {
    if (strlen($field) < $minLegth) {
        return false;
    } else {
        return true;
    }
}

/*
 * Validate Username
 * @param Username
 */

/* function validateUserName($useName)
  {
  if(!preg_match('/^[a-zA-Z0-9\s]+$/',$useName)) {
  return false;
  }else{
  return true;
  }
  } */

/*
 * Validate fileds with particular regex
 * @param field value
 * @param regex
 */

function validateFieldWithRegex($fieldVal, $regex) {
    if (!preg_match($regex, $fieldVal)) {
        return false;
    } else {
        return true;
    }
}

/* * **********Block to calculate age******** */

function calculateAge($dob) {
    /* $today = new DateTime();
      $birthdate = new DateTime($dob);
      $interval = $today->diff($birthdate);
      $age = $interval->format('%y years'); */
    return floor((time() - strtotime($dob)) / 31556926);
    //return $age;
}

function filterHtmlEntites($data) {
    $htmlEntityFilter = new Zend_Filter_HtmlEntities();
    $filterData = $htmlEntityFilter->filter($data);
    return $filterData;
}

function validateGrade($grade) {
    if (!preg_match('/^[0-9\-]+$/', $grade)) {
        return false;
    } else {
        return true;
    }
}

function validateDomain($domain) {
    if (!preg_match('/^[a-zA-Z]+$/', $domain)) {
        return false;
    } else {
        return true;
    }
}

function validateDomainLength($domain) {
    if (strlen($domain) > 50) {
        return false;
    } else {
        return true;
    }
}

function validateSubTopicLength($subtopic) {
    if (strlen($subtopic) > 4) {
        return false;
    } else {
        return true;
    }
}

function validateSubTopicFirst($subTopic1) {
    if (!preg_match('/^[a-zA-Z]+$/', $subTopic1)) {
        return false;
    } else {
        return true;
    }
}

function validateSubTopicSecond($subTopic2) {
    if (!preg_match('/^[a-zA-Z0-9&]+$/', $subTopic2)) {
        return false;
    } else {
        return true;
    }
}

function validateSubTopic($subTopic) {
    if (!preg_match('/^[a-zA-Z0-9]+$/', $subTopic)) {
        return false;
    } else {
        return true;
    }
}

function validateSubTopicLiteracyCCRA($subTopic) {
    if (!preg_match('/^[0-9]+$/', $subTopic)) {
        return false;
    } else {
        return true;
    }
}

function validateFrameWork($framework) {
    if (!preg_match('/^[a-zA-Z0-9]+$/', $framework)) {
        return false;
    } else {
        return true;
    }
}

/* * *********************function for return week of goal
 * according to child registered in which week of day
 * @param int week of day
 * $return int value
 *
 */

function weekOfGoalValue($dayOfWeek, $goalPoints) {
    $week = 7;
    $goalPoints = ($goalPoints / $week) * ($week - $dayOfWeek);
    return intval($goalPoints);
}

/* * *********************function for return end week of goal
 * according to child registered in which week of day
 * @param int week of day
 * $return date
 *
 */

function endDateWeekGoalVal($dayOfWeek) {
    $week = 6;
    $nextday = $week - $dayOfWeek;
    return date('Y-m-d 23:59:59', strtotime("+$nextday days"));
}

function endDateVal($dayOfWeek, $date) {
    $week = 6;
    $nextday = $week - $dayOfWeek;
    return date('Y-m-d 23:59:59', strtotime("+$nextday days", strtotime($date)));
}

/* * *********************function for return last end week of goal
 * according to child registered in which week of day
 * @param int week of day
 * $return date
 *
 */

function endDatelastWeekGoal($weekDay) {
    return date('Y-m-d 23:59:59', strtotime("-7 days", strtotime(endDateWeekGoalVal($weekDay))));
}

/* * *********************function for return last 4 week of goal date
 * according to child registered in which week of day
 * @param int week of day
 * $return date
 *
 */

function endDatelastFourWeekGoal($weekDay) {
    return date('Y-m-d 00:00:01', strtotime("+1 day", strtotime("-4 week", strtotime(endDatelastWeekGoal($weekDay)))));
}

function getStartDateForWeek($lastFourWeekStartDate, $i) {
    $days = 7 * $i;
    return date('Y-m-d 00:00:01', strtotime("+$days days", strtotime($lastFourWeekStartDate)));
}

function getendDateForWeek($startDate) {
    $days = 6;
    return date('Y-m-d 23:59:59', strtotime("+$days days", strtotime($startDate)));
}

function endDateForlastWeekGoal($weekDay) {
    return date('Y-m-d 00:00:01', strtotime("+1 day", strtotime("-1 week", strtotime(endDatelastWeekGoal($weekDay)))));
}

function ago($dateTime) {
    /* For change date according to time zone */
    // If you want time difference according to timezone
    // set default time zone according to php server
    //  date_default_timezone_set('UTC');


    $difference = time() - strtotime($dateTime);
    $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'years', 'decade');
    $lengths = array('60', '60', '24', '7', '4.35', '12', '10');

    for ($j = 0; $difference >= $lengths[$j]; $j++)
        $difference /= $lengths[$j];

    $difference = round($difference);
    if ($difference != 1)
        $periods[$j] .= "s";
    if ($difference < 0) {
        $difference = '0';
    }
    return "$difference $periods[$j] ago";
}

function currentWeekDate($dayofweek) {
    $week = 6;
    $nextday = $week - $dayofweek;
    $lstart = $dayofweek + 7;
    $lend = $lstart - 6;

    $endDate = date('Y-m-d 23:59:59', strtotime("+$nextday days"));
    $startDate = date('Y-m-d 00:00:01', strtotime("-$dayofweek days"));
    $lastweekS = date('Y-m-d 00:00:01', strtotime("-$lstart days"));
    $lastweekE = date('Y-m-d 23:59:59', strtotime("-$lend days"));
    $dateRange = array(
        'start_date' => $startDate,
        'end_date' => $endDate,
        'last_satart' => $lastweekS,
        'last_end' => $lastweekE,
        'lastm_start' => date("Y-m-d 00:00:01", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))),
        'lastm_end' => date("Y-m-d 23:59:59", mktime(0, 0, 0, date("m"), 0, date("Y"))),
    );
    return $dateRange;
}

/* * **********************format date time************************** */

function formatDate($date, $caseType = null) {
    switch ($caseType) {
        case 1:
            return date('Y-m-d H:i:s', strtotime($date));
            break;
        default:
            return date('M j, Y H:i:s', strtotime($date));
            break;
    }
}

/* * *********************get current page url*********************** */

function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/*
 * Function for Return today date using zend date
 * return today date  
 */

function todayZendDate() {
    $date = new Zend_Date();
    $todayDateObj = new Zend_Date(strtotime(Zend_Date::now()));
    //$todayDate	= $todayDateObj->toString("YYYY-MM-dd H:m:s");
    $todayDate = date("Y-m-d H:i:s");
    return $todayDate;
}

/*
 * function for generate expiry date
 * @param number of day
 * return date
 */

function expiryData($day) {
    return date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +$day days"));
}

/* * **********************format date time for notification list************************** */

function formatDateForNoti($date) {
    $date1 = $date;
    if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate()))) {
        $date = 'Today';
    } elseif (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate() . "-1 day"))) {
        $date = 'Yesterday';
    } else {
        $date = date('M j, Y', strtotime($date));
    }
    $time = date('H:i:s', strtotime($date1));
    $dateTime = $date . " at " . $time;
    return $dateTime;
}

/* * ****function for assigned values into layouts valirables*** */

function assignedVariablesForLayout($varName, $Value) {
    Zend_Layout::getMvcInstance()->assign($varName, $Value);
}

/* * **************function for generate unique code*********************** */

function unqueCodeGenerate() {
    $tblChildDevice = new Application_Model_DbTable_ChildDeviceInfo ();
    $unCode = substr(number_format(time() * rand(), 0, '', ''), 0, 9);
    $unCodeData = $tblChildDevice->fetchRow("unique_key ='$unCode'");
    if (empty($unCodeData)) {
        return $unCode;
    } else {
        unqueCodeGenerate();
    }
}

/* * *************function for return phone number with country*************** */

function addSpacesIntoUnqueCode($uniqueCode) {
    //local variables
    $uniqueCodeValue = '';
    //format number
    if (isset($uniqueCode) && !empty($uniqueCode)) {

        $uniqueCodeValue = substr($uniqueCode, 0, 3) . '-' . substr($uniqueCode, 3, 3) . '-' . substr($uniqueCode, -3);
    }
    return $uniqueCodeValue;
}

/* * **********************format date time for challenge list************************** */

function formatDateForChal($date) {
    $date1 = $date;
    if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate()))) {
        $date = 'Today';
    } elseif (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate() . "-1 day"))) {
        $date = 'Yesterday';
    } else {
        $date = date('M j, Y', strtotime($date));
    }
    $time = date('h:i A', strtotime($date1));
    $dateTime = $date . " at " . $time;
    return $dateTime;
}

/* * **********************format time for scorecard ************************** */

function formatTimeForScorecard($time) {
    $date1 = $date;
    if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate()))) {
        $date = 'Today';
    } elseif (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime(todayZendDate() . "-1 day"))) {
        $date = 'Yesterday';
    } else {
        $date = date('M j, Y', strtotime($date));
    }
    $time = date('h:i A', strtotime($date1));
    $dateTime = $date . " at " . $time;
    return $dateTime;
}

function seconds2human($ss) {
    $s = $ss % 60;
    $m = floor(($ss % 3600) / 60);
    $h = floor(($ss % 86400) / 3600);
    $d = floor(($ss % 2592000) / 86400);
    $M = floor($ss / 2592000);
    return "$d:$h:$m";
}

/*
 * @param date 
 * return hours
 */

function getHours($date) {
    $todayDateDiff = (strtotime(todayZendDate())) - (strtotime($date));
    $hours = intval($todayDateDiff / (60 * 60));
    return $hours;
}

/*
 * Validate number
 * @param number
 */

function validateNumber($numBer) {
    $resUlt = is_numeric($numBer);
    return $resUlt;
}

/*
 * function for checking unqie key is expitred or not @param deviceId return bool *
 */

function checkExpiredOrnot($createdate) {
    $hours = getHours($createdate); // geting hours
    if ($hours >= 24) {
        return true;
    } else {
        return false;
    }
}

/* * *********************get current page url*********************** */

function getPageURLForTrophy() {

    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }

    return $pageURL;
}

/**
 * @desc Function to validate digits
 * @param $variable
 * @author suman khatri on October 08 2014
 * @return true or false
 */
function validateDigits($fieldName) {
    $validateDigits = new Zend_Validate_Digits();
    if (!($validateDigits->isValid($fieldName))) {
        return false;
    } else {
        return true;
    }
}

/**
 * @desc Function to validate date formate
 * @param $variable
 * @author suman khatri on October 29 2014
 * @return true or false
 */
function validateDateFormat($fieldName) {
    $validator = new Zend_Validate_Date(array('format' => 'mm/dd/yyyy'));
    if (!($validator->isValid($fieldName))) {
        return false;
    } else {
        return true;
    }
}

function validateFutureDate($dateOne, $dateTwo) {
    $dateOne = new Zend_Date(strtotime($dateOne));
    $dateTwo = new Zend_Date(strtotime($dateTwo));
    if ($dateOne->isLater($dateTwo)) {
        return false;
    } else {
        return true;
    }
}

//function to get last weekly range
function getLastWeekDateRange() {
    $previous_week = strtotime("-1 week +1 day");
    $start_week = strtotime("last sunday midnight", $previous_week);
    $end_week = strtotime("+6 day", $start_week);
    $dateArray = array(
        'stratDate' => date("Y-m-d 00:00:00", $start_week),
        'endDate' => date("Y-m-d 23:59:59", $end_week)
    );
    return $dateArray;
}

function arraymsort($array, $cols) {
    //echo "<pre>";print_r($array);
    $colarr = array(); //print_r($cols);die;
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) {
            $colarr[$col]['_' . $k] = strtolower($row[$col]);
        }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
    }
    $eval = substr($eval, 0, -1) . ');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k, 1);
            if (!isset($ret[$k]))
                $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}
