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
}
