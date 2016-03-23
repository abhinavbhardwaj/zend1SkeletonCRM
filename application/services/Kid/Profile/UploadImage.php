<?php

/**
 * PHP version 5
 * 
 * @category  Service_Kid
 * @package   Kid
 * @author    Ashwini Asgarwal <ashwini.agarwal@a3logics.in>
 * @copyright 2014 Finny
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.myfinny.com/
 * @return  
 */
class Application_Service_Kid_Profile_UploadImage
{

    public function save($data)
    {
        $validateAuth = $this->validate($data);
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            return $validateAuth;
        }

        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $childInfo = $tblChildInfo->fetchRow(array('child_id = ?' => $data['childId']));

        $fileName = $this->saveImage($data['image']);

        $updateData = array('image' => $fileName, 'modified_date' => date("Y-m-d H:i:s"));
        $tblChildInfo->updateChildInfo($updateData, $data['childId']);

        $this->deletePreviosImage($childInfo->image);

        $this->sendPushNotification($data['childId'], $validateAuth['parentId']);
        $this->generateNotification($childInfo, $validateAuth['parentId'], $validateAuth['deviceId']);

        return array(
            'image' => AWS_S3_URL . 'child/' . $fileName,
            'status_code' => STATUS_SUCCESS,
            'message' => 'Image Updated Successfully',
        );
    }

    private function validate($data, $isAvatar = FALSE)
    {
        if (empty($data['childId'])) {
            $messageArray = array(
                'message' => "Child id can't be empty",
                'status_code' => STATUS_ERROR
            );
            return $messageArray;
        }

        $objAuth = new Application_Service_User_AuthDevice();
        $validateAuth = $objAuth->authenticate($data['device_key'], $data['access_token'], $data['childId']);
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            return $validateAuth;
        }

        if (!$isAvatar && empty($data['image'])) {
            return array(
                'message' => "Image can't be empty",
                'status_code' => STATUS_ERROR
            );
        }

        return $validateAuth;
    }

    private function saveImage($image)
    {
        $fileName = uniqid() . '_' . time() . '.jpeg';
        $s3 = new My_Service_Amazon_S3();
        $s3->save(My_Thumbnail::getThumbnail(base64_decode($image), 'jpeg', 512, 512), 'child/' . $fileName);
        $s3->save(My_Thumbnail::getThumbnail(base64_decode($image), 'jpeg', 64, 64), 'child/thumb/' . $fileName);

        return $fileName;
    }

    private function deletePreviosImage($image)
    {
        if (empty($image)) {
            return;
        }

        $s3 = new My_Service_Amazon_S3();
        $s3->delete('child/' . $image);
        $s3->delete('child/thumb/' . $image);
    }

    private function sendPushNotification($childId, $parentId)
    {
        $objChild = new Application_Model_Child();
        $childInfoArray = $objChild->getChildInfoArray($childId);
        $objChild->sendPushOnAddOrUpdateKid($parentId, $childInfoArray, 'edit', $childId);
    }

    private function generateNotification($childInfo, $parentId, $deviceId)
    {
        $tblParentInfo = new Application_Model_DbTable_ParentInfo();
        $parentInfo = $tblParentInfo->fetchRow(array('parent_id = ?' => $parentId));

        $gender = $childInfo->gender == 'B' ? 'his' : ($childInfo->gender == 'G' ? 'her' : '');
        $message = (empty($childInfo->image) ? "added " : "updated ") . $gender . " profile picture";

        $insertNotifdata = array(
            'user_id' => $parentInfo['user_id'],
            'notification_type' => 'IMAGE',
            'description' => $message,
            'child_device_id' => $deviceId,
            'childe_name' => $childInfo->name,
            'child_id' => $childInfo->child_id,
            'created_date' => date("Y-m-d H:i:s")
        );

        $tblParentNofic = new Application_Model_DbTable_ParentNotifications();
        $tblParentNofic->AddParentNotification($insertNotifdata);
    }

    public function updateAvatar($data)
    {
        $validateAuth = $this->validate($data, true);
        if ($validateAuth['status_code'] == STATUS_ERROR) {
            return $validateAuth;
        }

        $updateData = array('avatar' => (int) $data['avatar'], 'modified_date' => date("Y-m-d H:i:s"));
        $tblChildInfo = new Application_Model_DbTable_ChildInfo();
        $tblChildInfo->updateChildInfo($updateData, $data['childId']);

        $this->sendPushNotification($data['childId'], $validateAuth['parentId']);

        return array(
            'status_code' => STATUS_SUCCESS,
            'message' => 'Avatar Updated Successfully',
        );
    }

}
