<?php

require(APPPATH . '/libraries/REST_Controller.php');
include_once(APPPATH . 'core/ErrorHandler.php');

class MY_Controller extends REST_Controller
{

    protected $appConfig;
    protected $allowedTypes;

    public function __construct()
    {

        parent::__construct();

        $this->load->library('PayloadFormat', null, 'PayloadFormat');

        $this->appConfig = $this->Common_model->getAppConfig()->row_array();

        $this->allowedTypes = [
            'STS',
            'PFP',
            'HELPDESK',
            'GC',
            'PO_TRACKER'
        ];
    }

    private function _validateType($type)
    {

        return (bool)in_array($type, $this->allowedTypes);
    }

    protected function _generateReferenceID($type = '')
    {

        $referenceID = null;
        $try = 0;

        $this->PayloadFormat->customResponse(FALSE, ErrorHandler::UNKNOWN_ERROR, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        if ($this->_validateType($type) === true) {

            do {

                if ($try == 100) {

                    $this->PayloadFormat->customResponse(FALSE, ErrorHandler::UNABLE_TO_VALIDATE_REFERECE_ID, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);

                    $referenceID = null;

                    break;
                }

                $referenceID = uniqid(strtotime(date('Y-m-d h:i:s')));

                $try++;
            } while ($this->_validateReferenceID($type, $referenceID) === false);

            if ($referenceID !== null) {

                if ($this->_createReferenceID($type, $referenceID) === FALSE) {

                    $referenceID = null;
                }
            }
        } else {

            $this->PayloadFormat->customResponse(FALSE, ErrorHandler::INVALID_REFERECE_ID_TYPE, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $referenceID;
    }

    private function _createReferenceID($type = "", $referenceID = "")
    {

        $model = $type . "_model";

        $this->load->model($model);

        $createReferenceID = $this->$model->createReferenceID($referenceID);

        if ($createReferenceID === false) {

            log_message('error', $createReferenceID);

            $this->PayloadFormat->customResponse(FALSE, ErrorHandler::UNABLE_TO_CREATE_REFERECE_ID, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);

            return FALSE;
        }

        return TRUE;
    }

    private function _validateReferenceID($type = "", $referenceID = "")
    {

        $model = $type . '_model';

        $this->load->model($model);

        $referenceStatus = false;

        $getReferenceID = $this->$model->getReferenceID($referenceID);

        if ($getReferenceID !== FALSE) {

            $referenceStatus = ($getReferenceID->num_rows() == 0) && true;
        } else {

            $referenceStatus = FALSE;

            log_message('error', $getReferenceID);
        }

        return $referenceStatus;
    }

    private function _sysLog(
        $referenceID = '',
        $sysCode = '',
        $httpMethod = '',
        $scriptClass = '',
        $scriptMethod = '',
        $description = '',
        $hcccurlURL = '',
        $hcccurlContentType = '',
        $hccurlHttpCode = '',
        $hccurlHeaderSize = '',
        $hccurlRequestSize = '',
        $hccurlFiletime = '',
        $hccurlSSLVerifyResult = '',
        $hccurlRedirectCount = '',
        $hccurlTotalTime = '',
        $hccurlNameLookupTime = '',
        $hccurlConnectTime = '',
        $hccurlPreTransferTime = '',
        $hccurlSizeUpload = '',
        $hccurlSizeDownload = '',
        $hccurlSpeedDownload = '',
        $hccurlSpeedUpload = '',
        $hccurlDownloadContentLength = '',
        $hccurlUploadContentLength = '',
        $hccurlStartTransferTime = '',
        $hccurlRedirectTime = '',
        $hccurlRedirectUrl = '',
        $hccurlPrimaryIP = '',
        $hccurlPrimaryPort = '',
        $hccurlLocalIp = '',
        $hccurlLocalPort = '',
        $json = '',
        $hccurlError = ''
    ) {

        $this->load->model('SysLog_model');

        $params = [
            'referenceID' => $referenceID,
            'sysCode' => $sysCode,
            'json' => $json,
            'error' => $hccurlError,
            'httpMethod' => $httpMethod,
            'scriptClass' => $scriptClass,
            'scriptMethod' => $scriptMethod,
            'description' => $description,
            'hccurl_URL' => $hcccurlURL,
            'hccurl_contentType' => $hcccurlContentType,
            'hccurl_httpCode' => $hccurlHttpCode,
            'hccurl_headerSize' => $hccurlHeaderSize,
            'hccurl_requestSize' => $hccurlRequestSize,
            'hccurl_filetime' => $hccurlFiletime,
            'hccurl_sslVerifyResult' => $hccurlSSLVerifyResult,
            'hccurl_redirectCount' => $hccurlRedirectCount,
            'hccurl_totalTime' => $hccurlTotalTime,
            'hccurl_namelookupTime' => $hccurlNameLookupTime,
            'hccurl_connectTime' => $hccurlConnectTime,
            'hccurl_preTransferTime' => $hccurlPreTransferTime,
            'hccurl_sizeUpload' => $hccurlSizeUpload,
            'hccurl_sizeDownload' => $hccurlSizeDownload,
            'hccurl_speedDownload' => $hccurlSpeedDownload,
            'hccurl_speedUpload' => $hccurlSpeedUpload,
            'hccurl_downloadContentLength' => $hccurlDownloadContentLength,
            'hccurl_uploadContentLength' => $hccurlUploadContentLength,
            'hccurl_startTransferTime' => $hccurlStartTransferTime,
            'hccurl_redirectTime' => $hccurlRedirectTime,
            'hccurl_redirectUrl' => $hccurlRedirectUrl,
            'hccurl_primaryIp' => $hccurlPrimaryIP,
            'hccurl_primaryPort' => $hccurlPrimaryPort,
            'hccurl_localIp' => $hccurlLocalIp,
            'hccurl_localPort' => $hccurlLocalPort
        ];

        $createLog = $this->SysLog_model->createLog($params);

        if ($createLog == FALSE) {

            log_message('error', $createLog);
        }
    }

    protected function createSysLog($referenceID = '', $sysCode = '', $httpMethod = '', $scriptClass = '', $scriptMethod = '', $description = '', $hccurl = [], $hccurlError = "")
    {

        $this->_sysLog(
            $referenceID,
            $sysCode,
            $httpMethod,
            $scriptClass,
            $scriptMethod,
            $description,
            (isset($hccurl['url'])) ? $hccurl['url'] : '',
            (isset($hccurl['content_type'])) ? $hccurl['content_type'] : '',
            (isset($hccurl['http_code'])) ? $hccurl['http_code'] : '',
            (isset($hccurl['header_size'])) ? $hccurl['header_size'] : '',
            (isset($hccurl['request_size'])) ? $hccurl['request_size'] : '',
            (isset($hccurl['filetime'])) ? $hccurl['filetime'] : '',
            (isset($hccurl['ssl_verify_result'])) ? $hccurl['ssl_verify_result'] : '',
            (isset($hccurl['redirect_count'])) ? $hccurl['redirect_count'] : '',
            (isset($hccurl['total_time'])) ? $hccurl['total_time'] : '',
            (isset($hccurl['namelookup_time'])) ? $hccurl['namelookup_time'] : '',
            (isset($hccurl['connect_time'])) ? $hccurl['connect_time'] : '',
            (isset($hccurl['pretransfer_time'])) ? $hccurl['pretransfer_time'] : '',
            (isset($hccurl['size_upload'])) ? $hccurl['size_upload'] : '',
            (isset($hccurl['size_download'])) ? $hccurl['size_download'] : '',
            (isset($hccurl['speed_download'])) ? $hccurl['speed_download'] : '',
            (isset($hccurl['speed_upload'])) ? $hccurl['speed_upload'] : '',
            (isset($hccurl['download_content_length'])) ? $hccurl['download_content_length'] : '',
            (isset($hccurl['upload_content_length'])) ? $hccurl['upload_content_length'] : '',
            (isset($hccurl['starttransfer_time'])) ? $hccurl['starttransfer_time'] : '',
            (isset($hccurl['redirect_time'])) ? $hccurl['redirect_time'] : '',
            (isset($hccurl['redirect_url'])) ? $hccurl['redirect_url'] : '',
            (isset($hccurl['primary_ip'])) ? $hccurl['primary_ip'] : '',
            (isset($hccurl['primary_port'])) ? $hccurl['primary_port'] : '',
            (isset($hccurl['local_ip'])) ? $hccurl['local_ip'] : '',
            (isset($hccurl['local_port'])) ? $hccurl['local_port'] : '',
            json_encode($hccurl),
            $hccurlError
        );
    }
}
