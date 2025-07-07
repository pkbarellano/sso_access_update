<?php

class SSOClient
{

    public function sendRequest($payload = [], $appConfig = [], $method = 'POST')
    {

        $header = $this->_header($appConfig);

        $postFields = $this->_postFields($payload);

        $request = $this->_body($appConfig, $header, $postFields, $method);

        return $request;
    }

    private function _header($appConfig = [])
    {

        return [
            'Cache-Control: no-cache',
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($appConfig['username'] . ':' . $appConfig['password']),
        ];
    }

    private function _postFields($payload = [])
    {

        return [
            'sysCode' => $payload['sysCode'],
            'status' => $payload['status']
        ];
    }

    private function _body($appConfig = [], $header = [], $postFields = [], $method = 'POST')
    {

        $body = [
            CURLOPT_URL => $appConfig['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => $header,
        ];

        return $this->_cURLExec($body, $header, $postFields);
    }

    private function _cURLExec($body = [], $header = [], $postFields = [])
    {

        $cURL = curl_init();

        curl_setopt_array($cURL, $body);

        $responseCURL = json_decode(curl_exec($cURL));

        $responseInfo = curl_getinfo($cURL);

        $error = curl_error($cURL);

        curl_close($cURL);

        $apiStatus = FALSE;
        $apiRemarks = "Unprocessable entity.";
        $response = [
            'status' => 0,
            'message' => '',
            'data' => []
        ];

        if ($responseInfo['http_code'] == 200) {

            if (is_object($responseCURL) && property_exists($responseCURL, 'response')) {

                $apiStatus = TRUE;

                $apiRemarks = "";

                $resp = $responseCURL->response;

                $response = [
                    'status' => (is_object($resp) && property_exists($resp, 'status')) ? $resp->status : 0,
                    'message' => (is_object($resp) && property_exists($resp, 'message')) ? $resp->message : '',
                    'data' => (is_object($resp) && property_exists($resp, 'data') && is_array($resp->data)) ? $resp->data : []
                ];
            }
        }

        if (!empty($error) && $error !== '') {

            log_message('ERROR', $error);
        }

        return [
            'apiStatus' => $apiStatus,
            'apiRemarks' => $apiRemarks,
            'response' => $response,
            'cURLResponse' => $responseCURL,
            'cURLInfo' => $responseInfo,
            'cURLError' => $error,
            'header' => $header,
            'postFields' => $postFields
        ];
    }
}
