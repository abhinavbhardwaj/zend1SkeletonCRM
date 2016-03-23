<?php
/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Video
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */


class Admin_VideoController extends Zend_Controller_Action
{
	

	public function init()
	{	 
		parent::init();
		$layout = Zend_Layout::getMvcInstance();//Create object
		 $layout->setLayout('admin',true);//set layout admin
		 require_once APPLICATION_PATH.'/../library/functions.php';
	}

	/*
	 * function for when admin page request then redirect on login page for authentication
	 */
	public function indexAction()
	{
		$this->_redirect('admin/login/login');
	}
	
	
	public function updatevideoAction()
	{
		$request 					= $this->getRequest();
		$errorFound = false;
		$todayData = todayZendDate();
		try
		{
			$tblVideo 					= new Application_Model_DbTable_Video();
			if($request->isPost())
			{
				
				$videoTitle 			= $request->getPost('video_title');
				$videoUrl 			= $request->getPost('video_url');
				//$file					= $_FILES['video']['name']; 
				$videoId				= $request->getPost('id'); 
				//$oldVideo				= $request->getPost('old_video'); 
				if(validateNotNull($videoTitle)==false)
				{
					$this->view->error 	= 'Please enter video title';
					$errorFound = true;
					return false;
					
				}
				
				if(validateNotNull($videoUrl)==false)
				{
					$this->view->error 	= 'Please enter video URl';
					$errorFound = true;
					return false;
				}
				if($this->_parse_yturl($videoUrl)==false)
				{
					$this->view->error 	= 'Please enter valid video URl';
					$errorFound = true;
					return false;
				}
				
				if(empty($videoId) && !$errorFound ){
					$updateData 			= array('video_title' => $videoTitle,'video_url' =>$videoUrl,'modified_date'=> $todayData);
					$updatevideoData 		= $tblVideo->addVideoData($updateData);
					$this->view->success 	= 'Video updated successfully';
					$this->_helper->getHelper('FlashMessenger')
					->addMessage('Video updated successfully');
				}elseif(!$errorFound){
					$updateData 			= array('video_title' => $videoTitle,'video_url' =>$videoUrl,'modified_date'=> $todayData);
					$updatevideoData 		= $tblVideo->updateVideoData($videoId,$updateData);
					$this->view->success 	= 'Video updated successfully';
					$this->_helper->getHelper('FlashMessenger')
					->addMessage('Video updated successfully');
				}
				$this->_helper->redirector('videolist');
			}
			if($errorFound){
				$data = $request->getPost();
			}else{
				$data				= $tblVideo->getVideo();
			}
			$this->view->video	= $data;
		}
		catch(Exception $e)
		{
			$this->view->error = $e->getMessage();
			return false;
		}
	}
	
	
	public function videolistAction()
	{
		$request 			= $this->getRequest();
		$tblVideo 			= new Application_Model_DbTable_Video();
		$data				= $tblVideo->getVideo();
		$this->view->video	= $data;
		$flashMessages 			= $this->_helper->flashMessenger->getMessages();
		$flashMessenger 		= $this->_helper->getHelper('FlashMessenger');
		$flashMessages 			= $flashMessenger->getMessages();
		if(is_array($flashMessages) && !empty($flashMessages)){
			$this->view->success = $flashMessages[0];
			$flashMessenger->addMessage('');
		}
	}
	private function _parse_yturl($url){
    $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
}
}