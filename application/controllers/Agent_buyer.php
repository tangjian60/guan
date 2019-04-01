<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午9:59
 */
use SERVICE\AgentService;
class Agent_buyer extends Hilton_Controller
{

    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->service = new AgentService();
    }

    public function index()
    {
        $i_page = $this->input->get('i_page', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }

        $status = $this->input->get('status', true);
        $buyer_name = $this->input->get('buyer_name', true);

        $search['i_page'] = $i_page;
        $search['buyer_name'] = $buyer_name ? $buyer_name : '';
        $search['status'] = $status ? $status : '';

        $this->Data['data'] = $this->agent_model->getListBuyer($search);
        $this->Data['i_page'] = $i_page;
        $this->Data['status'] = $status;
        $this->Data['buyer_name'] = $buyer_name;
        $this->Data['status_arr'] = array(
            \CONSTANT\Agent::STATUS_NORMAL => '正常',
            \CONSTANT\Agent::STATUS_FROZEN => '冻结',
        );

        $this->Data['PageTitle'] = '买手代理商管理';
        $this->Data['TargetPage'] = 'agent_buyer/index';
        $this->load->view('frame_main', $this->Data);
    }

    function edit()
    {
        $id = $this->input->get('id', true);

        $this->Data['agent'] = $this->agent_model->getInfoBuyer($id);
        $this->Data['PageTitle'] = '代理商管理';
        $this->Data['TargetPage'] = 'agent_buyer/edit';
        $this->load->view('frame_main', $this->Data);
    }

    function add() {
        $this->Data['PageTitle'] = '代理商管理';
        $this->Data['TargetPage'] = 'agent_buyer/add';
        $this->load->view('frame_main', $this->Data);
    }

    function save() {
        $act = $this->input->post('act', true);
        $price_data = array(
            'tb_flow' => $this->input->post('tb_flow', true),
            'tb_prepaid' => $this->input->post('tb_prepaid', true),
            'pdd_prepaid' => $this->input->post('pdd_prepaid', true)
        );

        try{
            foreach($price_data as $val){
                //throw  new \Exception(bccomp('0.00', $val, 2));
                if (bccomp('0.00', $val, 2) >= 0){
                    throw  new \Exception('填写的价格数据不合法');
                }
            }

            $data = array_merge(array(
                'id' => $this->input->post('id', true),
                'buyer_name' => $this->input->post('buyer_name', true),
                'admin_id' => $this->get_admin_id(),
                'admin_name' => $this->get_admin_name(),
            ), $price_data);

            $this->load->model('member_model');
            $this->load->model('audit_model');
            $member_info = $this->member_model->getUserInfoByUserName($data['buyer_name'], USER_TYPE_BUYER);

            if (empty($member_info) || $member_info->user_type != CONSTANT\Member::USER_TYPE_BUYER){
                throw new \Exception('买手账号不符合要求');
            }

            if ('add' == strtolower($act)){
                if ($this->agent_model->countByAgentName($data['buyer_name']) >= 1){
                    throw new \Exception('请勿重复添加同一个买手');
                }
                $data['buyer_id'] = $member_info->id;
                $data['buyer_name'] = $member_info->user_name;
                $this->agent_model->saveBuyer($data);
                echo build_response_str(CODE_SUCCESS, '添加成功');
            }else{
                if ($this->agent_model->countByAgentName($data['buyer_name'], $data['id']) > 1){
                    throw new \Exception('请勿重复添加同一个买手');
                }
                $this->agent_model->updateBuyer($data);
                echo build_response_str(CODE_SUCCESS, '更新成功');
            }

        }catch(\Exception $e){
            echo build_response_str(\CONSTANT\Member::CODE_ERROR, $e->getMessage());
        }
    }

    public function frozen()
    {
        $act = $this->input->post('act', true);
        $id = $this->input->post('id', true);
        $user_name  = $this->input->post('user_name', true);

        $memo = '暂停授权';
        $this->service->auditBuyer($user_name, 1, $memo);
        try{
            if ('frozen' == strtolower($act)){
                $this->agent_model->updateStatus($id, \CONSTANT\Agent::STATUS_FROZEN);
            }else{
                throw new \Exception("请求数据不合法");
            }
            echo build_response_str(CODE_SUCCESS, '暂停授权成功');
        }catch(\Exception $e){
            echo build_response_str(\CONSTANT\Agent::CODE_ERROR_FROZEN, "暂停授权失败(".$e->getMessage().")");
        }
    }

    public function unFrozen()
    {
        $act = $this->input->post('act', true);
        $id = $this->input->post('id', true);
        $user_name  = $this->input->post('user_name', true);
        $memo = '解冻授权';
        $this->service->auditBuyer($user_name, 1, $memo);
        try{
            if ('unfrozen' == strtolower($act)){
                $this->agent_model->updateStatus($id, \CONSTANT\Agent::STATUS_NORMAL);
            }else{
                throw new \Exception("请求数据不合法");
            }
            echo build_response_str(CODE_SUCCESS, '解冻授权成功');
        }catch(\Exception $e){
            echo build_response_str(\CONSTANT\Agent::CODE_ERROR_UNFROZEN, '解冻授权失败');
        }
    }


}