<?php

if (!function_exists('validateInquiryParameter')) {

    function validateInquiryParameter($postData = null)
    {

        $CI = &get_instance();

        if (!isset($CI->form_validation)) {
            $CI->load->library('form_validation');
        }

        $CI->form_validation->set_rules([
            [
                'field' => 'registryNumber',
                'label' => 'Registry Number',
                'rules' => 'required|min_length[2]|max_length[20]'
            ],
            [
                'field' => 'terminalIPAddress',
                'label' => 'Terminal IP Address',
                'rules' => 'required|min_length[5]|max_length[15]|valid_ip'
            ],
            [
                'field' => 'transactionNumber',
                'label' => 'Transaction Number',
                'rules' => 'required|min_length[5]|max_length[255]'
            ],
            [
                'field' => 'storeCode',
                'label' => 'Store Code',
                'rules' => 'required|min_length[3]|max_length[8]'
            ],
            [
                'field' => 'gcCMNumber',
                'label' => 'GC Number / CM Number',
                'rules' => 'required|min_length[12]|max_length[20]'
            ]
        ]);

        if ($postData !== null) {
            $CI->form_validation->set_data($postData);
        }

        if ($CI->form_validation->run() === FALSE) {

            $errorArray = $CI->form_validation->error_array();

            if (count($errorArray) !== 0) {
                return reset($errorArray);
            } else {
                return "Unknown paramter.";
            }
        } else {
            return TRUE;
        }
    }

    function validateRedeemParameter($postData = null)
    {

        $CI = &get_instance();

        if (!isset($CI->form_validation)) {
            $CI->load->library('form_validation');
        }

        $CI->form_validation->set_rules([
            [
                'field' => 'registryNumber',
                'label' => 'Registry Number',
                'rules' => 'required|min_length[2]|max_length[20]'
            ],
            [
                'field' => 'terminalIPAddress',
                'label' => 'Terminal IP Address',
                'rules' => 'required|min_length[5]|max_length[15]|valid_ip'
            ],
            [
                'field' => 'transactionNumber',
                'label' => 'Transaction Number',
                'rules' => 'required|min_length[5]|max_length[255]'
            ],
            [
                'field' => 'storeCode',
                'label' => 'Store Code',
                'rules' => 'required|min_length[3]|max_length[8]'
            ],
            [
                'field' => 'gcCMNumber',
                'label' => 'GC Number or CM Number',
                'rules' => 'required|min_length[12]|max_length[20]'
            ],
            [
                'field' => 'amount',
                'label' => 'Amount',
                'rules' => 'required|max_length[18]'
            ]
        ]);

        if ($postData !== null) {
            $CI->form_validation->set_data($postData);
        }

        if ($CI->form_validation->run() === FALSE) {

            $errorArray = $CI->form_validation->error_array();

            if (count($errorArray) !== 0) {
                return reset($errorArray);
            } else {
                return "Unknown paramter.";
            }
        } else {
            return TRUE;
        }
    }
}
