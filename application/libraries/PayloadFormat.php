<?php

class PayloadFormat
{

    public $responseArray;
    public $httpCode;

    public function customResponse($status = FALSE, $message = "", $httpCode = REST_Controller::HTTP_INTERNAL_SERVER_ERROR)
    {

        $this->responseArray = [
            "response" => [
                "status" => $status,
                "details" => [
                    "code" => ($status === TRUE) ? "SUCCESS" : "FAILED",
                    "code_message" => ucfirst(strtolower($message)),
                ]
            ],
        ];

        $this->httpCode = $httpCode;

        return $this->responseArray;
    }
}
