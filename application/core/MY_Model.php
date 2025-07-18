<?php

class MY_Model extends CI_Model
{

    public $dynamicDb;

    function __construct()
    {

        parent::__construct();
    }

    protected function _transStart($dynamicDb = FALSE)
    {

        $db = ($dynamicDb === TRUE) ? 'dynamicDb' : 'db';

        $this->$db->trans_start();
    }

    protected function _transEnd($dynamicDb = FALSE)
    {

        $db = ($dynamicDb === TRUE) ? 'dynamicDb' : 'db';

        $this->$db->trans_complete();

        if ($this->$db->trans_status() === true) {

            $this->$db->trans_commit();

            return true;
        } else {

            $this->$db->trans_rollback();

            return false;
        }
    }

    protected function _transStop($dynamicDb = FALSE)
    {

        $db = ($dynamicDb === TRUE) ? 'dynamicDb' : 'db';

        $this->$db->trans_complete();

        $this->$db->trans_rollback();

        return false;
    }

    protected function _createConnection($config = [])
    {

        $db = [
            'dsn'    => '',
            'hostname' => $config['hostname'],
            'username' => $config['username'],
            'password' => $config['password'],
            'database' => $config['database'],
            'dbdriver' => $config['driver'],
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => FALSE,
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => FALSE,
            'compress' => FALSE,
            'stricton' => FALSE,
            'failover' => array(),
            'save_queries' => TRUE
        ];

        $this->dynamicDb = $this->load->database($db, TRUE);
    }

    protected function _closeConnection()
    {

        $this->dynamicDb->close();
    }
}
