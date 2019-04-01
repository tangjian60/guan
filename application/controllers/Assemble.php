<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assemble extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('bankcard_model');
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_assemble';
        $this->load->view('frame_main', $this->Data);
    }

    public function bind_info()
    {
        $i_page = $this->input->get('i_page', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }

        $user_id = $this->input->get('user_id', true);
        $tb_nick = $this->input->get('tb_nick', true);

        $search['i_page'] = $i_page;
        $search['user_id'] = $user_id ? $user_id : '';
        $search['tb_nick'] = $tb_nick ? $tb_nick : '';

        $this->Data['data'] = $this->bankcard_model->bind_info($search);
        $this->Data['i_page'] = $i_page;
        /*$this->Data['bank_card_num'] = $bank_card_num;
        $this->Data['true_name'] = $true_name;*/
        $this->Data['user_id'] = $user_id;

        $this->Data['TargetPage'] = 'page_bind_info';
        $this->load->view('frame_main', $this->Data);
    }

    public function edit_bind_info()
    {
        $id = $this->input->get('id', true);
        $this->Data['bankinfo'] = $this->bankcard_model->getInfo_bind($id);
        $this->Data['TargetPage'] = 'page_bind_edit';
        $this->load->view('frame_main', $this->Data);
    }

    public function save_bind()
    {
        $act = $this->input->post('act', true);
        $buyer_id = $this->input->post('user_id', true);
        $user_id = decode_id($buyer_id);
        $data['tb_nick'] = $this->input->post('tb_nick', true);
        $data['tb_receiver_name'] = $this->input->post('tb_receiver_name', true);
        $data['tb_receiver_tel'] = $this->input->post('tb_receiver_tel', true);
        $data['receiver_province'] = $this->input->post('receiver_province', true);
        $data['receiver_city'] = $this->input->post('receiver_city', true);

        $data['receiver_county'] = $this->input->post('receiver_county', true);
        $data['tb_receiver_addr'] = $this->input->post('tb_receiver_addr', true);
        $data['id'] = $this->input->post('id', true);


        try{
            if ('update' == strtolower($act)){
                $this->bankcard_model->update_bind($data);
                $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 6, 1, '修改买手账号信息：操作成功');
                echo build_response_str(CODE_SUCCESS, '修改成功');
            }else{
                throw new \Exception("error");
            }
        }catch(\Exception $e){
            $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 3, 2, '修改买手帐号信息：操作失败');
            echo build_response_str(CODE_SUCCESS, '修改失败，请稍后重试');
        }

    }


    //买手银行卡管理
    public function buyer_bank()
    {
        $i_page = $this->input->get('i_page', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }

        $true_name = $this->input->get('true_name', true);
        $user_id = $this->input->get('user_id', true);
        $bank_card_num = $this->input->get('bank_card_num', true);

        $search['i_page'] = $i_page;
        $search['true_name'] = $true_name ? $true_name : '';
        $search['user_id'] = $user_id ? $user_id : '';
        $search['bank_card_num'] = $bank_card_num ? $bank_card_num : '';

        $this->Data['data'] = $this->bankcard_model->getList_buyer($search);
        $this->Data['i_page'] = $i_page;
        $this->Data['bank_card_num'] = $bank_card_num;
        $this->Data['true_name'] = $true_name;
        $this->Data['user_id'] = $user_id;

        $this->Data['TargetPage'] = 'bank/buyer_bank';
        $this->load->view('frame_main', $this->Data);
    }

    //买手银行卡信息修改
    function edit()
    {
        $id = $this->input->get('id', true);

        $this->Data['bankinfo'] = $this->bankcard_model->getInfo_buyer($id);
        $this->Data['TargetPage'] = 'bank/buyer_edit';
        $this->load->view('frame_main', $this->Data);
    }

    public function save()
    {
        $act = $this->input->post('act', true);
        $buyer_id = $this->input->post('true_name', true);
        $user_id = decode_id($buyer_id);
        $data['true_name'] = $this->input->post('true_name', true);
        $data['id_card_num'] = $this->input->post('id_card_num', true);
        $data['bank_card_num'] = $this->input->post('bank_card_num', true);
        $data['bank_name'] = $this->input->post('bank_name', true);

        $data['bank_province'] = $this->input->post('bank_province', true);
        $data['bank_city'] = $this->input->post('bank_city', true);
        $data['bank_county'] = $this->input->post('bank_county', true);

        $data['bank_branch'] = $this->input->post('bank_branch', true);
        $data['id'] = $this->input->post('id', true);

        try{
            if (!is_numeric($data['bank_card_num'])){
                echo build_response_str(CODE_SUCCESS, '请填写正确的银行卡信息');
                return;
            }

            if ('update' == strtolower($act)){
                $this->bankcard_model->updateBuyer($data);
                $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 3, 1, '修改买手银行卡信息：操作成功');
                echo build_response_str(CODE_SUCCESS, '修改成功');
            }else{
                throw new \Exception("error");
            }
        }catch(\Exception $e){
            $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 3, 2, '修改买手银行卡信息：操作失败');
            echo build_response_str(CODE_SUCCESS, '修改失败，请稍后重试');
        }
    }

    //新增推广关系
    public function new_relation()
    {

        $conclusion_data = $this->input->post();
        $owner_id = decode_id($conclusion_data['owner_id']);//推荐人ID
        $promote_id = decode_id($conclusion_data['promote_id']); //被推荐人ID
        if($this->bankcard_model->is_exist($owner_id) && $this->bankcard_model->is_exist($promote_id) ){
            $promote = $this->bankcard_model->is_promote($promote_id);
            if(empty($promote)){    //查询该被推荐人是否已经被人推荐过
                  $owner = $this->bankcard_model->is_owner($owner_id); //推荐人和被推荐人是否已经存在上下级关系
                  if(empty($owner)){
                      if($this->bankcard_model->insert_relation($owner_id,$promote_id)){
                          $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 1, '新增推广关系：操作成功');
                          echo build_response_str(CODE_SUCCESS, '新增推广关系：操作成功');

                      }else{
                          $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 2, '新增推广关系：操作失败');
                          echo build_response_str(CODE_BAD_REQUEST, '新增推广关系：操作失败');
                      }

                  }else{

                      if($owner->owner_id != $promote_id){
                          if($this->bankcard_model->insert_relation($owner_id,$promote_id)){
                              $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 1, '新增推广关系：操作成功');
                              echo build_response_str(CODE_SUCCESS, '新增推广关系：操作成功');

                          }else{
                              $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 2, '新增推广关系：操作失败');
                              echo build_response_str(CODE_BAD_REQUEST, '新增推广关系：操作失败');
                          }

                      }else{
                          $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 2, '新增推广关系：操作失败');
                          echo build_response_str(CODE_BAD_REQUEST, '新增推广关系：操作失败');
                      }
                  }



              }else{
                  $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 2, '新增推广关系：操作失败');
                  echo build_response_str(CODE_BAD_REQUEST, '新增推广关系：操作失败');
              }



        }else{
            $this->hiltoncore->add_oper_log($owner_id, $this->get_admin_id(), $this->get_admin_name(), 10, 2, '新增推广关系：操作失败');
            echo build_response_str(CODE_BAD_REQUEST, '新增推广关系：操作失败');
        }


    }
}