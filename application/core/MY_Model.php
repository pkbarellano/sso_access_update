<?php

class MY_Model extends CI_Model
{

    protected $stsDB;
    protected $pfpDB;
    protected $helpdeskDB;

    function __construct()
    {

        parent::__construct();

        $this->stsDB = $this->load->database('stsDB', TRUE, 'stsDB');
        $this->pfpDB = $this->load->database('pfpDB', TRUE, 'pfpDB');
        $this->helpdeskDB = $this->load->database('helpdeskDB', TRUE, 'helpdeskDB');
    }

    protected function _transStart($db = 'db')
    {

        $db = ($db !== 'db') ? $db . 'DB' : $db;

        $this->$db->trans_start();
    }

    protected function _transEnd($db = 'db')
    {

        $db = ($db !== 'db') ? $db . 'DB' : $db;

        $this->$db->trans_complete();

        if ($this->$db->trans_status() === true) {

            $this->$db->trans_commit();

            return true;
        } else {

            $this->$db->trans_rollback();

            return false;
        }
    }

    protected function _transStop($db = 'db')
    {

        $db = ($db !== 'db') ? $db . 'DB' : $db;

        $this->$db->trans_complete();

        $this->$db->trans_rollback();

        return false;
    }
}
