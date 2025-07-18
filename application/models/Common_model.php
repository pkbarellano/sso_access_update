<?php

class Common_model extends MY_Model
{

    function getAppConfig()
    {

        $this->db->select([
            'username',
            'password',
            'urlBySysCode'
        ])
            ->from('appConfig');

        return $this->db->get();
    }

    function getDatabaseConnection()
    {

        $this->db->select([
            'id',
            'applicationName',
            'systemCode',
            'systemStatus',
            'hostname',
            'database',
            'username',
            'password',
            'driver'
        ])
            ->from('dbConn')
            ->where("deletedAt IS NULL OR deletedAt = ''");

        return $this->db->get();
    }

    function getDatabaseConnectionTable($id = 0)
    {

        $this->db->select([
            'tableName',
            'employeeNumberColumn',
            'statusColumn',
            'activeValue',
            'inactiveValue',
            'tableLogReferenceID',
            'tableLogRequest',
            'tableLogResponse'
        ])
            ->from('dbConnTables')
            ->where("(deletedAt IS NULL OR deletedAt = '')")
            ->where('dbConnID =', $id);

        return $this->db->get();
    }
}
