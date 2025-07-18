<?php

class CoreManagement_controller extends MY_Controller
{

    function __construct()
    {

        parent::__construct();

        $this->load->library('AccessLibrary', NULL, 'AccessLibrary');
        $this->load->library('SSOClient', NULL, 'SSOClient');
    }

    private function _processApp($dbConfig = [], $tableConfig = [])
    {

        $status = FALSE;

        $referenceID = $this->_generateReferenceID($tableConfig['tableLogReferenceID']);

        if ($referenceID !== null) {

            $resp = $this->SSOClient->sendRequest(
                [
                    'sysCode' => $dbConfig['systemCode'],
                    'status' => $dbConfig['systemStatus']
                ],
                [
                    'username' => $this->appConfig['username'],
                    'password' => $this->appConfig['password'],
                    'url' => $this->appConfig['urlBySysCode']
                ]
            );

            $this->createSysLog($referenceID, $dbConfig['applicationName'], $dbConfig['systemCode'], $_SERVER['REQUEST_METHOD'], __CLASS__, __METHOD__, 'CORE', $resp['cURLInfo'], $resp['cURLError']);

            if ($resp['apiStatus'] === TRUE) {

                $update = $this->AccessLibrary->updateStatus($referenceID, $resp['response']['status'], $resp['response']['message'], $resp['response']['data'], $resp['header'], $resp['postFields'], json_encode($resp['cURLResponse']), $dbConfig, $tableConfig);

                $status = $update['status'];

                $this->PayloadFormat->customResponse($status, $update['remarks'], $update['httpCode']);
            }
        }

        return $status;
    }

    public function create_post()
    {

        $configCount = $this->dbConfig->num_rows();

        if ($configCount > 0) {

            $configuration = $this->dbConfig->result_array();

            foreach ($configuration as $config) {

                $getTables = $this->Common_model->getDatabaseConnectionTable($config['id']);

                $tablesCount = $getTables->num_rows();

                if ($tablesCount > 0) {

                    $tables = $getTables->result_array();

                    foreach ($tables as $table) {

                        $this->_processApp($config, $table);
                    }
                }
            }
        } else {

            $this->PayloadFormat->customresponse(FALSE, 'No database configuration available.', REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->response($this->PayloadFormat->responseArray, $this->PayloadFormat->httpCode);
    }
}
