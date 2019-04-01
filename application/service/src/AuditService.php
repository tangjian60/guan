<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-27
 * Time: 下午6:20
 */

namespace SERVICE;
class AuditService extends Service
{
    public function __construct()
    {
        parent::__construct();
        $this->ci->load->model('audit_model');
    }

    public function addAudit($data)
    {
        $this->ci->audit_model->save(array_merge($data, array(
            'ctime' => time()
        )));
    }

}