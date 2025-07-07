<?php

class CoreManagement_controller extends MY_Controller
{

    function __construct()
    {

        parent::__construct();

        $this->load->library('AccessLibrary', NULL, 'AccessLibrary');
        $this->load->library('SSOClient', NULL, 'SSOClient');
    }

    private function _processType($appType = "")
    {

        $status = FALSE;

        $referenceID = $this->_generateReferenceID($appType);

        if ($referenceID !== null) {

            $resp = $this->SSOClient->sendRequest(
                [
                    'sysCode' => $appType,
                    'status' => 'A'
                ],
                [
                    'username' => $this->appConfig['username'],
                    'password' => $this->appConfig['password'],
                    'url' => $this->appConfig['urlBySysCode'],
                ]
            );

            $this->createSysLog($referenceID, $appType, $_SERVER['REQUEST_METHOD'], __CLASS__, __METHOD__, 'CORE', $resp['cURLInfo'], $resp['cURLError']);

            if ($resp['apiStatus'] === TRUE) {

                $update = $this->AccessLibrary->updateStatus($referenceID, $resp['response']['status'], $resp['response']['message'], $resp['response']['data'], $resp['header'], $resp['postFields'], json_encode($resp['cURLResponse']), $appType);

                $status = $update['status'];

                $this->PayloadFormat->customResponse($status, $update['remarks'], $update['httpCode']);
            }
        }

        return $status;
    }

    public function create_post()
    {

        foreach ($this->allowedTypes as $key => $appType) {

            $processType = $this->_processType($appType);

            if ($processType === FALSE) {

                break;
            }
        }

        return $this->response($this->PayloadFormat->responseArray, $this->PayloadFormat->httpCode);
    }
}
