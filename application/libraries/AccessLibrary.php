<?php

class AccessLibrary
{

    protected $CI;
    protected $status = FALSE;
    protected $remarks = "";
    protected $httpCode = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;

    function __construct()
    {

        $this->CI = &get_instance();
    }

    private function _createRequest($referenceID = '', $header = [], $postFields = [], $table = '')
    {

        $this->CI->load->model('CoreManagement_model');

        $createRequest = $this->CI->CoreManagement_model->createRequest($referenceID, $header, $postFields, json_encode($header), json_encode($postFields), $table);

        if ($createRequest === FALSE) {

            log_message('ERROR', $createRequest);

            $this->status = FALSE;
        } else {

            $this->status = TRUE;
        }

        return;
    }

    private function _createResponse($referenceID = '', $status = 0, $message = "", $data = [], $jsonResponse = '', $table = '')
    {

        $this->CI->load->model('CoreManagement_model');

        $createResponse = $this->CI->CoreManagement_model->createResponse($referenceID, $status, $message, json_encode($data), $jsonResponse, $table);

        if ($createResponse === FALSE) {

            log_message('ERROR', $createResponse);

            $this->status = FALSE;
        } else {

            $this->status = TRUE;
        }

        return;
    }

    private function _updateAccess($data = [], $dbConfig = [], $tableConfig = [])
    {

        $this->CI->load->model('CoreManagement_model');

        $this->status = $this->CI->CoreManagement_model->updateAccess($data, $dbConfig, $tableConfig);

        if ($this->status === TRUE) {

            $this->remarks = "Employee access update was completed.";

            $this->httpCode = REST_Controller::HTTP_OK;
        } else {

            log_message('ERROR', $this->status);

            $this->remarks = "Failed to update " . $dbConfig['applicationName'] . " employee access.";

            $this->httpCode = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
        }
    }

    public function updateStatus($referenceID = '', $status = 0, $message = '', $data = [], $header = [], $postFields = [], $jsonResponse = '', $dbConfig = [], $tableConfig = [])
    {

        $this->_createRequest($referenceID, $header, $postFields, $tableConfig['tableLogRequest']);

        $this->_createResponse($referenceID, $status, $message, $data, $jsonResponse, $tableConfig['tableLogResponse']);

        $this->_updateAccess($data, $dbConfig, $tableConfig);

        return [
            'status' => $this->status,
            'remarks' => $this->remarks,
            'httpCode' => $this->httpCode
        ];
    }
}
