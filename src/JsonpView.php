<?php
/*
 * creator: maigohuang
 * */

namespace EasyLib;

class JsonpView
{
    private $response = null;

    protected $errorMap = [
        'Undefinition' => [-1000, '未定义错误', 500], //error_code, error_message_format, http_code
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
            'errno' => $data[0],
            'errmsg' => call_user_func_array('sprintf', $args),
        ];
        Log::error($message['errmsg']);
        return $this->render($message, $data[2]);
    }

    public function render($message, $httpCode = 200)
    {
        Log::Info('request', 'response:' . json_encode($message));
        $callback = isset($_GET['_callback']) ? $_GET['_callback'] : 'callback';
        return $this->response
            ->withHeader('Content-Type', 'text/javascript;charset=UTF-8')
            ->withStatus($httpCode)
            ->write($callback.'('.json_encode($message).')');
    }

    public function __construct($response) 
    {
        $this->response = $response;
    }
}
