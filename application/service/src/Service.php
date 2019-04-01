<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-27
 * Time: 下午6:33
 */
namespace SERVICE;
class Service
{
    protected $ci;
    public function __construct()
    {
        $this->ci = &get_instance();
    }
}
