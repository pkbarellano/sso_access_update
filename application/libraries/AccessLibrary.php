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

    private function _createRequest($referenceID = '', $header = [], $postFields = [], $app = '')
    {

        $app = strtoupper($app);

        $model = $app . '_model';

        $this->CI->load->model($model);

        $createRequest = $this->CI->$model->createRequest($referenceID, $header, $postFields, json_encode($header), json_encode($postFields), strtolower($app));

        if ($createRequest === FALSE) {

            log_message('ERROR', $createRequest);

            $this->status = FALSE;
        } else {

            $this->status = TRUE;
        }

        return;
    }

    private function _createResponse($referenceID = '', $status = 0, $message = "", $data = [], $jsonResponse = '', $app = '')
    {

        $app = strtoupper($app);

        $model = $app . "_model";

        $this->CI->load->model($model);

        $createResponse = $this->CI->$model->createResponse($referenceID, $status, $message, json_encode($data), $jsonResponse, strtolower($app));

        if ($createResponse === FALSE) {

            log_message('ERROR', $createResponse);

            $this->status = FALSE;
        } else {

            $this->status = TRUE;
        }

        return;
    }

    private function _updateAccess($data = [], $app = '')
    {

        $app = strtoupper($app);

        $model = $app . "_model";

        $this->CI->load->model($model);

        $inactiveEmployees = array_map(function ($item) {
            return $item->empNo;
        }, $data);

        switch ($app) {
            case 'STS':

                $activeEmployees = array_map(function ($item) {
                    return [
                        'empNo' => $item->empNo,
                        'userStat' => 'A'
                    ];
                }, $data);

                break;
            case 'PFP':

                $activeEmployees = array_map(function ($item) {
                    return [
                        'empNo' => $item->empNo,
                        'userStat' => 'A'
                    ];
                }, $data);

                break;

            case 'HELPDESK':

                $activeEmployees = array_map(function ($item) {
                    return [
                        'employee_no' => $item->empNo,
                        'is_active' => '1'
                    ];
                }, $data);

                break;

            case 'GC':

                $activeEmployees = array_map(function ($item) {
                    return [
                        'empNo' => $item->empNo,
                        'userStat' => 'A'
                    ];
                }, $data);

                break;

            case 'PO_TRACKER':

                $activeEmployees = array_map(function ($item) {
                    return [
                        'empNo' => $item->empNo,
                        'userStat' => 'A'
                    ];
                }, $data);

                break;
            default:

                $activeEmployees = null;

                break;
        }

        $this->status = $this->CI->$model->updateAccess($activeEmployees, $inactiveEmployees, strtolower($app));

        if ($this->status === TRUE) {

            $this->remarks = "Employee access update was completed.";

            $this->httpCode = REST_Controller::HTTP_OK;
        } else {

            log_message('ERROR', $this->status);

            $this->remarks = "Failed to update " . $app . " employee access.";

            $this->httpCode = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
        }
    }

    public function updateStatus($referenceID = '', $status = 0, $message = '', $data = [], $header = [], $postFields = [], $jsonResponse = '', $app = '')
    {

        $this->_createRequest($referenceID, $header, $postFields, $app);

        $this->_createResponse($referenceID, $status, $message, $data, $jsonResponse, $app);

        $this->_updateAccess($data, $app);

        return [
            'status' => $this->status,
            'remarks' => $this->remarks,
            'httpCode' => $this->httpCode
        ];
    }
}
