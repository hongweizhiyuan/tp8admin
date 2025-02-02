<?php

namespace app\common\controller;

use app\BaseController;
use think\App;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\Response;

/**
 * API控制器基础类
 */
class Api extends BaseController
{
    /**
     * 默认响应输出类型，支持json/xml/jsonp
     */
    protected string $responseType = 'json';

    /**
     * 应用站点系统设置
     * @var bool
     */
    protected bool $useSystemSettings = true;

    /**
     * 构造方法
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 控制器初始化方法
     */
    protected function initialize(): void
    {
        parent::initialize();
    }

    /**
     * 操作成功
     * @param string $msg 提示消息
     * @param mixed $data 返回数据
     * @param int $code 错误码
     * @param string|null $type 输出类型
     * @param array $header 发送的 header 信息
     * @param array $options Response 输出参数
     */
    protected function success(string $msg = '', mixed $data = null, int $code = 1, string $type = null, array $header = [], array $options = [])
    {
        $this->result($msg, $data, $code, $type, $header, $options);
    }

    /**
     * 操作失败
     * @param string $msg 提示消息
     * @param mixed $data 返回数据
     * @param int $code 错误码
     * @param string|null $type 输出类型
     * @param array $header 发送的 header 信息
     * @param array $options Response 输出参数
     */
    protected function error(string $msg = '', mixed $data = null, int $code = 0, string $type = null, array $header = [], array $options = [])
    {
        $this->result($msg, $data, $code, $type, $header, $options);
    }

    /**
     * 返回 API 数据
     * @param string $msg 提示消息
     * @param mixed $data 返回数据
     * @param int $code 错误码
     * @param string|null $type 输出类型
     * @param array $header 发送的 header 信息
     * @param array $options Response 输出参数
     */
    public function result(string $msg, mixed $data = null, int $code = 0, string $type = null, array $header = [], array $options = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => $this->request->server('REQUEST_TIME'),
            'data' => $data,
        ];

        // 如果未设置类型则自动判断
        $type = $type ?: ($this->request->param(Config::get('route.var_jsonp_handler')) ? 'jsonp' : $this->responseType);

        $code = 200;
        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        }

        $response = Response::create($result, $type, $code)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
}
