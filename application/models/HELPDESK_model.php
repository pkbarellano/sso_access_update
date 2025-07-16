<?php

class HELPDESK_model extends MY_Model
{

    function getReferenceID($referenceID = "")
    {

        $this->db->select('referenceID')
            ->from('helpdeskReferenceIDs')
            ->where('referenceID', $referenceID);

        return $this->db->get();
    }

    function createReferenceID($referenceID = "")
    {

        $this->_transStart();

        $data = ['referenceID' => $referenceID];

        $this->db->insert('helpdeskReferenceIDs', $data);

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

        if (!is_null($activeEmpNos) && !empty($activeEmpNos)) {

            /** USERS */

            $this->$db->where('rtrim(is_active) !=', '1');
            $this->$db->update_batch('users_tb', $activeEmpNos, 'employee_no');

            /** AGENTS */

            $this->$db->where('rtrim(is_active) !=', '1');
            $this->$db->update_batch('agent_tb', $activeEmpNos, 'employee_no');
        }

        if (!is_null($inactiveEmpNos) && !empty($inactiveEmpNos)) {

            /** USERS */

            $this->$db->where_not_in('rtrim(employee_no)', $inactiveEmpNos);
            $this->$db->where('rtrim(is_active) =', '0');
            $this->$db->or_where("(rtrim(employee_no) IS NULL AND rtrim(is_active) = '1')");
            $this->$db->update('users_tb', ['rtrim(is_active)' => '0']);

            /** AGENTS */

            $this->$db->where_not_in('rtrim(employee_no)', $inactiveEmpNos);
            $this->$db->where('rtrim(is_active) =', '0');
            $this->$db->or_where("(rtrim(employee_no) IS NULL AND rtrim(is_active) = '1')");
            $this->$db->update('agent_tb', ['rtrim(is_active)' => '0']);
        }

        return $this->_transEnd($app);
    }
}
