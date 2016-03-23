<?php

/**
 * 
 */
class Application_Service_LeaderBoard
{

    protected $_db;
    protected $_length = 50;

    public function __construct()
    {
        $this->_db = $db = new Application_Model_DbTable_ChildGradePoints();
    }

    public function get($data)
    {

        $data['type'] = empty($data['type']) ? NULL : $data['type'];
        $data['child_id'] = empty($data['child_id']) ? NULL : $data['child_id'];
        $data['position'] = empty($data['position']) ? 0 : $data['position'];

        switch ($data['type']) {
            case 'today':
                $board = $this->getTodayBoard($data['child_id'], $data['position']);
                break;

            case 'weekly':
                $board = $this->getWeeklyBoard($data['child_id'], $data['position']);
                break;

            default:
                $board = $this->getOverallBoard($data['child_id'], $data['position']);
        }

        if (!$board->count()) {
            $return = array(
                'message' => 'no data available',
                'status_code' => STATUS_ERROR
            );

            return $return;
        }

        $board = $this->formatData($board->toArray());
        $return = array(
            'data' => $board,
            'status_code' => STATUS_SUCCESS
        );

        return $return;
    }

    public function getTodayBoard($childId, $position)
    {
        return $this->_db->getTodayBoard($childId, $position, $this->_length);
    }

    public function getWeeklyBoard($childId, $position)
    {
        return $this->_db->getWeeklyBoard($childId, $position, $this->_length);
    }

    public function getOverallBoard($childId, $position)
    {
        return $this->_db->getOverallBoard($childId, $position, $this->_length);
    }

    public function formatData($data)
    {
        $serverUrl = new Zend_View_Helper_ServerUrl();
        $baseUrl = new Zend_View_Helper_BaseUrl();

        foreach ($data as &$row) {
            $row['name'] = html_entity_decode($row['name']);

            if ($row['coppa_required']) {
                $row['image'] = NULL;
            } else if (empty($row['image'])) {
                $row['image'] = $serverUrl->serverUrl() . $baseUrl->baseUrl('/images/no-image-child.png');
            } else {
                $row['image'] = AWS_S3_URL . 'child/' . $row['image'];
            }

            unset($row['gender']);
            unset($row['coppa_required']);
            unset($row['coppa_accepted']);
        }

        return $data;
    }

}
