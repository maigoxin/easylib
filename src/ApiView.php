<?php
/*
 * creator: maigohuang
 * */

namespace EasyLib;

class ApiView
{
    private $response = null;

    protected static $errorMap = [
        'Undefinition' => [-1000, '未定义错误', 500], //error_code, error_message_format, http_code
    ];

    public function error()
    {
        $args = func_get_args();
        $error = $args[0];

        if (isset(self::$errorMap[$error])) {
            $data = self::$errorMap[$error];
        }else {
            $data = self::$errorMap['Undefinition'];
        }

        $args[0] = $data[1];
        $message = [
            'error_code' => $data[0],
            'error_message' => call_user_func_array('sprintf', $args),
        ];
        return $this->render($message, $data[2]);
    }

    public function render($message, $httpCode = 200)
    {
        return $this->response
            ->withHeader('Content-Type', 'application/json;charset=UTF-8')
            ->withStatus($httpCode)
            ->write(json_encode($message));
    }

    public function __construct($response) 
    {
        $this->response = $response;
    }
}
