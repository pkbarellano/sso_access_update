<?php

class CoreManagement_model extends MY_Model
{

    function getReferenceID($referenceID = "", $table = "")
    {

        if ($this->_createReferenceIDTable($table) !== FALSE) {

            $this->db->select('referenceID')
                ->from($table)
                ->where('referenceID =', $referenceID);

            return $this->db->get();
        } else {

            return FALSE;
        }
    }

    private function _createReferenceIDTable($table = "")
    {

        $query = "
            IF NOT EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[{$table}]') AND type IN ('U'))
                CREATE TABLE [dbo].[{$table}] (
                    [id] int  IDENTITY(1,1) NOT NULL,
                    [referenceID] varchar(255) COLLATE Latin1_General_CI_AI  NOT NULL,
                    [createdAt] datetime DEFAULT (getdate()) NOT NULL,
                    [deletedAt] datetime  NULL
                )
        ";

        return $this->db->query($query);
    }

    function createReferenceID($referenceID = "", $table = "")
    {

        $this->_transStart();

        $data = ['referenceID' => $referenceID];

        $this->db->insert($table, $data);

        return $this->_transEnd();
    }

    function createRequest($referenceID = '', $header = [], $postFields = [], $jsonHeader = '', $jsonPostFields = '', $table = '')
    {

        if ($this->_createRequestLogTable($table) !== FALSE) {

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

            $this->db->insert($table, $merged);

            return $this->_transEnd();
        } else {

            return FALSE;
        }
    }

    private function _createRequestLogTable($table = '')
    {

        $query = "
            IF NOT EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[{$table}]') AND type IN ('U'))
                CREATE TABLE [dbo].[{$table}] (
                    [id] int  IDENTITY(1,1) NOT NULL,
                    [referenceID] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [header0] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [header1] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [header2] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [sysCode] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [status] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [headerJson] text COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [bodyJson] text COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [createdAt] datetime DEFAULT (getdate()) NULL,
                    [updatedAt] datetime  NULL,
                    [deletedAt] datetime  NULL
                )
        ";

        return $this->db->query($query);
    }

    function createResponse($referenceID = '', $status = 0, $message = '', $data = '', $json = '', $table = '')
    {

        if ($this->_createResponseLogTable($table) !== FALSE) {

            $this->_transStart();

            $data = [
                'referenceID' => $referenceID,
                'status' => $status,
                'message' => $message,
                'dataArray' => $data,
                'json' => $json
            ];

            $this->db->insert($table, $data);

            return $this->_transEnd();
        } else {

            return FALSE;
        }
    }

    private function _createResponseLogTable($table = '')
    {

        $query = "
            IF NOT EXISTS (SELECT * FROM sys.all_objects WHERE object_id = OBJECT_ID(N'[dbo].[{$table}]') AND type IN ('U'))
                CREATE TABLE [dbo].[{$table}] (
                    [id] int  IDENTITY(1,1) NOT NULL,
                    [referenceID] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [status] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [message] varchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [dataArray] text COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [json] text COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
                    [createdAt] datetime DEFAULT (getdate()) NULL,
                    [updatedAt] datetime  NULL,
                    [deletedAt] datetime  NULL
                )
        ";

        return $this->db->query($query);
    }

    function udpateAccess($data = [], $dbConfig = [], $tableConfig = [])
    {

        $this->_createConnection($dbConfig);

        $this->_transStart(TRUE);

        $employeeNumberColumn = $tableConfig['employeeNumberColumn'];
        $statusColumn = $tableConfig['statusColumn'];
        $activeValue = $tableConfig['activeValue'];
        $inactiveValue = $tableConfig['inactiveValue'];
        $tableName = $tableConfig['tableName'];

        if (is_array($data) && !is_null($data) && !empty($data)) {

            $activeEmployees = array_map(function ($item) use ($employeeNumberColumn, $statusColumn, $activeValue) {
                return [
                    $employeeNumberColumn => $item->empNo,
                    $statusColumn => $activeValue
                ];
            }, $data);

            $inactiveEmployees = array_map(function ($item) {
                return $item->empNo;
            }, $data);

            $this->dynamicDb->where("{$statusColumn} !=", $activeValue)
                ->update_batch($tableName, $activeEmployees, $employeeNumberColumn);

            $this->dynamicDb->where_not_in($employeeNumberColumn, $inactiveEmployees)
                ->where("{$statusColumn} =", $activeValue)
                ->or_where("({$employeeNumberColumn} IS NULL AND {$statusColumn} = '{$activeValue}')")
                ->update($tableName, [$statusColumn => $inactiveValue]);
        } else {

            $this->dynamicDb->where("{$statusColumn} =", $activeValue)
                ->or_where("({$employeeNumberColumn} IS NULL AND {$statusColumn} = '{$activeValue}')")
                ->update($tableName, [$statusColumn => $inactiveValue]);
        }

        $status = $this->_transEnd(TRUE);

        $this->_closeConnection();

        return $status;
    }
}
