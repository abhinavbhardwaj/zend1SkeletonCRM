<?php

class Application_Model_Trophy extends Zend_Loader_Autoloader
{

    /**
     * @desc Function to get trophies of child
     * @param childId,$dateSearch
     * @author suman khatri on 18th November 2013
     * @return ArrayIterator
     */
    public function getTrophiesOfChild($childId, $dateSearch = NULL, $dateEnd = NULL)
    {
        $tblchildTrophy = new Application_Model_DbTable_ChildTrophy ();
        //fetches trophies of child 
        $childTrophy = $tblchildTrophy->getChildTrophyData($childId, $dateSearch, $dateEnd);
        return $childTrophy; //returns array
    }

    /**
     * Get all trophies add for a particular grade
     * 
     * @param int $grade
     * @return Zend_Db_Table_Rowset
     */
    public function getTrophyData($grade, $getQuery = FALSE)
    {
        $dbTO = new Application_Model_DbTable_Trophies();
        $query1 = $dbTO->select()->setIntegrityCheck(false)->from($dbTO, '');
        $query1->columns(array('trophy_id as id', 'title', new Zend_Db_Expr('"TO" as type'), 'description', 'image', 'points', new Zend_Db_Expr('null as streak'), new Zend_Db_Expr('null as subject_id')));

        $dbTS = new Application_Model_DbTable_Trophiessub();
        $query2 = $dbTS->select()->setIntegrityCheck(false)->from($dbTS, '');
        $query2->columns(array('subject_trophy_id as id', 'title', new Zend_Db_Expr('"TS" as type'), 'description', 'image', 'points', new Zend_Db_Expr('null as streak'), 'subject_id'));
        if (!empty($grade)) {
            $query2->where("grade_id = ?", $grade);
        }

        $dbSO = new Application_Model_DbTable_Streak();
        $query3 = $dbSO->select()->setIntegrityCheck(false)->from($dbSO, '');
        $query3->columns(array('streak_trophy_id as id', 'title', new Zend_Db_Expr('"SO" as type'), 'description', 'image', new Zend_Db_Expr('null as points'), 'streak', new Zend_Db_Expr('null as subject_id')));

        $dbSS = new Application_Model_DbTable_Streaksub();
        $query4 = $dbSS->select()->setIntegrityCheck(false)->from($dbSS, '');
        $query4->columns(array('streak_subject_trophy_id as id', 'title', new Zend_Db_Expr('"SS" as type'), 'description', 'image', new Zend_Db_Expr('null as points'), 'streak', 'subject_id'));

        $query = $dbTO->select()->union(array($query1, $query2, $query3, $query4));
        if ($getQuery) {
            return $query;
        }

        return $dbTO->fetchAll($query);
    }

    /**
     * Send push to app with all trophy data
     * @param array $trophyArray
     * @param string $type
     * @param int $trophyId
     * @param string $processCode
     * @param int $parentId
     */
    public function sendPushOfTrophy($trophyArray, $type, $trophyId, $processCode, $parentId = null)
    {
        $objParent = new Application_Model_Parents();
        $dataTrophyList = $this->formatTrophyArray($trophyArray, $type, $trophyId, $processCode);
        $trophyArrayForPush['trophy_list'] = $dataTrophyList;
        if ($processCode != 'child trophy') {
            $trophyArrayForPush['trophyId'] = $trophyId;
            $trophyArrayForPush['type'] = $type;
        } else {
            $trophyArrayForPush['trophyId'] = null;
            $trophyArrayForPush['type'] = null;
        }
        $sendNotificationData = array(
            'process_code' => $processCode,
            'data' => $trophyArrayForPush
        );

        if (isset($trophyArrayForPush['type']) && $trophyArrayForPush['type'] == 'TS') {
            $sendNotificationData['child_grade'] = $trophyArray[0]['grade_id'];
        }
        $objParent->sendPushToAllDevices($parentId, $sendNotificationData, null);
    }

    /**
     * Format trophy data for push notification
     * @param array $trophyArray
     * @param string $type
     * @param int $trophyId
     * @param string $processCode
     * @return Array
     */
    public function formatTrophyArray($trophyArray, $type, $trophyId, $processCode)
    {
        if ($processCode != 'delete trophy') {
            $i = 0;
            foreach ($trophyArray as $trophyDataArray) {
                if ($processCode != 'child trophy') {
                    $dataTrophyList[$i]['id'] = $trophyId;
                } else {
                    $dataTrophyList[$i]['id'] = null;
                    $dataTrophyList[$i]['child_id'] = $trophyDataArray['child_id'];
                    $dataTrophyList[$i]['trophy_id'] = $trophyDataArray['trophy_id'];
                    $dataTrophyList[$i]['awarded_date'] = date('Y-m-d H:i:s', strtotime($trophyDataArray['awarded_date']));
                    $dataTrophyList[$i]['grade_id'] = $trophyDataArray['grade_id'];
                    $dataTrophyList[$i]['counter'] = $trophyDataArray['counter'] . "";
                }
                $dataTrophyList[$i]['title'] = $trophyDataArray['title'];
                if (!empty($type)) {
                    $dataTrophyList[$i]['type'] = $type;
                    $typeOfTrophy = $type;
                } else {
                    $dataTrophyList[$i]['type'] = $trophyDataArray['type'];
                    $typeOfTrophy = $trophyDataArray['type'];
                }

                $dataTrophyList[$i]['description'] = $trophyDataArray['description'];

                $serverUrl = new Zend_View_Helper_ServerUrl();
                $baseUrl = new Zend_View_Helper_BaseUrl();
                $dataTrophyList[$i]['image'] = AWS_S3_URL . 'trophy/' . $trophyDataArray['image'];
                if ($typeOfTrophy == 'TO') {
                    $dataTrophyList[$i]['points'] = $trophyDataArray['points'];
                } else {
                    $dataTrophyList[$i]['points'] = null;
                }
                if ($typeOfTrophy == 'TS') {
                    $dataTrophyList[$i]['subject_id'] = $trophyDataArray['subject_id'];
                    $dataTrophyList[$i]['points'] = $trophyDataArray['points'];
                } else {
                    $dataTrophyList[$i]['subject_id'] = null;
                }
                if ($typeOfTrophy == 'SO') {
                    $dataTrophyList[$i]['streak'] = $trophyDataArray['streak'];
                } else {
                    $dataTrophyList[$i]['streak'] = null;
                }
                if ($typeOfTrophy == 'SS') {
                    $dataTrophyList[$i]['subject_id'] = $trophyDataArray['subject_id'];
                    $dataTrophyList[$i]['streak'] = $trophyDataArray['streak'];
                } else {
                    if ($dataTrophyList[$i]['subject_id'] == '') {
                        $dataTrophyList[$i]['subject_id'] = null;
                    } else {
                        $dataTrophyList[$i]['subject_id'] = $dataTrophyList[$i]['subject_id'];
                    }
                }
                $i++;
            }
        } else {
            $i = 0;
            $dataTrophyList[$i]['id'] = $trophyId;
            $dataTrophyList[$i]['type'] = $type;
            $dataTrophyList[$i]['title'] = null;
            $dataTrophyList[$i]['description'] = null;
            $dataTrophyList[$i]['image'] = null;
            $dataTrophyList[$i]['points'] = null;
            $dataTrophyList[$i]['subject_id'] = null;
            $dataTrophyList[$i]['streak'] = null;
        }
        return $dataTrophyList;
    }

    /**
     * Award trophy to kid
     * @param Array $tr trophy details
     * @param string $type {To,TS,SO,SS}
     * @param int $childId
     * @param int $gradId
     * @return type
     */
    public function addTrophyForKid($tr, $type, $childId, $gradId)
    {
        $tblchildTrophy = new Application_Model_DbTable_ChildTrophy ();
        $trophydata = array(
            'child_id' => $childId,
            'grade_id' => $gradId,
            'title' => $tr['title'],
            'description' => $tr['description'],
            'image' => $tr['image'],
            'type' => $type,
            'awarded_date' => date('Y-m-d H:i:s')
        );
        if ($type == 'TO') {
            $trophydata['trophy_id'] = $tr['trophy_id'];
            $trophydata['points'] = $tr['points'];
        }
        if ($type == 'TS') {
            $trophydata['trophy_id'] = $tr['subject_trophy_id'];
            $trophydata['subject_id'] = $tr['subject_id'];
            $trophydata['points'] = $tr['points'];
        }
        if ($type == 'SO') {
            $trophydata['trophy_id'] = $tr['streak_trophy_id'];
            $trophydata['streak'] = $tr['streak'];
        }
        if ($type == 'SS') {
            $trophydata['trophy_id'] = $tr['streak_subject_trophy_id'];
            $trophydata['points'] = $tr['points'];
            $trophydata['streak'] = $tr['streak'];
        }

        $addTrophy = $tblchildTrophy->AddTrophy($trophydata);
        return $addTrophy;
    }

    /**
     * Get trophy count per subject
     * @param Int $childId
     * @return Zend_Db_Table_Rowset
     */
    public function getTrophyCountPerSubject($childId, $fromDate, $toDate)
    {
        $tblchildTrophy = new Application_Model_DbTable_ChildTrophy ();
        return $tblchildTrophy->getTrophyCountPerSubject($childId, $fromDate, $toDate);
    }

    /**
     * Function to get trophies of child
     * Both loked and unlocked with status
     * 
     * @param int $childId
     * @return Zend_Db_Table_Rowset
     * 
     */
    public function getAllTrophiesOfChild($childId, $gradeId, $fromDate, $toDate)
    { 
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        // get all the trophies for child's grade
        $allTrophiesQuery = $db->select()->distinct();
        $allTrophiesQuery->from(array('trophy' => $this->getTrophyData($gradeId, TRUE)));
        $allTrophiesQuery->columns(array(
            new Zend_Db_Expr('0 as is_unlocked'),
            new Zend_Db_Expr('null as awarded_date'),
            new Zend_Db_Expr($gradeId . ' as grade_id'),
            new Zend_Db_Expr('null as counter')
        ));

        // get trophies achieved by kid
        $childTrophyQuery = $db->select()->distinct();
        $childTrophyQuery->from(array('child_trophy' => 'bal_child_trophies'), '');
        $childTrophyQuery->where('child_trophy.child_id = ?', $childId);
        $childTrophyQuery->columns(array(
            'trophy_id as id', 'title', 'type', 'description',
            'image', 'points', 'streak', 'subject_id', new Zend_Db_Expr('1 as is_unlocked'),
            'awarded_date', 'grade_id', 'counter'
        ));

        // get unioun of both data
        $query = $db->select();
        $query->from(array('trophies' => $db->select()->union(array($childTrophyQuery, $allTrophiesQuery))));
        $query->joinLeft(array('subject' => 'bal_subjects'), 'subject.subject_id = trophies.subject_id', 'subject_name');
        if ($fromDate != null && $toDate != null) {
            $from = date('Y-m-d H:i:s', strtotime($fromDate));
            $to = date('Y-m-d 23:59:59', strtotime($toDate));
            $query->where("awarded_date >= '$from' AND awarded_date < '$to' OR is_unlocked != 1");
        }
        $query->group(array('id', 'type', 'grade_id'));
        $query->columns('SUM(is_unlocked) as is_unlocked');
        $query->order('awarded_date DESC');

        $trophies = $db->fetchAll($query);
        return $trophies;
    }

}
