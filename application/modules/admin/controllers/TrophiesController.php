<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Questions
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */
class Admin_TrophiesController extends Zend_Controller_Action {


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
        $this->view->headTitle(ADMIN_OVERALL_TROPHYEDIT);
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (is_array($flashMessages) && !empty($flashMessages)) {

            if ($flashMessages[0] == 'Title already exists' || $flashMessages[0] == 'Points already exists')
                $this->view->error = $flashMessages[0];
            else
                $this->view->success = $flashMessages[0];
        }
        $tblTrophies = new Application_Model_DbTable_Trophies();
        $request = $this->getRequest();
        $trophy_id = $request->id;
        $getData = $tblTrophies->trophyInfo($trophy_id);
        
        //echo "<pre>"; print_r($getData); exit;

        $this->view->trophyData = $getData;

        if ($request->isPost()) {
            $title = $request->getPost('title');
            $description = $request->getPost('description');
            $image = basename($request->getPost('image_file_name'));
            //$points = $request->getPost('points');
            $points = $getData['points'];
            $date = date('Y-m-d H:i:s');

            $check_title = $tblTrophies->check_title2($title, $trophy_id);
            $flage = 0;
            if ($check_title != 0) {
                $this->view->error = 'For this title, trophy is already exists';
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->points = $points;
                $flage = 1;
                return false;
            }

            $check_points = $tblTrophies->check_points2($points, $trophy_id);
            if ($check_points != 0) {
                $this->view->error = 'For these finny coins, trophy is already exists';
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->points = $points;
                $flage = 1;
                return false;
            }


            if ($flage == 0) {
                if (empty($image))
                    $image = 'no_image.jpeg';
                $trophyDataArray = array('title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'points' => $points,
                    'created_date' => $date
                );
                $arrayForPush[0] = $trophyDataArray;
                try {
                    
                    
                    $updateData = $tblTrophies->updatetrophyData($trophyDataArray, $trophy_id);
                    
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

                           //update all entries in child trophies where trophy_id = $trophy_id and type = TO
                           $tblChildTrophy = new Application_Model_DbTable_ChildTrophy();
                           $tblChildTrophy->updateChildTrophyData($newArray, $trophy_id, 'TO');

                        }
    
                    }
                    
                    $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy($arrayForPush, 'TO', $trophy_id , 'edit trophy');
                    $this->_helper->flashMessenger->addMessage('Trophy updated successfully');
                    $this->_redirect('/admin/trophies/trophylist');
                } catch (Exception $e) {
                    $this->view->error = $e->getMessage();
                }
            }
        }
    }

    public function addtrophyAction() {
        $objTrophy = new Application_Model_Trophy();
        $this->view->headTitle(ADMIN_ADD_OVERALLTROPHY);
        $flashMessages = $this->_helper->flashMessenger->getMessages();

        if (is_array($flashMessages) && !empty($flashMessages)) {

            if ($flashMessages[0] == 'Title already exists' || $flashMessages[0] == 'Coins already exists')
                $this->view->error = $flashMessages[0];
            else
                $this->view->success = $flashMessages[0];
        }
        $request = $this->getRequest();
        $tblTrophies = new Application_Model_DbTable_Trophies();
        if ($request->isPost()) {

            $title = $request->getPost('title');
            $description = $request->getPost('description');
            $image = basename($request->getPost('image_file_name'));
            $points = $request->getPost('points');
            $date = date('Y-m-d H:i:s');

            $check_title = $tblTrophies->check_title($title);
            $flage = 0;
            if ($check_title != 0) {
                $this->view->error = 'For this title, trophy is already exists';
                $this->view->title = $title;
                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->points = $points;
                $flage = 1;
                return false;
            }

            $check_points = $tblTrophies->check_points($points);
            if ($check_points != 0) {
                $this->view->error = 'For these finny coins, trophy is already exists';
                $this->view->title = $title;

                $this->view->description = $description;
                $this->view->image = $image;
                $this->view->points = $points;
                $flage = 1;
                return false;
            }


            if ($flage == 0) {
                if (empty($image))
                    $image = 'no_image.jpeg';
                $trophyDataArray = array('title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'points' => $points,
                    'created_date' => $date
                );


                $addData = $tblTrophies->addTrophy($trophyDataArray);
                $arrayForPush[0] = $trophyDataArray;
                if ($addData) {
                    try{
                        $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy($arrayForPush, 'TO', $addData , 'add trophy');
                    }catch (Exception $e){
                        $this->view->error = $e->getMessage();
                    }
                    $this->_helper->flashMessenger->addMessage('Trophy added successfully');
                    $this->_redirect('/admin/trophies/trophylist');
                } else {
                    $this->view->error = 'Error adding trophy data';
                }
            }
        }
    }

    public function trophylistAction() {
        $this->view->headTitle(ADMIN_OVERALL_TROPHYLIST);
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
        $tblTrophies = new Application_Model_DbTable_Trophies();
        $getTrophiesList = $tblTrophies->getTrophiesInfo($searchData);

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

        $tblTrophies = new Application_Model_DbTable_Trophies();
        $objTrophy = new Application_Model_Trophy();
        $request = $this->getRequest();

        $trophy_id = $request->getParam('id');
        try {
            $delete = $tblTrophies->deleteTrophy($trophy_id);

            if ($delete) {
                $datatoSendViaPush = $objTrophy
                            ->sendPushOfTrophy(null, 'TO', $trophy_id , 'delete trophy');
                $this->_helper->getHelper('FlashMessenger')
                        ->addMessage('Trophy deleted successfully.');
                $this->_redirect('admin/trophies/trophylist');
            }
        } catch (Exception $ex) {
            $this->view->error = $this->_helper->getHelper('FlashMessenger')
                    ->addMessage(' Error: ' . $ex->getMessage());
            $this->_redirect('admin/trophies/trophylist');
        }
    }

    public function viewtrophydataAction() {
        //$this->_helper->layout->disableLayout(); 
        $this->view->headTitle(ADMIN_OVERALL_TROPHYVIEW);
        $tblTrophies = new Application_Model_DbTable_Trophies();
        $request = $this->getRequest();
        $Id = $request->id;
        $getData = $tblTrophies->trophyInfo($Id);
        $this->view->trophyData = $getData;
    }

}
