<?php
/*
 * creator: maigohuang
 * */

namespace EasyLib;

class OpenApiView
{
    private $response = null;
protected $errorMap = [
        'Undefinition' => ['Undefinition', '未定义错误', 400], //error_code, error_message_format, http_code
    ];

    public function error()
    {
        $args = func_get_args();
        $error = $args[0];

        if (isset($this->errorMap[$error])) {
            $data = $this->errorMap[$error];
        }else {
            $data = $this->errorMap['Undefinition'];
        }

        $args[0] = $data[1];
        $message = [
            'RequestId' => REQUEST_ID,
            'Error' => [
                'Type' => 'Sender',
                'Code' => $data[0],
                'Message' => call_user_func_array('sprintf', $args),
            ]
        ];
        return $this->render(['ErrorResponse' => $message], $data[2]);
    }

    public function response($message, $httpCode = 200)
    {
        $message['RequestId'] = REQUEST_ID;
        return $this->render(['Response' => $message], $httpCode);
    }

    private function render($message, $httpCode = 200)
    {
        Log::Info('request', 'response:' . json_encode($message));
        if (isset($_SERVER['HTTP_ACCEPT']) && strtolower($_SERVER['HTTP_ACCEPT']) == 'application/json') {
            return $this->response
                ->withHeader('Content-Type', 'application/json;charset=UTF-8')
                ->withStatus($httpCode)
                ->write(json_encode(current($message)));
        }else {
            return $this->response
                ->withHeader('Content-Type', 'application/xml;charset=UTF-8')
                ->withStatus($httpCode)
                ->write(Utils::xmlEncode($message));
        }
    }

    public function __construct($response) 
    {
        $this->response = $response;
    }
}
