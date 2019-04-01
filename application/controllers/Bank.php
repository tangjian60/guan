<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午9:59
 */
use SERVICE\AgentService;
class Bank extends Hilton_Controller
{

    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->Data['PageTitle'] = '银行卡管理';
        $this->load->model('bankcard_model');
    }

    public function index()
    {
        $i_page = $this->input->get('i_page', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }

        $true_name = $this->input->get('true_name', true);
        $seller_id = $this->input->get('seller_id', true);
        $bank_card_num = $this->input->get('bank_card_num', true);

        $search['i_page'] = $i_page;
        $search['true_name'] = $true_name ? $true_name : '';
        $search['seller_id'] = $seller_id ? $seller_id : '';
        $search['bank_card_num'] = $bank_card_num ? $bank_card_num : '';

        $this->Data['data'] = $this->bankcard_model->getList($search);
        $this->Data['i_page'] = $i_page;
        $this->Data['bank_card_num'] = $bank_card_num;
        $this->Data['true_name'] = $true_name;
        $this->Data['seller_id'] = $seller_id;

        $this->Data['TargetPage'] = 'bank/index';
        $this->load->view('frame_main', $this->Data);
    }

    function edit()
    {
        $id = $this->input->get('id', true);

        $this->Data['bankinfo'] = $this->bankcard_model->getInfo($id);
        $this->Data['TargetPage'] = 'bank/edit';
        $this->load->view('frame_main', $this->Data);
    }

    function save()
    {
        $act = $this->input->post('act', true);
        $bank_card_num = $this->input->post('bank_card_num', true);
        $id = $this->input->post('id', true);

        try{

            if (!is_numeric($bank_card_num)){
                echo build_response_str(CODE_SUCCESS, '请填写正确的银行卡信息');
                return;
            }

            if ('update' == strtolower($act)){
                $this->bankcard_model->updateBankcard($bank_card_num, $id);
                echo build_response_str(CODE_SUCCESS, '修改成功');
            }else{
                throw new \Exception("error");
            }
        }catch(\Exception $e){
            echo build_response_str(CODE_SUCCESS, '修改失败，请稍后重试');
        }
    }
}