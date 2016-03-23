<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Questions
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */
class Admin_StreaksubController extends Zend_Controller_Action {

    public function init() {
        parent::init();
        $layout = Zend_Layout::getMvcInstance(); //Create object
        $layout->setLayout('admin', true); //set layout admin
        require_once APPLICATION_PATH . '/../library/functions.php';
    }

    /**
     * upload trophy image (Ajax Preview)
     */
    public function uploadimageAction()
    {
        $ext = pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION);
        $fileName = preg_replace("/[^A-Za-z0-9]/", '', $_FILES['images']['name']) . '_' . time() . '.' . $ext;

        $s3 = new My_Service_Amazon_S3();
        $s3->save(My_Thumbnail::getThumbnail($_FILES ["images"]["tmp_name"], $ext, 512, 512), 'trophy/' . $fileName);

        echo AWS_S3_URL . 'trophy/' . $fileName;
        exit();
    }

//end of $handle->uploade block

    public function indexAction() {
        
    }

    /*
     * function to add category
     */

    public function edittrophyAction() {
        $objTrophy = new Application_Model_Trophy(); 
        $this->view->headTitle(ADMIN_SUB_TROPHYEDIT);
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {

            if ($flashMessages[0] == 'Title already exists' || $flashMessages[0] == 'Streak already exists')
                $this->view->error = $flashMessages[0];
            else
                $this->view->success = $flashMessages[0];
        }
        $tblStreakSub = new Application_Model_DbTable_Streaksub();
        $request = $this->getRequest();
        $trophy_id = $request->id;
        $getData = $tblStreakSub->trophyInfo($trophy_id);
        $this->view->trophyData = $getData;
        $sub_std = $tblStreakSub->getsubject_standard();
        $this->view->sub_std = $sub_std;

        if ($request->isPost()) {
            
            $subject_id = $getData['subject_id'];
            $title = $request->getPost('title');
            $description = $request->getPost('description');
            $image = basename($request->getPost('image_file_name'));
            //$streak = $request->getPost('streak');
            $streak = $getData['streak'];
            $date = date('Y-m-d H:i:s');
            $flage = 0;
            $check_title = $tblStreakSub->check_title2($subject_id, $title, $trophy_id);
            if ($check_title != 0) {
                $this->view->error = 'For this title and subject, streak trophy is already exists';
                $this->view->subject_id = $subject_id;
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->streak = $streak;
                $flage = 1;
                return false;
            }
            $check_streak = $tblStreakSub->check_streak2($subject_id, $streak, $trophy_id);
            if ($check_streak != 0) {
                $this->view->error = 'For this subject and streak, streak trophy already exists';
                $this->view->subject_id = $subject_id;
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->streak = $streak;
                $flage = 1;
                return false;
            }
            if ($flage == 0) {
                if (empty($image))
                $image = 'no_image.jpeg';
                $trophyDataArray = array(
                    'subject_id' => $subject_id,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'streak' => $streak,
                    'created_date' => $date
                );
                $arrayForPush[0] = $trophyDataArray;
                try {
                    $updateData = $tblStreakSub->updatetrophyData($trophyDataArray, $trophy_id);                   
                    
                    if($updateData){
                            
                        /**
                        *  IF ANY OF THE TROPHY TITLE, DESCRIPTION OR IMAGE GETS CHANGED, CHANGE THE RESPECTIVE ENTRIES FOR THIS TROPHY IN CHILD TORPHIES TABLE   
                        */

                        $newArray = array();
                        /* check if trophy has new title */
                        if(strtolower($getData['title']) != strtolower($trophyDataArray['title'])){
                            $newArray['title'] = $trophyDataArray['title'];
                        }
                        /* check if trophy has new description */
                        if(strtolower($getData['description']) != strtolower($trophyDataArray['description'])){
                            $newArray['description'] = $trophyDataArray['description'];
                        }
                        /* check if trophy has new image */
                        if($image != 'no_image.jpeg' && strtolower($getData['image']) != strtolower($trophyDataArray['image'])){
                            $newArray['image'] = $trophyDataArray['image'];
                        }

                        if(!empty($newArray)){

                           //update all entries in child trophies where trophy_id = $trophy_id and type = SS
                           $tblChildTrophy = new Application_Model_DbTable_ChildTrophy();
                           $tblChildTrophy->updateChildTrophyData($newArray, $trophy_id, 'SS');

                        }
                        
                    }
                    
                    $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy($arrayForPush, 'SS', $trophy_id , 'edit trophy');
                    $this->_helper->flashMessenger->addMessage('Streak Trophy updated successfully');
                    $this->_redirect('/admin/streaksub/trophylist');
                } catch (Exception $e) {
                    $this->view->error = $e->getMessage();
                }
            }
        }
    }

    public function addtrophyAction() {
        $objTrophy = new Application_Model_Trophy();
        $this->view->headTitle(ADMIN_ADD_SUBTROPHY);
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {
            if ($flashMessages[0] == 'Title already exists' || $flashMessages[0] == 'Points already exists')
                $this->view->error = $flashMessages[0];
            else
                $this->view->success = $flashMessages[0];
        }
        $request = $this->getRequest();
        $tblStreakSub = new Application_Model_DbTable_Streaksub();
        $sub_std = $tblStreakSub->getsubject_standard();
        $this->view->sub_std = $sub_std;
        if ($request->isPost()) {
            $subject_id = $request->getPost('subject_id');
            $title = $request->getPost('title');
            $description = $request->getPost('description');
            $image = basename($request->getPost('image_file_name'));
            $streak = $request->getPost('streak');
            $date = date('Y-m-d H:i:s');
            $check_title = $tblStreakSub->check_title($subject_id, $title);
            $flage = 0;
            if ($check_title != 0) {
                $this->view->error = 'For this title and subject, streak trophy is already exists';

                $this->view->subject_id = $subject_id;
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->streak = $streak;
                $flage = 1;
                return false;
            }
            $check_streak = $tblStreakSub->check_streak($subject_id, $streak);
            if ($check_streak != 0) {
                $this->view->error = 'For this subject and streak, streak trophy already exists';
                $this->view->subject_id = $subject_id;
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->streak = $streak;
                $flage = 1;
                return false;
            }
            if ($flage == 0) {
                if (empty($image))
                $image = 'no_image.jpeg';
                $trophyDataArray = array(
                    'subject_id' => $subject_id,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'streak' => $streak,
                    'created_date' => $date
                );
                $addData = $tblStreakSub->addTrophy($trophyDataArray);
                $arrayForPush[0] = $trophyDataArray;
                if ($addData) {
                    $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy($arrayForPush, 'SS', $addData , 'add trophy');
                    $this->_helper->flashMessenger->addMessage('Streak Trophy added successfully');
                    $this->_redirect('/admin/streaksub/trophylist');
                } else {
                    $this->view->error = 'Error adding trophy data';
                }
            }
        }
    }

    public function trophylistAction() {
        $this->view->headTitle(ADMIN_SUB_TROPHYLIST);
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {
            $this->view->success = $flashMessages[0];
        }
        $request = $this->getRequest();
        $searchData = $request->getParam('seachdata');
        $perPage = $request->getParam('perpage'); //echo $perPage; die;
        if (!empty($perPage)) {
            $recordsPerPage = $perPage;
        } else {
            $recordsPerPage = PER_PAGE;
        }
        $this->view->perpage = $recordsPerPage;
        $tblStreakSub = new Application_Model_DbTable_Streaksub();
        $getTrophiesList = $tblStreakSub->getTrophiesInfo($searchData);
        $sub_std = $tblStreakSub->getsubject_standard();
        foreach ($sub_std as $val) {
            $subject[$val['subject_id']] = $val['subject_name'] . '-' . $val['name'];
        }
        $this->view->sub_std = $subject;
        $totalRecords = count($getTrophiesList);
        $page = $this->_getParam('page', 1);
        $paginator = Zend_Paginator::factory($getTrophiesList);
        $paginator->setItemCountPerPage($recordsPerPage);
        $paginator->setCurrentPageNumber($page);
        $this->view->trophyData = $paginator;
        $this->view->totalRecord = $totalRecords;
        $this->view->currentPage = $page;
        $this->view->searchdata = $searchData;
    }

    public function deletetrophyAction() {
        $tblStreakSub = new Application_Model_DbTable_Streaksub();
        $objTrophy = new Application_Model_Trophy();
        $request = $this->getRequest();
        $trophy_id = $request->getParam('id');
        try {
            $delete = $tblStreakSub->deleteTrophy($trophy_id);
            if ($delete) {
                $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy(null, 'SS', $trophy_id , 'delete trophy');
                $this->_helper->getHelper('FlashMessenger')
                        ->addMessage('Streak Trophy deleted successfully.');
                $this->_redirect('admin/streaksub/trophylist');
            }
        } catch (Exception $ex) {
            $this->view->error = $this->_helper->getHelper('FlashMessenger')
                    ->addMessage(' Error: ' . $ex->getMessage());
            $this->_redirect('admin/streaksub/trophylist');
        }
    }

    public function viewtrophydataAction() {
        //$this->_helper->layout->disableLayout(); 
        $this->view->headTitle(ADMIN_SUB_TROPHYVIEW);
        $tblStreakSub = new Application_Model_DbTable_Streaksub();
        $request = $this->getRequest();
        $Id = $request->id;
        $getData = $tblStreakSub->trophyInfo($Id);
        $this->view->trophyData = $getData;
        $subject = $tblStreakSub->getparticularsubject_standard($getData['subject_id']);
        $subject_name = $subject[0]['subject_name'] . '-' . $subject[0]['name'];
        $this->view->subject_name = $subject_name;
    }

}
