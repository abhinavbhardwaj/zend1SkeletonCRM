<?php
/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Images For Fronend side
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */
class Admin_ImagesController extends Zend_Controller_Action {
	public function init() {
		parent::init ();
		$layout = Zend_Layout::getMvcInstance (); // Create object
		$layout->setLayout ( 'admin', true ); // set layout admin
		require_once APPLICATION_PATH . '/../library/functions.php';
		require_once (APPLICATION_PATH . '/../library/function.resize.php');
	}
	
	/*
	 * function for when admin page request then redirect on login page for authentication
	 */
	public function indexAction() {
		$this->_redirect ( 'admin/login/login' );
	}
	public function updateimageAction() {
		$tblImage = new Application_Model_DbTable_Images();
		$request = $this->getRequest ();
		$imageIdUrl = base64_decode($request->getParam('id'));
		$this->view->imageId = $imageIdUrl;
		$data = $tblImage->getImages($imageIdUrl);
		$this->view->Images = $data;
		try {
			if ($request->isPost ()) {
				$imageTitle = $request->getPost ( 'image_title' );
				$imageOld = $request->getPost ( 'old_image' );
				$file = $_FILES ['image'] ['name'];
				$imageId = $request->getPost ( 'id' );
				//$oldImage = $request->getPost ( 'old_image' );
				if (validateNotNull ( $imageTitle ) == false) {
					$this->view->error = 'Please enter image Title';
					return false;
				}
				
				if (validateNotNull ( $file ) == false) {
					$this->view->error = 'Please select image File';
					return false;
				}
				if ($_FILES ['image'] ['name'] != '') {
					$name = $_FILES ['image'] ['name'];
					$tmpfile = $_FILES ['image'] ['tmp_name'];
					$ext_array = explode ( '.', $name );
					$ext = strtolower ( $ext_array [1] );
					$allowedArray = array (
							'png',
							'bmp',
							'gif',
							'jpg',
							'jpeg',
							'pjpeg',
							'png',
							'tiff',
							'tif',
							'x-tiff',
							'x-windows-bmp' 
					);
					
					list($width, $height) = getimagesize($tmpfile);
					if($width >= 494 && $height >= 268){
					if (in_array ( $ext, $allowedArray )) {
						$destinationPath = PUBLIC_PATH .FRONT_IMAGE; // defined path for main parent images
						$destinationPathThum = $destinationPath.FRONT_IMAGE_THUMB;
						$fileGetPath = PUBLIC_PATH .'/'.REMOTE_PATH. $file; // file move for pass path into class where classs read images
						move_uploaded_file ( $tmpfile, $fileGetPath ); // move files
						@chmod ( $fileGetPath, 0777 ); // change images access
						$ext = pathinfo($fileGetPath, PATHINFO_EXTENSION);
						$fileName = 'front_'.time().'.'.$ext;
						$image = new Imagick($fileGetPath);
						if($ext == 'tiff' || $ext == 'TIFF' || $ext == 'TIF' || $ext == 'tiff'){
							$image->setImageFormat('jpg');
							$fileName = 'childpic_'.time().'.'.jpg;
						}
						$image->cropThumbnailImage(F_IMAGE_SIZE_W, F_IMAGE_SIZE_H);
						$image->writeImage($destinationPath.$fileName);
						@chmod ( $destinationPath . $fileName, 0777 );
						
						
						$image = new Imagick($fileGetPath);
						if($ext == 'tiff' || $ext == 'TIFF'){
							$image->setImageFormat('jpg');
							$fileName = 'childpic_'.time().'.'.jpg;
						}
						$image->cropThumbnailImage(F_T_IMAGE_SIZE_W, F_T_IMAGE_SIZE_H);
						$image->writeImage($destinationPathThum.$fileName);
						@chmod ( $destinationPathThum . $fileName, 0777 );
						
						
						
						
						
						
						/*$settings = array (
								'w' => F_IMAGE_SIZE_W,
								'h' => F_IMAGE_SIZE_H,
								'crop' => true
						); // defined array for images crop
						$newFileName = 'front_' . time (); // get time with name of images
						$fileName = resize ( $fileGetPath, $settings, $destinationPath, $newFileName );
						@chmod ( $destinationPath . $fileName, 0777 );
						$destinationPathThum = $destinationPath.FRONT_IMAGE_THUMB; // defined destination path for save thumb of images
						$settingsThumb = array (
								'w' => F_T_IMAGE_SIZE_W,
								'h' => F_T_IMAGE_SIZE_H,
								'crop' => true
						); // defined array for images crop to generate thumb of parent
						$imagesThumb = resize ( $fileGetPath, $settingsThumb, $destinationPathThum, $newFileName ); // call resize fucntion
						@chmod ( $destinationPathThum . $imagesThumb, 0777 );*/
						if(file_exists($fileGetPath)){
							unlink ( $fileGetPath ); // remove file from uploaded orginal files
							unlink ( $destinationPath.$imageOld ); //removes old imges
							//unlink ( $destinationPathThum.$imageOld ); //removes old imges thumb
						}
						if(empty($imageId)){
							$imageData 			= array('image_title' => $imageTitle,'image_name' =>$fileName,'modified_date'=> todayZendDate());
							$result = $tblImage->addImageData($imageData);
						}else{
							$imageData 			= array('image_title' => $imageTitle,'image_name' =>$fileName,'modified_date'=> todayZendDate());
							$result = $tblImage->updateImageData($imageId,$imageData);
						}
						$this->view->success 	= 'Image updated Successfully';
						$this->_helper->getHelper('FlashMessenger')
						->addMessage('Image updated Successfully');
						$this->_helper->redirector('imageslist');
					} else {
						$this->view->error = 'Incorrect type of image';
						return false;
					}

				}else{
					$this->view->error = "The image you tried to upload is too small. It needs to be at least 494 pixels wide. Please try again with a larger image.";
					return false;
				}
					
				} else {
					$this->view->error = "image can't be blank";
					return false;
				}
			}
			
		} catch ( Exception $e ) {
			$this->view->error = $e->getMessage ();
			return false;
		}
	}
	public function imageslistAction() {
		$tblImages = new Application_Model_DbTable_Images ();
		$data = $tblImages->getAllImages ();
		$this->view->Images = $data;
		$flashMessages = $this->_helper->flashMessenger->getMessages ();
		$flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$flashMessages = $flashMessenger->getMessages ();
		if (is_array ( $flashMessages ) && ! empty ( $flashMessages )) {
			$this->view->success = $flashMessages [0];
			$flashMessenger->addMessage ( '' );
		}
	}
}