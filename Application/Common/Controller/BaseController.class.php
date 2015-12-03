<?php
namespace Common\Controller;
use Think\Controller;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class BaseController extends Controller {
    protected $log;

    public function __construct() {
        $this->log = new Logger(MODULE_NAME);
        $this->log->pushHandler(new StreamHandler(LOG_PATH . MODULE_NAME . '/log', Logger::DEBUG));
    }

}