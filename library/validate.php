<?php
require_once APPLICATION_PATH . '/../library/functions.php';
//function 
function validateFirstName($firstName) {
    if (validateNotNull($firstName) == false) {
        return 'Please enter name';
    }
    //validates min length of $parentfName
    if (validateMinLength($firstName, '1') == false) {
        return 'Please enter name of min 1 characters';
    }
    //validates max length of $parentfName
    if (validateMaxLength($firstName, '128') == false) {
        return 'Please enter name of max 128 characters';
    }
    return null;
}

//function to validate last name
function validateLastName($lastName) {
    //validates max length of $parentfName
    if (validateMaxLength($lastName, '128') == false) {
        return 'Please enter last name of max 128 characters';
    }
    return null;
}

//function to validate email
function validateEmailId($emailId) {
    if (validateNotNull($emailId) == false) {
        return "Please enter Email";
    }
    if (validateEmail($emailId) == false) {
        return "Please enter valid Email";
    }
    return null;
}

//function to validate username
function validateUserName($rUserName) {
    if (validateNotNull($rUserName) == false) {
        return "Please enter username";
    }
    if (validateMinLength($rUserName, '4') == false) {
        return "Please enter username of min 4 characters";
    }
    if (validateMaxLength($rUserName, '15') == false) {
        return "Please enter username of max 15 characters";
    }
    if (validateFieldWithRegex($rUserName, '/^[a-zA-Z0-9_\-]+$/') == false) {
        return "Only alphanumeric, - and _ are allowed in Username";
    }
    return null;
}

//function to validate password
function validatePassword($userPassword) {
    if (validateNotNull($userPassword) == false) {
        return "Please enter Password";
    }
    if (validateMinLength($userPassword, '8') == false) {
        return "Please enter password of min 8 characters";
    }
    if (validateMaxLength($userPassword, '16') == false) {
        return "Please enter password of max 16 characters";
    }
    return null;
}

/**
 * @desc Function to validate pin
 * @param $pinNumber
 * @author suman khatri on October 08 2014
 * @return message or null
 */
function validatePin($pinNumber) {
    //validates null value
    if (validateNotNull($pinNumber) == false) {
        return "Please enter pin number";
    }
    //validates digit in pin number
    if (validateDigits($pinNumber) == false) {
        return "Only numbers are allowed in pin munber";
    }

    //validates min length 
    if (validateMinLength($pinNumber, '4') == false) {
        return "Please enter pin munber of 4 digits";
    }

    //validates max length 
    if (validateMaxLength($pinNumber, '4') == false) {
        return "Please enter pin munber of 4 digits";
    }
    return null;
}

/**
 * @desc Function to validate device detail
 * @param $deviceKey,$deviceName
 * @author suman khatri on October 08 2014
 * @return message or null
 */
function validateDeviceDetail($deviceKey, $deviceName, $accessToken = null) {
    $message = ''; //defining variable $message
    //validate null value for $deviceKey
    if (empty($deviceKey) && $deviceKey == null) {
        return "Device key can't be null or empty";
    }
    if (empty($accessToken) && $accessToken == null) {
        //if $message is null
        if (empty($message) && $message == null) {
            //validate null value for $deviceName
            if (empty($deviceName) && $deviceName == null) {
                return "Device name can't be null or empty";
            }
        }
    }
}

/**
 * @desc Function to validate authentication detail
 * @param $deviceKey,$accessToken
 * @author suman khatri on October 08 2014
 * @return message or null
 */
function validateAuthenticationDetailMobile($deviceKey, $accessToken) {
    $message = ''; //defining variable $message
    //validate null value for $deviceKey
    if (empty($deviceKey) && $deviceKey == null) {
        return "Device key can't be null or empty";
    }
    //if $message is null
    if (empty($message) && $message == null) {
        //validate null value for $deviceName
        if (empty($accessToken) && $accessToken == null) {
            return "Access token can't be null or empty";
        }
    }
    //if $message is null
    if (empty($message) && $message == null) {
        //validate digit value for $deviceName
        if (validateDigits($accessToken) == false) {
            return "Only numbers are allowed in access token";
        }
    }
}

//function 
function validateKidFirstName($firstName) {
    if (validateNotNull($firstName) == false) {
        return 'Please enter child\'s display name';
    }
    if (validateNoSpace($firstName) == false) {
        return 'No space allowed in child\'s display name';
    }    
    //validates min length of $parentfName
    if (validateMinLength($firstName, '1') == false) {
        return 'Please enter child\'s display name of min 1 characters';
    }
    //validates max length of $parentfName
    if (validateMaxLength($firstName, '10') == false) {
        return 'Please enter child\'s display name of max 10 characters';
    }
    return null;
}

//function 
function validateKidLastName($firstName) {
    //validates max length of $parentfName
    if (validateMaxLength($firstName, '128') == false) {
        return 'Please enter child last name of max 128 characters';
    }
    return null;
}
