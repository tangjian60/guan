<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-28
 * Time: 上午10:04
 */
namespace SERVICE;
class AgentService extends Service
{
    public function __construct()
    {
        parent::__construct();
        $this->ci->load->model('agent_model');
    }

    public function audit($user_name, $status, $memo)
    {
        $service = new AuditService();
        $data = array(
            'type' => 'seller_agent',
            'reason' => '',
            'memo' => $memo,
            'status' => $status,
            'user_name' => $user_name,
            'admin_id' => $this->ci->session->userdata('SESSION_MANAGER_ID'),
            'admin_name' => $this->ci->session->userdata('SESSION_MANAGER_NAME'),
        );
        $service->addAudit($data);
    }

    public function auditBuyer($user_name, $status, $memo)
    {
        $service = new AuditService();
        $data = array(
            'type' => 'buyer_agent',
            'reason' => '',
            'memo' => $memo,
            'status' => $status,
            'user_name' => $user_name,
            'admin_id' => $this->ci->session->userdata('SESSION_MANAGER_ID'),
            'admin_name' => $this->ci->session->userdata('SESSION_MANAGER_NAME'),
        );
        $service->addAudit($data);
    }

}