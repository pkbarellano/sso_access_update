<?php

class PFP_model extends MY_Model
{

    function getReferenceID($referenceID = "")
    {

        $this->db->select('referenceID')
            ->from('pfpReferenceIDs')
            ->where('referenceID', $referenceID);

        return $this->db->get();
    }

    function createReferenceID($referenceID = "")
    {

        $this->_transStart();

        $data = ['referenceID' => $referenceID];

        $this->db->insert('pfpReferenceIDs', $data);

        return $this->_transEnd();
    }

    function createRequest($referenceID = '', $header = [], $postFields = [], $jsonHeader = '', $jsonPostFields = '', $app = '')
    {

        $this->_transStart();

        $headerKeys = [
            'header0',
            'header1',
            'header2'
        ];

        if (is_array($header)) {

            $setHeader = array_combine($headerKeys, $header);
        }

        $data = [
            'referenceID' => $referenceID,
            'headerJson' => $jsonHeader,
            'bodyJson' => $jsonPostFields
        ];

        $merged = array_merge($data, $setHeader, $postFields);

        $this->db->insert($app . 'Requests', $merged);

        return $this->_transEnd();
    }

    function createResponse($referenceID = '', $status = 0, $message = '', $data = '', $json = '', $app)
    {

        $this->_transStart();

        $data = [
            'referenceID' => $referenceID,
            'status' => $status,
            'message' => $message,
            'dataArray' => $data,
            'json' => $json
        ];

        $this->db->insert($app . 'Responses', $data);

        return $this->_transEnd();
    }

    function updateAccess($activeEmpNos = [], $inactiveEmpNos = [], $app)
    {

        $this->_transStart($app);

        $db = $app . 'DB';

        $this->$db->where('userStat !=', 'A');
        $this->$db->update_batch('tblUsers', $activeEmpNos, 'empNo');

        $this->$db->where_not_in('empNo', $inactiveEmpNos);
        $this->$db->where('userStat =', 'A');
        $this->$db->or_where("(empNo IS NULL AND userStat = 'A')");
        $this->$db->update('tblUsers', ['userStat' => 'D']);

        return $this->_transEnd($app);
    }
}
