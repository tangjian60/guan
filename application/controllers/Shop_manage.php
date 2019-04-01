<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shop_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->library('YTOExpress');
        $this->load->model('taskengine');
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['shop_name'] = $this->input->get('shop_name', TRUE);
            $this->Data['shop_ww'] = $this->input->get('shop_ww', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['data'] = $this->hiltoncore->get_all_shop_infos($this->Data);
        }


        $this->Data['TargetPage'] = 'page_shop_manage';
        $this->load->view('frame_main', $this->Data);
    }

    public function operation_handle()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $act = $this->input->post('act', true);
        if (empty($act)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if ($act == 'set_black') {
            if ($this->hiltoncore->set_shop_ban($this->input->post('shop_id', true))) {
                echo build_response_str(CODE_SUCCESS, "设置黑名单成功");
                return;
            }
        } else if ($act == 'unset_black') {
            if ($this->hiltoncore->unset_shop_ban($this->input->post('shop_id', true))) {
                echo build_response_str(CODE_SUCCESS, "取消黑名单成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function edit()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['id'] = $this->input->get('id', TRUE);
        $data = $this->hiltoncore->get_shop($this->Data);
        $this->Data['data'] = $data[0];
        $this->Data['TargetPage'] = 'page_shop_edit';
        $this->load->view('frame_main', $this->Data);
    }

    public function edit_do()
    {
        $act = $this->input->post('act', true);
        $data['id'] = $this->input->post('id', true);
        $data['seller_id'] = $this->input->post('seller_id', true);
        $seller_id = decode_id($data['seller_id']);
        $data['shop_name'] = $this->input->post('shop_name', true);
        if ('update' == strtolower($act)){
            if($this->hiltoncore->updateShop($data)){
                $this->hiltoncore->add_oper_log($seller_id, $this->get_admin_id(), $this->get_admin_name(), 7, 1, '修改店铺信息：操作成功');
                echo build_response_str(CODE_SUCCESS, '修改成功');
            }else{
                $this->hiltoncore->add_oper_log($seller_id, $this->get_admin_id(), $this->get_admin_name(), 7, 2, '修改店铺信息：操作失败');
                echo build_response_str(CODE_SUCCESS, '修改失败，请稍后重试');
            }
        }else{
            throw new \Exception("error");
        }
    }

    //店铺换绑
    public function change_shop()
    {
        $conclusion_data = $this->input->post();
        $data['id'] = $conclusion_data['id'];//店铺ID
        $data['old_seller_id'] = $conclusion_data['old_seller_id'];//商家ID
        $data['seller_id'] = $conclusion_data['reject_reason'];//待绑定的新商家ID
        $shop_info = $this->hiltoncore->get_shop($data);//获取该店铺的信息
        $this->Data['data'] = $shop_info[0];
        $this->Data['new_seller_id'] = decode_id($data['seller_id']);
        $new_shop_id = $this->hiltoncore->add_shop_info($this->Data);//新增一条同样的店铺信息绑定到新商家下面 返回新增的这条店铺信息的id

        $templates_info =  $this->hiltoncore->templates_info($data['id']);//获取该店铺下所有的模板信息
        $result =  $this->hiltoncore->add_templates($new_shop_id,$this->Data['new_seller_id'],$templates_info); //将该店铺下的所有模板复制一份到新的
        if($result == true){
            $this->hiltoncore->add_oper_log($data['old_seller_id'], $this->get_admin_id(), $this->get_admin_name(), 8, 1, '店铺换绑：操作成功');
            echo build_response_str(CODE_SUCCESS, '店铺换绑：操作成功');
        }else{
            $this->hiltoncore->add_oper_log($data['old_seller_id'], $this->get_admin_id(), $this->get_admin_name(), 8, 2, '店铺换绑：操作失败');
            echo build_response_str(CODE_SUCCESS, '店铺换绑：操作失败');
        }
    }

    //重申快递单号
    public function  reassert_express()
    {
        $id = $this->input->post('id', true);
        $ids = encode_id($id);
        $type = $this->input->post('type', true);
        $conclusion_data['task_type'] = $type;
        $conclusion_data['task_id'] = $id;
        $taskData = $this->taskengine->get_dianfu_task_info($ids);  //获取该笔订单的信息
        $conclusion_data['conclusion'] = SELLER_CONCLUSION_TASK_OK;
        $conclusion_data['seller_id'] = $taskData->seller_id;
        $seller = $this->taskengine->get_sellerPhone($conclusion_data['seller_id']);  //获取商家的手机号
        $sellerPhone = $seller->user_name;

        if (!$this->ifExpress($conclusion_data)) {
            die(build_response_str(CODE_BANED, 'NO'));
        }

        if (empty($taskData->is_express) || $taskData->is_express == NOT_AVAILABLE || $taskData->express_success != 0) {
            die(build_response_str(CODE_BANED, 'NO'));
        }

        $requestYTO = $this->packYTOdata($conclusion_data, $sellerPhone); // 组装请求参数
        if (!$requestYTO) {
            die(build_response_str(CODE_BANED, '请求参数错误'));
        }
        $responseYTO = $this->ytoexpress->sendYTORequest($requestYTO);// 请求快递通快递接口

        // 修改任务单状态
        if($this->taskengine->expressYto($requestYTO, $responseYTO, $taskData)){
            $this->hiltoncore->add_oper_log($conclusion_data['seller_id'], $this->get_admin_id(), $this->get_admin_name(), 9, 1, '重申快递单号：操作成功');
            die(build_response_str(CODE_SUCCESS, 'OK'));
        }else{
            $this->hiltoncore->add_oper_log($conclusion_data['seller_id'], $this->get_admin_id(), $this->get_admin_name(), 9, 2, '重申快递单号：操作失败');
            die(build_response_str(CODE_BANED, 'NO'));
        }
    }

    public function ifExpress($p)
    {
        if (empty($p['task_type']) || empty($p['task_id']) || empty($p['conclusion']) || empty($p['seller_id'])) {
            return false;
        }

        if ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            return true;
        }elseif ($p['task_type'] == TASK_TYPE_DT && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            return true;
        }elseif ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            return true;
        }
        return false;
    }

    public function packYTOdata($conclusion_data,$sellerPhone)
    {
        if (invalid_parameter($conclusion_data)) {
            throw new Exception('非法请求', CODE_BAD_REQUEST);
        }
        $this->load->model('taskengine');
        // 1. 获取订单和店铺信息 -
        $taskInfo = $this->taskengine->getExpressData1($conclusion_data);
        if(empty($taskInfo)) {
            return false;
        }

        $senderInfo = $taskInfo->shop_ww . '@';
        $senderInfo .=  '000000' . '@';
        $senderInfo .=  '0' . '@';
        $senderInfo .=  $sellerPhone . '@';
        $senderInfo .=  $taskInfo->shop_province . '@';
        $senderInfo .=  $taskInfo->shop_city . ',';
        $senderInfo .=  $taskInfo->shop_county . '@';
        $senderInfo .=  preg_replace('/ /', '', $taskInfo->shop_address);
        // 2. 获取买家信息
        $buyerInfo = $this->taskengine->getBuyerBindInfo1($conclusion_data, $taskInfo->buyer_tb_nick);
        if(empty($buyerInfo)) {
            return false;
        }

        $receiverInfo = preg_replace('/ /', '', $buyerInfo->tb_receiver_name) . '@';
        $receiverInfo .=  '000000' . '@';
        $receiverInfo .=  '0' . '@';
        $receiverInfo .=  $buyerInfo->tb_receiver_tel . '@';
        $receiverInfo .=  $buyerInfo->receiver_province . '@';
        $receiverInfo .=  $buyerInfo->receiver_city . ',';
        $receiverInfo .=  $buyerInfo->receiver_county . '@';
        $receiverInfo .=  preg_replace('/ /', '', $buyerInfo->tb_receiver_addr);
        // 3. 获取任务信息
        $parentOrderInfo = $this->taskengine->get_parent_order_info($taskInfo->parent_order_id);
        // 4. 任务类型
        switch ($parentOrderInfo->platform_type) {
            case PLATFORM_TYPE_TAOBAO:
                $platform = 'taobao';
                break;
            case PLATFORM_TYPE_PINDUODUO:
                $platform = 'pdd';
                break;
            default:
                $platform = 'taobao';
                break;
        }

        $sendYTO = [
            'sender'            => trim($senderInfo),//拼接后的端商家地址
            'receiver'          => trim($receiverInfo),//拼接后的买手地址
            'user_id'           => $taskInfo->seller_id,
            'channel'           => 'xiaohongbao',
            'shop_type'         => $platform,
            'order_number'      => $taskInfo->order_number,//订单号
            'shop_id'           => $taskInfo->shop_id,//店铺id
            'shop_name'         => $taskInfo->shop_name,//店铺名称
            'express_company'   => $taskInfo->express_type,//快递类型
            'express_price'     => $taskInfo->single_task_express,//快递金额
            'express_total'     => 1,
            'goods_weight'      => $taskInfo->goods_weight,//收件人货物重量
        ];
        return $sendYTO;
    }



}