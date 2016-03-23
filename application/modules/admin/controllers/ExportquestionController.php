<?php

/**
 * @category   PSV Balance Admin modules
 * @package    PSV Balance
 * @subpackage Cms
 * @copyright  Copyright (c) A3logics India Ltd. (http://www.a3logics.com)
 * @Library    Zend FrameWork 1.11
 * @version    PSV Balance 1.0
 */

/**
 * Concrete base class for About classes
 *
 *
 * @uses CmsController
 * @category Cms
 * @package Zend_Application
 * @subpackage Cms
 */
class Admin_ExportquestionController extends Zend_Controller_Action {
	/*
	 * function call during initialization
	 */
	protected $_categoryTbObj;
	public function init() {
		parent::init ();
		$layout = Zend_Layout::getMvcInstance (); // Create object
		$layout->setLayout ( 'admin', true ); // set layout admin
		require_once APPLICATION_PATH . '/../library/functions.php';
		require_once (APPLICATION_PATH . '/../library/excel_reader/excel_reader.php');
		
		$this->_categoryTbObj = new Application_Model_Category ();
	}
	/*
	 * function for when admin page request then redirect on login page for authentication
	 */
	public function indexAction() {
		$countErorr = 0;
		$excel = new PhpExcelReader ();
		$data = array ();
		$excel->read ( APPLICATION_PATH . '/../public/questionImport/5_Final_11042013a.xls' );
		foreach ( $excel->sheets as $sheets ) {
			$cell = 1;
			$numRows = $sheets ['numRows'];
			$numCols = $sheets ['numCols'];
			for($i = 2; $i <= $numRows; $i ++) {
				$categoryName = $sheets ['cells'] [$i] [5];
				$question = $sheets ['cells'] [$i] [7];
				$rightAnswer = $sheets ['cells'] [$i] [8];
				$wrongAnswer1 = $sheets ['cells'] [$i] [9];
				$wrongAnswer2 = $sheets ['cells'] [$i] [10];
				$wrongAnswer3 = $sheets ['cells'] [$i] [11];
				
				$catIdData = $this->_categoryTbObj->getcategoryId ( $categoryName );
				if (! empty ( $catIdData )) {
					//$questionImage = $this->loadimage ( $question );
					//$rightAnswerImage = $this->loadimage ( $rightAnswer );
					//$wrongAnswer1Image = $this->loadimage ( $wrongAnswer1 );
					//$wrongAnswer2Image = $this->loadimage ( $wrongAnswer2 );
					//$wrongAnswer3Image = $this->loadimage ( $wrongAnswer3 );
					
					
					$tblStandards = new Application_Model_DbTable_CategoriesStandards ();
					$tblcategory = new Application_Model_DbTable_QuestionCategories ();
					$tblquestion = new Application_Model_DbTable_ChildQuestion ();
					$tbloptions = new Application_Model_DbTable_QuestionOptions ();
					
					$adminInfoSession = new Zend_Session_Namespace ( 'adminInfo' );
					$adminLogindata = $adminInfoSession->adminData;
					$adminLoginId = $adminLogindata->admin_user_id;
					$adminName = $adminLogindata->name;
						/**
					 * *************image path for question********************
					 */
					
					/**
					 * ************************variables for assigned null***********************
					 */
					$questionEquation = '';
					$questionEquationImage = '';
					$questionEquationImgName = '';
					$explanationEquation = '';
					$explanationEquationImg = '';
					$explanationEquationImgName = '';
					$optionEquation = '';
					$optionEquationImg = '';
					$optionEquationImgName = '';
					$wrognEquation = '';
					$wrognEquationImg = '';
					$wrognEquationImgName = '';
					$wrogn1Equation = '';
					$wrogn1EquationImg = '';
					$wrogn1EquationImgName = '';
					$wrogn2Equation = '';
					$wrogn2EquationImg = '';
					$wrogn2EquationImgName = '';
					// $userQuestionIdData = $tblquestion->fetchRow("bal_question_id = '$edit_id'");
					$createdById = $userQuestionIdData ['created_byid'];
						$arrEqImageNames = array ();
						$category_id = $catIdData ['category_id'];
						
						/**************block for quation text and theire images****************/
						$strReplacedStringQuestion = $this->replaceEQTagsWithImage ( 'PSVEQ[', $question );
						$arrImageUrlsQuestion = $this->getEQTags ( 'PSVEQ[', $question );
						$newFileName = 'question';
						$arrEqImageNamesQuestion = $this->createdImages($arrImageUrlsQuestion,$newFileName);
						$strDescQuestion = $strReplacedStringQuestion;
						$csvEqImagesQuestion = implode ( ',', $arrEqImageNamesQuestion );
						
						/**************End block for quation text and theire images****************/
						$difficulty_level = 'M';
						$grade = 5;
						$url_of_question = uniqid();
						
							
							/**
							 * ************************images created*************************
							 */
						
										$dataqInsert = array (
												'question' => $question,
												'question_display' => $strDescQuestion,
												'question_equation_images' => $csvEqImagesQuestion,
												'question_equation' => $questionEquation,
												'question_equation_image' => $questionEquationImage,
												'question_equation_image_name' => $questionEquationImgName,
												'difficulty_level' => $difficulty_level,
												'grade_id' => $grade,
												'category_id' => $category_id,
												'url_of_question' => $url_of_question,
												'question_image_url' => $question_image_url,
												'answer_image_url' => $answer_image_url,
												'explanation' => addslashes ( $explanation ),
												'explanation_equation' => $explanationEquation,
												'explanation_equation_image' => $explanationEquationImg,
												'explanation_equation_image_name' => $explanationEquationImgName,
												'set_question' => $set_question,
												'refer_book_name' => $refer_book_name,
												'refer_book_chapter' => $refer_book_chapter,
												'refer_article_url' => $refer_article_url,
												'wolframalphaquery' => $wolframalphaquery,
												'expiry_date' => $expiryDate,
												'created_by' => $adminName,
												'created_byid' => $adminLoginId,
												'created_date' => date ( 'Y-m-d H:i:s' ),
												'date' => date ( 'Y-m-d H:i:s' ) 
										);
								
									$questionId = $tblquestion->addQuestion ( $dataqInsert );
									
										/**************block for rigth answer text and theire images****************/
											
										
										$strReplacedStringRight = $this->replaceEQTagsWithImage ( 'PSVEQ[', $rightAnswer );
										$arrImageUrlsQuestionRight = $this->getEQTags ( 'PSVEQ[', $rightAnswer );
										$newFileName = 'right';
										$arrEqImageNamesRight = $this->createdImages($arrImageUrlsQuestionRight,$newFileName);
										$strDescRight = $strReplacedStringRight == 'equation_tags'?'':$strReplacedStringRight;
										$csvEqImagesRight = implode ( ',', $arrEqImageNamesRight );
										
										/**************End block for rigth answer text and theire images****************/
										$strReplacedStringWrong1 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer1 );
										$arrImageUrlsWrong1 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer1 );
										$newFileName = 'wrong1';
										$arrEqImageNamesWrong1 = $this->createdImages($arrImageUrlsWrong1,$newFileName);
										$strDescWrong1 = $strReplacedStringWrong1 == 'equation_tags'?'':$strReplacedStringWrong1;
										$csvEqImagesWrong1 = implode ( ',', $arrEqImageNamesWrong1 );
										
										/**************block for quation text and theire images****************/
										
										$strReplacedStringWrong2 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer2 );
										$arrImageUrlsWrong2 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer2 );
										$newFileName = 'wrong2';
										$arrEqImageNamesWrong2 = $this->createdImages($arrImageUrlsWrong2,$newFileName);
										$strDescWrong2 = $strReplacedStringWrong2  == 'equation_tags'?'':$strReplacedStringWrong2;
										$csvEqImagesWrong2 = implode ( ',', $arrEqImageNamesWrong2 );
										
										/**************End block for rigth answer text and theire images****************/
										
										$strReplacedStringWrong3 = $this->replaceEQTagsWithImage ( 'PSVEQ[', $wrongAnswer3 );
										$arrImageUrlsWrong3 = $this->getEQTags ( 'PSVEQ[', $wrongAnswer3 );
										$newFileName = 'wrong3';
										$arrEqImageNamesWrong3 = $this->createdImages($arrImageUrlsWrong3,$newFileName);
										$strDescWrong3 = $strReplacedStringWrong3  == 'equation_tags'?'':$strReplacedStringWrong3;
										$csvEqImagesWrong3 = implode ( ',', $arrEqImageNamesWrong3 );
										/**************End block for rigth answer text and theire images****************/
										
										
										
										
										
									$dataOrinsert = array (
											'option' => $strDescRight,
											'question_id' => $questionId,
											'option_equation' => $optionEquation,
											'option_equation_image' => $optionEquationImg,
											'option_equation_image_name' => $csvEqImagesRight,
											'answer' => 'Y' 
									);
									
									
									$ranswerId = $tbloptions->addOptionforQuestion ( $dataOrinsert );
									$dataOw1insert = array (
											'option' => $strDescWrong1,
											'question_id' => $questionId,
											'option_equation' => $wrognEquation,
											'option_equation_image' => $wrognEquationImg,
											'option_equation_image_name' =>$csvEqImagesWrong1,
											'answer' => 'N' 
									);
									$w1answerId = $tbloptions->addOptionforQuestion ( $dataOw1insert );
									$dataOw2insert = array (
											'option' => $strDescWrong2,
											'question_id' => $questionId,
											'option_equation' => $wrogn1Equation,
											'option_equation_image' => $wrogn1EquationImg,
											'option_equation_image_name' => $csvEqImagesWrong2,
											'answer' => 'N' 
									);
									
									$w2answerId = $tbloptions->addOptionforQuestion ( $dataOw2insert );
									$dataOw3insert = array (
											'option' => $strDescWrong3,
											'question_id' => $questionId,
											'option_equation' => $wrogn2Equation,
											'option_equation_image' => $wrogn2EquationImg,
											'option_equation_image_name' => $csvEqImagesWrong3,
											'answer' => 'N' 
									);
									
									$w3answerId = $tbloptions->addOptionforQuestion ( $dataOw3insert );
									$this->_helper->getHelper ( 'FlashMessenger' )->addMessage ( 'Question added successfully' );
									unset ( $adminInfoSession->questionData );
								
							
						
					
				} else {
					$errorArray[$i] = 'Standard does not exits into table';
				}
			}
		}
		if(!empty($errorArray)){
			print_r($errorArray);
		}else{
			echo "All questions are inserted into table successfully";
			$this->_redirect ( '/admin/questions/draftquestionlist' );
		}
	}
	/**
	 * controller function to load images
	 * 
	 * @param
	 *        	str question string
	 * @author Ram Kaushik
	 * @return arrEqTags
	 */
	private function loadimage($strQuestions) {
		$tagName = 'PSVEQ[';
		// $tagName = 'PVEQ[';
		
		$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
		$arrTagStr = explode ( $tagName, $strQuestions );
		
		$arrEqStr = array ();
		$arrImageUrls = array ();
		
		for($i = 1; $i <= count ( $arrTagStr ); $i ++) {
			if ($arrTagStr [$i] && strpos ( $arrTagStr [$i], ']' )) {
				$arrSubTagStr = explode ( ']', $arrTagStr [$i] );
                array_pop($arrSubTagStr);
                array_push ( $arrEqStr, implode(']', $arrSubTagStr));
			}
		}
		
		for($j = 0; $j < count ( $arrEqStr ); $j ++) {
			array_push ( $arrImageUrls, $urlImageProcess . str_replace(' ', '&space;', $arrEqStr[$j]) );
		}
		
		$strDesWithDummyTag = $this->replaceEQTagsWithImage ( $tagName, $strQuestions );
		$arrDesWithDummyTag = explode ( 'equation_tags', $strDesWithDummyTag );
		
		$strTextWithImages = $arrDesWithDummyTag [0];
		
		for($k = 1; $k < count ( $arrDesWithDummyTag ); $k ++) {
			
			if (@fopen ( $arrImageUrls [$k - 1], "r" )) {
				$strTextWithImages .= "<img src='" . $arrImageUrls [$k - 1] . "' />" . $arrDesWithDummyTag [$k];
			} else {
				echo 'Unable to load image, please try again';
				exit ();
			}
		}
		return $strTextWithImages;
	}
	
	/**
	 * controller function to get equation tags
	 * 
	 * @param
	 *        	str question string
	 * @author Ram Kaushik
	 * @return arrEqTags
	 */

    private function createdImages($arrImageUrls, $FileName)
    {
        $arrEqImageNames = array();
        $s3 = new My_Service_Amazon_S3();
        for ($k = 0; $k < count($arrImageUrls); $k ++) {
            if (empty($arrImageUrls[$k])) {
                continue;
            }

            $newFileName = $FileName . '_' . time() . '.gif';
            $s3->saveFile($arrImageUrls[$k], 'equation/' . $newFileName);

            array_push($arrEqImageNames, $newFileName);
        }
        return $arrEqImageNames;
    }
    
	private function getEQTags($tagName, $strQuestions) {
		$urlImageProcess = 'http://latex.codecogs.com/gif.latex?';
		$arrTagStr = explode ( $tagName, $strQuestions );
	
		$arrEqStr = array ();
		$arrImageUrls = array ();
	
		for($i = 1; $i <= count ( $arrTagStr ); $i ++) {
			if ($arrTagStr [$i] && strpos ( $arrTagStr [$i], ']' )) {
				$arrSubTagStr = explode ( ']', $arrTagStr [$i] );
                array_pop($arrSubTagStr);
      			array_push ( $arrEqStr, implode(']', $arrSubTagStr));
			}
		}
	
		for($j = 0; $j < count ( $arrEqStr ); $j ++) {
			array_push ( $arrImageUrls, $urlImageProcess . str_replace(' ', '&space;', $arrEqStr[$j]) );
		}
	
		return $arrImageUrls;
	}
	
	/**
	 * controller function to replace eq tag with img tag
	 *
	 * @param
	 *        	tagName, str question string
	 * @author Ram Kaushik
	 * @return arrEqTags
	 */
	private function replaceEQTagsWithImage($tagName, $strQuestions) {
		$strWithImgLoc = "";
	
		$arrTagStr = explode ( $tagName, $strQuestions );
	
		for($i = 0; $i < count ( $arrTagStr ); $i ++) {
			if (strpos ( $arrTagStr [$i], ']' )) {
				$arrDataChunk = explode ( ']', $arrTagStr [$i] );
				$strWithImgLoc .= 'equation_tags' . $arrDataChunk [1];
			} else {
				$strWithImgLoc .= $arrTagStr [$i];
			}
		}
		return $strWithImgLoc;
	}
}