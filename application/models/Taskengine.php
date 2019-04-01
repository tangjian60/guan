<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Taskengine extends Hilton_Model
{

    const DB_TASK                   = 'user_tasks';
    const DB_TASK_PARENT_ORDERS     = 'hilton_task_parent_orders';
    const DB_TASK_DIANFU            = 'hilton_task_dianfu';
    const DB_TASK_DIANFU_EXT        = 'hilton_task_dianfu_ext';
    const DB_TASK_LIULIANG          = 'hilton_task_liuliang';
    const DB_TASK_PINDUODUO         = 'hilton_task_pinduoduo';
    const DB_CANCELLED_TASKS        = 'hilton_task_cancelled';
    const DB_USER_MEMBERS           = 'user_members';
    const DB_BIND_SHOPS             = 'seller_bind_shops';
    const DB_USER_BIND_INFO         = 'user_bind_info';
    const DB_BUYER_TASK_DUOTIAN     = 'buyer_task_duotian';

    const TASK_STATUS_DZF       = 1;
    const TASK_STATUS_DJD       = 2;
    const TASK_STATUS_DCZ       = 3;
    const TASK_STATUS_MJSH      = 4;
    const TASK_STATUS_MJSH_BTG  = 5;
    const TASK_STATUS_PTSH      = 6;
    const TASK_STATUS_PTSH_BTG  = 7;
    const TASK_STATUS_DPJ       = 8;
    const TASK_STATUS_HPSH      = 9;
    const TASK_STATUS_HPSH_BTG  = 10;
    const TASK_STATUS_YWC       = 11;
    const TASK_STATUS_YCX       = 99;

    const TASK_CLEARING_STATUS_NO = 0;
    const TASK_CLEARING_STATUS_YES = 1;

    const TASK_STATUS_XTCZ = 12;
    const TASK_STATUS_XTGB = 13;
    const TASK_STATUS_XTGB_DT = 14;

    const TASK_STATUS_SSZ = 20;

    private static $TASK_STATUS = array(
        self::TASK_STATUS_DZF           => "待支付",
        self::TASK_STATUS_DJD           => "派单中",
        self::TASK_STATUS_DCZ           => "已接单待操作",
        self::TASK_STATUS_MJSH          => "卖家审核",
        self::TASK_STATUS_MJSH_BTG      => "卖家审核不通过",
        self::TASK_STATUS_PTSH          => "平台审核",
        self::TASK_STATUS_PTSH_BTG      => "平台审核不通过",
        self::TASK_STATUS_DPJ           => "待评价",
        self::TASK_STATUS_HPSH          => "好评审核",
        self::TASK_STATUS_HPSH_BTG      => "好评审核不通过",
        self::TASK_STATUS_YWC           => "已完成",
        self::TASK_STATUS_YCX           => "已撤销",
        self::TASK_STATUS_XTCZ          => "商家审核拒绝，买家不操作，系统重置订单",
        self::TASK_STATUS_XTGB          => "商家审核拒绝，买家不操作，系统关闭订单",
        self::TASK_STATUS_XTGB_DT       => "未及时操作关闭任务",
        self::TASK_STATUS_SSZ           => "申诉中",
    );

    function __construct()
    {
        parent::__construct();
    }

    public static function get_status_name($status_code)
    {
        if (empty($status_code)) {
            return;
        }

        foreach (self::$TASK_STATUS as $k => $v) {
            if ($k == $status_code) {
                return $v;
                break;
            }
        }

        return;
    }

    public static function get_all_status()
    {
        return self::$TASK_STATUS;
    }

    function get_task_parent_orders($p)
    {
        if (!empty($p['order_id'])) {
            $this->db->where('id', decode_id($p['order_id']));
            return $this->db->get(self::DB_TASK_PARENT_ORDERS)->result();
        }

        if (!empty($p['member_id'])) {
            $this->db->where('seller_id', decode_id($p['member_id']));
        }

        if (!empty($p['start_time'])) {
            $this->db->where('gmt_create >=', $p['start_time']);
        }

        if (!empty($p['end_time'])) {
            $this->db->where('gmt_create <=', $p['end_time']);
        }

        if (!empty($p['status'])) {
            $this->db->where('status', $p['status']);
        }

        if (!empty($p['i_page']) && is_numeric($p['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($p['i_page'] - 1));
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_TASK_PARENT_ORDERS)->result();
    }

    function get_parent_order_info($i)
    {
        $this->db->where('id', $i);
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_PARENT_ORDERS)->row();
    }

    function update_parent_order_status($i, $n_status)
    {
        if (empty($i) || empty($n_status)) {
            return false;
        }
        $this->db->set('status', $n_status);
        $this->db->where('id', $i);
        return $this->db->update(self::DB_TASK_PARENT_ORDERS);
    }

    function get_liuliang_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_LIULIANG)->row();
    }

    function get_dianfu_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_DIANFU)->row();
    }

    function get_duotian_task_info($id)
    {
        $id = decode_id($id);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $row = $this->db->get(self::DB_TASK_DIANFU)->row();

        $data['detail'] = $row;

        $row2 = $this->db->select('task_attr')->where('task_id', $id)->get(self::DB_TASK_DIANFU_EXT)->row();
        if ($row) {
            $task_attr = json_decode($row2->task_attr, true);
            $res = $this->db->where('task_id', $id)->get(self::DB_BUYER_TASK_DUOTIAN)->result();
            $show_data = [];
            for ($i=1; $i<=$row->cur_task_day; $i++) {
                $show_data[$i]['of'] = $task_attr['op_flow_' . $i];
                $show_data[$i]['mo'] = $this->_getMoTxtArr($task_attr['method_outer_' . $i]);
                foreach ($res as $val) {
                    if ($i == $val->task_step) {
                        $show_data[$i]['imgs'] = json_decode($val->task_imgs, true);
                        break;
                    }
                }
            }
            $data['show_data'] = $show_data;
            unset($show_data);
        }
        //print_r($data);exit;
        return $data;
    }

    private function _getMoTxtArr($mo_arr)
    {
        $aData = [];
        $config = load_config('shang');
        if (is_array($mo_arr)) {
            foreach ($mo_arr as $val) {
                if ($val > 4) $data['ext']['is_browse_inner'] = 1;
                $aData[] = $config['task_behaviors'][$val-1];
            }
        } else {
            if (intval($mo_arr) > 4) $data['ext']['is_browse_inner'] = 1;
            $aData[] = $config['task_behaviors'][intval($mo_arr)-1];
        }
        return $aData;
    }

    function get_pinduoduo_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_PINDUODUO)->row();
    }

    function get_task_list($r)
    {
        $task_type = '';
        if (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DF) {
            $task_type = TASK_TYPE_DF;
            $db_name = self::DB_TASK_DIANFU;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_LL) {
            $task_type = TASK_TYPE_LL;
            $db_name = self::DB_TASK_LIULIANG;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_PDD) {
            $task_type = TASK_TYPE_PDD;
            $db_name = self::DB_TASK_PINDUODUO;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DT) {
            $task_type = TASK_TYPE_DT;
            $db_name = self::DB_TASK_DIANFU;
        } else {
            return false;
        }

        if (!empty($r['task_id'])) {
            $this->db->where('id', decode_id($r['task_id']));
            $this->db->where('task_type', $task_type);
            return $this->db->get($db_name)->result();
        }

        if (!empty($r['order_id'])) {
            $this->db->where('parent_order_id', decode_id($r['order_id']));
        }

        if (!empty($r['member_id'])) {
            $this->db->where('seller_id', decode_id($r['member_id']));
        }
        
        if (!empty($r['buyer_id'])) {
            $this->db->where('buyer_id', decode_id($r['buyer_id']));
        }

        if (!empty($r['start_time'])) {
            $this->db->where('gmt_taking_task >=', $r['start_time']);
        }

        if (!empty($r['end_time'])) {
            $this->db->where('gmt_taking_task <=', $r['end_time']);
        }

        if (!empty($r['status'])) {
            $this->db->where('status', $r['status']);
        }

        if (!empty($r['create_start_time'])) {
            $this->db->where('gmt_create >=', $r['create_start_time']);
        }

        if (!empty($r['create_end_time'])) {
            $this->db->where('gmt_create <=', $r['create_end_time']);
        }




        if (!empty($task_type)) {
            $this->db->where('task_type', $task_type);
        }

        if (!empty($r['i_page']) && is_numeric($r['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($r['i_page'] - 1));
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get($db_name)->result();
    }

    function get_status_PTSH_task_list($r)
    {
        if (!empty($r['member_id'])) {
            $this->db->where('seller_id', decode_id($r['member_id']));
        }

        if (!empty($r['start_time'])) {
            $this->db->where('gmt_taking_task >=', $r['start_time']);
        }

        if (!empty($r['end_time'])) {
            $this->db->where('gmt_taking_task <=', $r['end_time']);
        }

        if (!empty($r['i_page']) && is_numeric($r['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($r['i_page'] - 1));
        }

        $this->db->where('status', self::TASK_STATUS_PTSH);
        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    function approve_dianfu_task($task_id, $oper_id)
    {
        if (empty($task_id) || !is_numeric($task_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', self::TASK_STATUS_DPJ);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }

    function reject_dianfu_task($task_id, $oper_id)
    {
        if (empty($task_id) || !is_numeric($task_id) || empty($oper_id)) {
            return false;
        }

        $this->db->set('status', self::TASK_STATUS_PTSH_BTG);
        $this->db->set('oper_id', $oper_id);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }

    function cancel_timeout_DJD_tasks()
    {
        $sql1 = "UPDATE " . self::DB_TASK_LIULIANG . " SET status = " . self::TASK_STATUS_YCX . " WHERE status = " . self::TASK_STATUS_DJD . " AND end_time <= NOW();";
        $this->db->query($sql1);
        $sql2 = "UPDATE " . self::DB_TASK_DIANFU . " SET status = " . self::TASK_STATUS_YCX . " WHERE status = " . self::TASK_STATUS_DJD . " AND end_time <= NOW();";
        $this->db->query($sql2);
        $sql3 = "UPDATE " . self::DB_TASK_PINDUODUO . " SET status = " . self::TASK_STATUS_YCX . " WHERE status = " . self::TASK_STATUS_DJD . " AND end_time <= NOW();";
        $this->db->query($sql3);
    }

    // 自动审核超过24小时的单，对于垫付单、拼多多，仅审核第一步
    function autoaudit_24timeout_tasks(){
        // 流量单
        $sql_update_1 = "UPDATE " . self::DB_TASK_LIULIANG . " SET status = " . self::TASK_STATUS_YWC . " WHERE status = " . self::TASK_STATUS_MJSH . " AND date_add(task_submit_time, interval 1 day) <= NOW();";
        $this->db->query($sql_update_1);
        // 垫付单
        $sql_result_2_1 = "SELECT id,task_type,is_express FROM " . self::DB_TASK_DIANFU . " 
                            WHERE status = " . self::TASK_STATUS_MJSH . " 
                            AND date_add(task_submit_time, interval 1 day) <= NOW() 
                            AND real_task_capital = single_task_capital;";
        $task_express = $this->db->query($sql_result_2_1)->result();
//        return $task_express ;
        if (!empty($task_express)){
            foreach ($task_express as $item){
                // 处理快递费
                $this->autoaudit_24timeout_tasks_express($item);
                // 更新垫付单状态
                $this->db->query("UPDATE " . self::DB_TASK_DIANFU . " SET status = " . self::TASK_STATUS_DPJ . " 
                            WHERE id = {$item->id}");
            }
        }
//        $sql_update_2_2 = "UPDATE " . self::DB_TASK_DIANFU . " SET status = " . self::TASK_STATUS_DPJ . "
//                            WHERE status = " . self::TASK_STATUS_MJSH . "
//                            AND date_add(task_submit_time, interval 1 day) <= NOW()
//                            AND real_task_capital <= single_task_capital;";
//        $this->db->query($sql_update_2_2);
        // 拼多多单
        $sql3 = "UPDATE " . self::DB_TASK_PINDUODUO . " SET status = " . self::TASK_STATUS_DPJ . " 
                    WHERE status = " . self::TASK_STATUS_MJSH . " 
                    AND date_add(task_submit_time, interval 1 day) <= NOW() 
                    AND real_task_capital = single_task_capital;";
        $this->db->query($sql3);
    }

    // 自动审核超过48小时的好评单，仅仅适用于垫付单、拼多多
    function autoaudit_haoping_48timeout_tasks(){
        // 垫付单
        $sql_df = "UPDATE " . self::DB_TASK_DIANFU . " SET status = " . self::TASK_STATUS_YWC . " WHERE status = " . self::TASK_STATUS_HPSH . " AND date_add(task_submit_time, interval 2 day) <= NOW();";
        $this->db->query($sql_df);
        // 拼多多单
        $sql_pdd = "UPDATE " . self::DB_TASK_PINDUODUO . " SET status = " . self::TASK_STATUS_YWC . " WHERE status = " . self::TASK_STATUS_HPSH . " AND date_add(task_submit_time, interval 2 day) <= NOW();";
        $this->db->query($sql_pdd);
    }

    function autoaudit_24timeout_tasks_express($taskData){
        if (!empty($taskData->is_express) && $taskData->is_express != NOT_AVAILABLE) {
            $requestYTO     = $this->packYTOdata($taskData);
            $responseYTO    = $this->ytoexpress->sendYTORequest($requestYTO);

            $log = array(
                'task_id' => $taskData->id,
                'task_type' => $taskData->task_type,
                'request_data' => json_encode($requestYTO),
                'response_data' => json_encode($responseYTO),
                'success' => $responseYTO->success
            );
            $this->addRequestLog($log);
            $this->updateExpressNumOrReturn($taskData, $responseYTO);
        }
    }

    public function addRequestLog($log)
    {
        $data = array(
            'ctime' => time()
        );

        return $this->db->insert('request_log', array_merge($log, $data));
    }

    public function packYTOdata($taskData){
        // 1. 获取订单和店铺信息 -
        $taskInfo = $this->getExpressData($taskData);
        $senderInfo = $taskInfo->shop_ww . '@';
        $senderInfo .=  '000000' . '@';
        $senderInfo .=  '0' . '@';
        $senderInfo .=  $taskInfo->user_name . '@';
        $senderInfo .=  $taskInfo->shop_province . '@';
        $senderInfo .=  $taskInfo->shop_city . ',';
        $senderInfo .=  $taskInfo->shop_county . '@';
        $senderInfo .=  preg_replace('/ /', '', $taskInfo->shop_address);
        // 2. 获取买家信息
        $buyerInfo = $this->getBuyerBindInfo($taskData, $taskInfo->buyer_tb_nick);
        $receiverInfo = preg_replace('/ /', '', $buyerInfo->tb_receiver_name) . '@';
        $receiverInfo .=  '000000' . '@';
        $receiverInfo .=  '0' . '@';
        $receiverInfo .=  $buyerInfo->tb_receiver_tel . '@';
        $receiverInfo .=  $buyerInfo->receiver_province . '@';
        $receiverInfo .=  $buyerInfo->receiver_city . ',';
        $receiverInfo .=  $buyerInfo->receiver_county . '@';
        $receiverInfo .=  preg_replace('/ /', '', $buyerInfo->tb_receiver_addr);
        // 3. 获取任务信息
        $parentOrderInfo = $this->get_parent_order_info($taskInfo->parent_order_id);
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
            'sender'            => trim($senderInfo),
            'receiver'          => trim($receiverInfo),
            'user_id'           => $taskInfo->seller_id,
            'channel'           => 'xiaohongbao',
            'shop_type'         => $platform,
            'order_number'      => $taskInfo->order_number,
            'shop_id'           => $taskInfo->shop_id,
            'shop_name'         => $taskInfo->shop_name,
            'express_company'   => $taskInfo->express_type,
            'express_price'     => $taskInfo->single_task_express,
            'express_total'     => 1,
            'goods_weight'      => $taskInfo->goods_weight,
        ];
        return $sendYTO;
    }

    public function getExpressData($r){
        if ($r->task_type == TASK_TYPE_DF) {
            $this->db->where(self::DB_TASK_DIANFU . '.id', $r->id);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_DIANFU .'.shop_id', 'left');
            $this->db->join(self::DB_USER_MEMBERS, self::DB_USER_MEMBERS . '.id = '. self::DB_TASK_DIANFU .'.seller_id', 'left');
            $this->db->select([
                self::DB_TASK_DIANFU . '.id',
                self::DB_TASK_DIANFU . '.task_type',
                self::DB_TASK_DIANFU . '.seller_id',
                self::DB_TASK_DIANFU . '.parent_order_id',
                self::DB_TASK_DIANFU . '.shop_id',
                self::DB_TASK_DIANFU . '.shop_type',
                self::DB_TASK_DIANFU . '.shop_name',
                self::DB_TASK_DIANFU . '.shop_ww',
                self::DB_TASK_DIANFU . '.item_id',
                self::DB_TASK_DIANFU . '.buyer_id',
                self::DB_TASK_DIANFU . '.buyer_tb_nick',
                self::DB_TASK_DIANFU . '.gmt_taking_task',
                self::DB_TASK_DIANFU . '.single_task_express',
                self::DB_TASK_DIANFU . '.gmt_update',
                self::DB_TASK_DIANFU . '.order_number',
                self::DB_TASK_DIANFU . '.express_type',
                self::DB_TASK_DIANFU . '.is_express',
                self::DB_TASK_DIANFU . '.goods_weight',
                self::DB_BIND_SHOPS  . '.shop_province',
                self::DB_BIND_SHOPS  . '.shop_city',
                self::DB_BIND_SHOPS  . '.shop_county',
                self::DB_BIND_SHOPS  . '.shop_address',
                self::DB_USER_MEMBERS . '.user_name',
            ]);
            return $this->db->get(self::DB_TASK_DIANFU)->row();
        } elseif ($r->task_type == TASK_TYPE_LL) {
            $this->db->where(self::DB_TASK_LIULIANG . '.id', $r->id);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_LIULIANG .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_LIULIANG)->row();
        } elseif ($r->task_type == TASK_TYPE_PDD) {
            $this->db->where(self::DB_TASK_PINDUODUO . '.id', $r->id);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_PINDUODUO .'.shop_id', 'left');
            $this->db->join(self::DB_USER_MEMBERS, self::DB_USER_MEMBERS . '.id = '. self::DB_TASK_DIANFU .'.seller_id', 'left');
            return $this->db->get(self::DB_TASK_PINDUODUO)->row();
        }
        return 0;
    }

    public function getExpressData1($r){
        if ($r['task_type'] == TASK_TYPE_DF || $r['task_type'] == TASK_TYPE_DT) {
            $this->db->where(self::DB_TASK_DIANFU . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_DIANFU .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_DIANFU)->row();
        } elseif ($r['task_type'] == TASK_TYPE_LL) {
            $this->db->where(self::DB_TASK_LIULIANG . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_LIULIANG .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_LIULIANG)->row();
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            $this->db->where(self::DB_TASK_PINDUODUO . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_PINDUODUO .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_PINDUODUO)->row();
        }
        return 0;
    }

    //获取商家的手机号
    public function get_sellerPhone($seller_id)
    {
        if(empty($seller_id)){
            return false;
        }
        $this->db->where('id', $seller_id);
        return $this->db->get(self::DB_USER_MEMBERS)->row();

    }

    public function expressYto($requestYto, $responseYto, $task)
    {
        $log = array(
            'task_id' => $task->id,
            'task_type' => $task->task_type,
            'request_data' => json_encode($requestYto),
            'response_data' => json_encode($responseYto),
            'success' => $responseYto->success
        );
        $this->addRequestLog($log);

        if ($requestYto && !empty($responseYto->express_number)){
            $data = array(
                'express_success' => 1,
                'express_number' => $responseYto->express_number,
                'express_reason' => $responseYto->msg
            );
        }else{
            $data = array(
                'express_success' => 0,
                'express_number' => '异常未产生单号',
                'express_reason' => $responseYto->msg
            );
        }

        $this->db->where('id', $task->id);
        if ($task->task_type == TASK_TYPE_DF || $task->task_type == TASK_TYPE_DT) {
            return $this->db->update(self::DB_TASK_DIANFU, $data);
        } elseif ($task->task_type == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO, $data);
        }
        return false;
    }


    public function getBuyerBindInfo($r, $buyer_tb_nick){
        if ($r->task_type == TASK_TYPE_DF) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r->task_type == TASK_TYPE_LL) {
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r->task_type == TASK_TYPE_PDD) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 3);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        }
        return 0;
    }

    public function getBuyerBindInfo1($r, $buyer_tb_nick){
        if ($r['task_type'] == TASK_TYPE_DF || $r['task_type'] == TASK_TYPE_DT) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r['task_type'] == TASK_TYPE_LL) {
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 3);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        }
        return 0;
    }



    public function updateExpressNumOrReturn($r, $responseYTO){
        if (!empty($responseYTO)) {
            if ($responseYTO->success == 'true') {
                $expressOrder = $responseYTO->express_number;
            }else{
                $expressOrder = '异常未产生单号';
            }
        }else{
            $expressOrder = '异常未产生单号';
        }
        $this->updateExpressOrder($r, $expressOrder);
    }
    public function updateExpressOrder($r, $orderNum){
        $this->db->where('id', $r->id);
        $update_data = array(
            'express_number' => $orderNum,
        );
        if ($r->task_type == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU, $update_data);
        } elseif ($r->task_type == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO, $update_data);
        }
    }

    function get_unclearing_canceled_dianfu_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YCX);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    function get_unclearing_canceled_pinduoduo_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YCX);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        $this->db->where('express_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_PINDUODUO)->result();
    }

    function get_unclearing_canceled_liuliang_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YCX);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_LIULIANG)->result();
    }

    function get_unclearing_capital_dianfu_tasks()
    {
        $this->db->where_in('status', array(self::TASK_STATUS_DPJ, self::TASK_STATUS_HPSH, self::TASK_STATUS_HPSH_BTG, self::TASK_STATUS_YWC));
        $this->db->where('capital_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    function get_unclearing_express_dianfu_tasks(){
        $this->db->where('is_express', 1);
        $this->db->where('express_number', '异常未产生单号');
        $this->db->where('express_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    function get_unclearing_capital_pinduoduo_tasks()
    {
        $this->db->where_in('status', array(self::TASK_STATUS_DPJ, self::TASK_STATUS_HPSH, self::TASK_STATUS_HPSH_BTG, self::TASK_STATUS_YWC));
        $this->db->where('capital_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_PINDUODUO)->result();
    }

    function get_unclearing_express_pinduoduo_tasks(){
        $this->db->where('is_express', 1);
        $this->db->where('express_number', '异常未产生单号');
        $this->db->where('express_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_PINDUODUO)->result();
    }

    function get_unclearing_commission_dianfu_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YWC);
        $this->db->where('commission_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    function get_unclearing_commission_pinduoduo_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YWC);
        $this->db->where('commission_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_PINDUODUO)->result();
    }

    function get_unclearing_commission_liuliang_tasks()
    {
        $this->db->where('status', self::TASK_STATUS_YWC);
        $this->db->where('commission_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_LIULIANG)->result();
    }

    //系统关闭订单退款
    function get_unclearing_xitonggb_liuliang_tasks($id)
    {
        $this->db->where('status', self::TASK_STATUS_XTGB);
        $this->db->where('id', $id);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_LIULIANG)->result();
    }

    //系统关闭订单退款
    function get_unclearing_xitonggb_dianfu_tasks($id)
    {
        $this->db->where('status', self::TASK_STATUS_XTGB);
        $this->db->where('id', $id);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->result();
    }

    //系统关闭订单退快递费 TODO...
    function get_unclearing_xitonggb_dianfu_tasks_express($id)
    {
        $this->db->where('id', $id);
        $this->db->where('is_express', 1);
        $this->db->where('express_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_DIANFU)->row();
    }

    //系统关闭订单退款
    function get_unclearing_xitonggb_pinduoduo_tasks($id)
    {
        $this->db->where('status', self::TASK_STATUS_XTGB);
        $this->db->where('id', $id);
        $this->db->where('refund_clearing_status', self::TASK_CLEARING_STATUS_NO);
        return $this->db->get(self::DB_TASK_PINDUODUO)->result();
    }

        function dianfu_task_refund_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('refund_clearing_status', self::TASK_CLEARING_STATUS_YES);//退款资金清算状态改为1
        $this->db->set('express_clearing_status', self::TASK_CLEARING_STATUS_YES);//任务快递费清算状态改为1
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }

    function pinduoduo_task_refund_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('refund_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_PINDUODUO);
    }

    function liuliang_task_refund_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('refund_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_LIULIANG);
    }

    function dianfu_task_capital_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('capital_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }
    function dianfu_task_express_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('express_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }
    
    function pinduoduo_task_express_clearing_complete($task_id){
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('express_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_PINDUODUO);
    }

    function pinduoduo_task_capital_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('capital_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_PINDUODUO);
    }

    function dianfu_task_commission_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('commission_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_DIANFU);
    }

    function pinduoduo_task_commission_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('commission_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_PINDUODUO);
    }

    function liuliang_task_commission_clearing_complete($task_id)
    {
        if (empty($task_id) || !is_numeric($task_id)) {
            return false;
        }

        $this->db->set('commission_clearing_status', self::TASK_CLEARING_STATUS_YES);
        $this->db->where('id', $task_id);
        return $this->db->update(self::DB_TASK_LIULIANG);
    }

    function get_liuliang_DJD_tasks()
    {
        $sql = "SELECT
                        id,
                        task_type,
                        is_preferred,
                        commission_to_buyer,
                        device_type,
                        shop_name
                    FROM
                        " . self::DB_TASK_LIULIANG . "
                    WHERE
                        STATUS = " . self::TASK_STATUS_DJD . " AND refund_clearing_status = 0 AND start_time <= NOW() AND end_time > NOW() Order By start_time";
        return $this->db->query($sql)->result();
    }

    function get_dianfu_DJD_tasks()
    {
        $sql = "SELECT
                        id,
                        task_type,
                        is_preferred,
                        is_huabei,
                        sex_limit,
                        age_limit,
                        tb_rate_limit,
                        tb_area_limit,
                        commission_to_buyer,
                        device_type,
                        shop_name
                    FROM
                        " . self::DB_TASK_DIANFU . "
                    WHERE
                        STATUS = " . self::TASK_STATUS_DJD . " AND refund_clearing_status = 0 AND start_time <= NOW() AND end_time > NOW() Order By start_time";
        return $this->db->query($sql)->result();
    }

    function get_pinduoduo_DJD_tasks()
    {
        $sql = "SELECT
                        id,
                        task_type,
                        is_preferred,
                        commission_to_buyer,
                        device_type,
                        shop_name
                    FROM
                        " . self::DB_TASK_PINDUODUO . "
                    WHERE
                        STATUS = " . self::TASK_STATUS_DJD . " AND refund_clearing_status = 0 AND start_time <= NOW() AND end_time > NOW() Order By start_time";
        return $this->db->query($sql)->result();
    }

    function cancel_timeout_DCZ_tasks()
    {
        // 超时未做的垫付任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));
//        $errorLog = $this->db->last_query();
        $time_out_dianfu_task = $this->db->get(self::DB_TASK_DIANFU)->result();
        if (!empty($time_out_dianfu_task)) {
            foreach ($time_out_dianfu_task as $v) {
                // 多天任务 且 已完成第一天任务的单子不予撤销
                if ($v->task_type == TASK_TYPE_DT && $v->cur_task_day > 1) {
                    continue;
                }
                $this->add_cancel_task_record($v);
                $this->clean_dianfu_task_zuodan_info($v->id);
            }
        };

        // 超时未做的拼多多任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));

        $time_out_pinduoduo_task = $this->db->get(self::DB_TASK_PINDUODUO)->result();
        if (!empty($time_out_pinduoduo_task)) {
            foreach ($time_out_pinduoduo_task as $v) {
                $this->add_cancel_task_record($v);
                $this->clean_pinduoduo_task_zuodan_info($v->id);
            }
        };

        // 超时未做的流量任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));

        $time_out_liuliang_task = $this->db->get(self::DB_TASK_LIULIANG)->result();
        if (!empty($time_out_liuliang_task)) {
            foreach ($time_out_liuliang_task as $v) {
                $this->add_cancel_task_record($v);
                $this->clean_liuliang_task_zuodan_info($v->id);
            }
        };
    }
    /**
     * 临时定时任务
     */
    function cancel_timeout_DCZ_tasks_temp()
    {

        // 超时未做的垫付任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));
        $time_out_dianfu_task = $this->db->get(self::DB_TASK_DIANFU)->result();
        if (!empty($time_out_dianfu_task)) {
            foreach ($time_out_dianfu_task as $v) {
                // 多天任务 且 已完成第一天任务的单子不予撤销
                if ($v->task_type == TASK_TYPE_DT && $v->cur_task_day > 1) {
                    continue;
                }
                $this->add_cancel_task_record($v);
                $this->clean_dianfu_task_zuodan_info($v->id);
            }
        };

        // 超时未做的拼多多任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));

        $time_out_pinduoduo_task = $this->db->get(self::DB_TASK_PINDUODUO)->result();
        if (!empty($time_out_pinduoduo_task)) {
            foreach ($time_out_pinduoduo_task as $v) {
                $this->add_cancel_task_record($v);
                $this->clean_pinduoduo_task_zuodan_info($v->id);
            }
        };

        // 超时未做的流量任务处理
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('gmt_taking_task <=', date("Y-m-d H:i:s", strtotime('-' . ZUODAN_SHIJIAN_MIN . ' minutes')));

        $time_out_liuliang_task = $this->db->get(self::DB_TASK_LIULIANG)->result();
        if (!empty($time_out_liuliang_task)) {
            foreach ($time_out_liuliang_task as $v) {
                $this->add_cancel_task_record($v);
                $this->clean_liuliang_task_zuodan_info($v->id);
            }
        };
        echo 'success';
        echo '<br>';
        echo date('Y-m-d H:i:s');
    }

    // 多天任务 - 未及时操作关闭任务
    function cancel_timeout_DCZ_tasks_dt($deadLine)
    {
        // 查询超时未做的多天垫付任务
        $this->db->where('task_type', TASK_TYPE_DT);
        $this->db->where('status', self::TASK_STATUS_DCZ);
        $this->db->where('next_start_time <', $deadLine);
        $time_out_dianfu_task = $this->db->get(self::DB_TASK_DIANFU)->result();

        if (!empty($time_out_dianfu_task)) {
            $this->load->model('paycore');
            foreach ($time_out_dianfu_task as $v) {
                // 新增一条记录到订单删除表
                $this->add_cancel_task_record_xtgb_dt($v, self::TASK_STATUS_XTGB_DT, '未及时操作关闭任务');
                // 修改多天垫付单状态
                $this->clean_task_zuodan_duotian_info_xtgb($v->id, self::TASK_STATUS_XTGB_DT);

                // 关闭订单后的退款清算
                if ($v->refund_clearing_status == self::TASK_CLEARING_STATUS_NO && $this->dianfu_task_refund_clearing_complete($v->id)) {
                    // 退回垫付任务本金
                    $this->paycore->capital_refund($v->seller_id, $v->single_task_capital, '买手未及时操作，系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '本金');
                    // 退回垫付任务的佣金和服务费
                    $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '买手未及时操作，系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                    // 退回垫付任务快递费
                    if ($v->is_express == '1') {
                        $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                    }
                }
            }
        }
    }

    //商家审核拒绝后，买家不操作的重置处理
    function cancel_shenhejj_CZDD_tasks($id, $type)
    {
        if ($type == 'LIULIANG') {
            $this->db->where('id', $id);
            $shenhejj_liuliang_xtcz_task = $this->db->get(self::DB_TASK_LIULIANG)->result();
            if (!empty($shenhejj_liuliang_xtcz_task)) {
                foreach ($shenhejj_liuliang_xtcz_task as $v) {
                    $this->add_cancel_task_record_xtcz($v, $id);
                    $this->clean_liuliang_task_zuodan_info_xtcz($v->id);
                }
            };
        } else if ($type == 'DIANFU' || $type == 'DUOTIAN') {
            $this->db->where('id', $id);
            $shenhejj_dianfu_xtcz_task = $this->db->get(self::DB_TASK_DIANFU)->result();
            if (!empty($shenhejj_dianfu_xtcz_task)) {
                foreach ($shenhejj_dianfu_xtcz_task as $v) {
                    $this->add_cancel_task_record_xtcz($v, $id);
                    $this->clean_dianfu_task_zuodan_info_xtcz($v->id);
                }
            };
        } else if ($type == 'PINDUODUO') {
            $this->db->where('id', $id);
            $shenhejj_pinduoduo_xtcz_task = $this->db->get(self::DB_TASK_PINDUODUO)->result();
            if (!empty($shenhejj_pinduoduo_xtcz_task)) {
                foreach ($shenhejj_pinduoduo_xtcz_task as $v) {
                    $this->add_cancel_task_record_xtcz($v, $id);
                    $this->clean_pinduoduo_task_zuodan_info_xtcz($v->id);
                }
            };
        }
    }

    //商家审核拒绝后，买家不操作的頂单关闭处理
    function cancel_shenhejj_GBDD_tasks($id, $type)
    {
        if ($type == 'LIULIANG') {
            $this->db->where('id', $id);
            $shenhejj_liuliang_xtgb_task = $this->db->get(self::DB_TASK_LIULIANG)->result();
            if (!empty($shenhejj_liuliang_xtgb_task)) {
                foreach ($shenhejj_liuliang_xtgb_task as $v) {
                    $this->add_cancel_task_record_xtgb($v);
                    $this->clean_task_zuodan_liuliang_info_xtgb($id);
                }
            };
        } else if ($type == 'DIANFU' || $type == 'DUOTIAN') {
            $this->db->where('id', $id);
            $shenhejj_dianfu_xtgb_task = $this->db->get(self::DB_TASK_DIANFU)->result();
            if (!empty($shenhejj_dianfu_xtgb_task)) {
                foreach ($shenhejj_dianfu_xtgb_task as $v) {
                    $this->add_cancel_task_record_xtgb($v);
                    $this->clean_task_zuodan_dianfu_info_xtgb($id);
                }
            };
        } else if ($type == 'PINDUODUO') {
            $this->db->where('id', $id);
            $shenhejj_pinduoduo_xtgb_task = $this->db->get(self::DB_TASK_PINDUODUO)->result();
            if (!empty($shenhejj_pinduoduo_xtgb_task)) {
                foreach ($shenhejj_pinduoduo_xtgb_task as $v) {
                    $this->add_cancel_task_record_xtgb($v);
                    $this->clean_task_zuodan_pinduoduo_info_xtgb($id);
                }
            };
        }
    }

    private function add_cancel_task_record($task_obj)
    {
        $cancel_reason = "任务超时，系统自动撤销";
        $status = self::TASK_STATUS_YCX;
        $insert_data = array(
            'task_type' => $task_obj->task_type,
            'task_id' => $task_obj->id,
            'seller_id' => $task_obj->seller_id,
            'parent_order_id' => $task_obj->parent_order_id,
            'shop_id' => $task_obj->shop_id,
            'device_type' => $task_obj->device_type,
            'item_id' => $task_obj->item_id,
            'item_pic' => $task_obj->item_pic,
            'buyer_id' => $task_obj->buyer_id,
            'buyer_tb_nick' => $task_obj->buyer_tb_nick,
            'cancel_reason' => $cancel_reason,
            'status' => $status
        );

        return $this->db->insert(self::DB_CANCELLED_TASKS, $insert_data);
    }

    private function add_cancel_task_record_xtcz($task_obj)
    {

        $cancel_reason = "商家审核拒绝后，买家不再操作，系统重置";
        $insert_data = array(
            'task_type' => $task_obj->task_type,
            'task_id' => $task_obj->id,
            'seller_id' => $task_obj->seller_id,
            'parent_order_id' => $task_obj->parent_order_id,
            'shop_id' => $task_obj->shop_id,
            'device_type' => $task_obj->device_type,
            'item_id' => $task_obj->item_id,
            'item_pic' => $task_obj->item_pic,
            'buyer_id' => $task_obj->buyer_id,
            'buyer_tb_nick' => $task_obj->buyer_tb_nick,
            'cancel_reason' => $cancel_reason,
            'status' => self::TASK_STATUS_XTCZ
        );

        return $this->db->insert(self::DB_CANCELLED_TASKS, $insert_data);
    }

    private function add_cancel_task_record_xtgb($task_obj)
    {
        $cancel_reason = "商家审核拒绝后，买家不再操作，系统关闭";
        $insert_data = array(
            'task_type' => $task_obj->task_type,
            'task_id' => $task_obj->id,
            'seller_id' => $task_obj->seller_id,
            'parent_order_id' => $task_obj->parent_order_id,
            'shop_id' => $task_obj->shop_id,
            'device_type' => $task_obj->device_type,
            'item_id' => $task_obj->item_id,
            'item_pic' => $task_obj->item_pic,
            'buyer_id' => $task_obj->buyer_id,
            'buyer_tb_nick' => $task_obj->buyer_tb_nick,
            'cancel_reason' => $cancel_reason,
            'status' => self::TASK_STATUS_XTGB
        );

        return $this->db->insert(self::DB_CANCELLED_TASKS, $insert_data);
    }

    private function add_cancel_task_record_xtgb_dt($task_obj, $status, $cancel_reason)
    {
        $insert_data = array(
            'task_type' => $task_obj->task_type,
            'task_id' => $task_obj->id,
            'seller_id' => $task_obj->seller_id,
            'parent_order_id' => $task_obj->parent_order_id,
            'shop_id' => $task_obj->shop_id,
            'device_type' => $task_obj->device_type,
            'item_id' => $task_obj->item_id,
            'item_pic' => $task_obj->item_pic,
            'buyer_id' => $task_obj->buyer_id,
            'buyer_tb_nick' => $task_obj->buyer_tb_nick,
            'cancel_reason' => $cancel_reason,
            'status' => $status
        );

        return $this->db->insert(self::DB_CANCELLED_TASKS, $insert_data);
    }


    /**
     * @name 处理已经接单但是未做超时的订单
     * @param $task_id
     * @return bool
     * @author chen.jian
     */
    private function clean_pinduoduo_task_zuodan_info($task_id)
    {
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'huobi_1st_prove_pic' => null,
            'huobi_2nd_prove_pic' => null,
            'huobi_3rd_prove_pic' => null,
            'item_check_status' => STATUS_DISABLE,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'fukuan_prove_pic' => null,
            'haoping_prove_pic' => null,
            'status' => self::TASK_STATUS_DJD
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_PINDUODUO, $update_data)) {
            error_log('clean pinduoduo task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    private function clean_dianfu_task_zuodan_info($task_id)
    {
        $status = self::TASK_STATUS_DJD;
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'huobi_1st_prove_pic' => null,
            'huobi_2nd_prove_pic' => null,
            'huobi_3rd_prove_pic' => null,
            'item_check_status' => STATUS_DISABLE,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'fukuan_prove_pic' => null,
            'haoping_prove_pic' => null,
            'status' => $status
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_DIANFU, $update_data)) {
            error_log('clean dianfu task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    private function clean_liuliang_task_zuodan_info($task_id)
    {
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'item_check_status' => null,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'favorite_shop_prove_pic' => null,
            'favorite_item_prove_pic' => null,
            'add_cart_prove_pic' => null,
            'status' => self::TASK_STATUS_DJD
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_LIULIANG, $update_data)) {
            error_log('clean liuliang task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    //重置系统流量订单
    private function clean_liuliang_task_zuodan_info_xtcz($task_id)
    {
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'item_check_status' => null,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'favorite_shop_prove_pic' => null,
            'favorite_item_prove_pic' => null,
            'add_cart_prove_pic' => null,
            'status' => self::TASK_STATUS_DJD
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_LIULIANG, $update_data)) {
            error_log('clean liuliang task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    //重置系统垫付订单
    private function clean_dianfu_task_zuodan_info_xtcz($task_id)
    {
        $status = self::TASK_STATUS_DJD;
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'huobi_1st_prove_pic' => null,
            'huobi_2nd_prove_pic' => null,
            'huobi_3rd_prove_pic' => null,
            'item_check_status' => STATUS_DISABLE,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'fukuan_prove_pic' => null,
            'haoping_prove_pic' => null,
            'order_number' => null,
            'status' => $status
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_DIANFU, $update_data)) {
            error_log('clean dianfu task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    //重置系统拼多多订单
    private function clean_pinduoduo_task_zuodan_info_xtcz($task_id)
    {
        $update_data = array(
            'buyer_id' => null,
            'buyer_tb_nick' => null,
            'gmt_taking_task' => null,
            'zhusou_prove_pic' => null,
            'huobi_1st_prove_pic' => null,
            'huobi_2nd_prove_pic' => null,
            'huobi_3rd_prove_pic' => null,
            'item_check_status' => STATUS_DISABLE,
            'zhubaobei_prove_pic' => null,
            'fubaobei_prove_pic' => null,
            'fukuan_prove_pic' => null,
            'haoping_prove_pic' => null,
            'order_number' => null,
            'status' => self::TASK_STATUS_DJD
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_PINDUODUO, $update_data)) {
            error_log('clean pinduoduo task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    //系统关闭订单
    private function clean_task_zuodan_liuliang_info_xtgb($task_id)
    {
        $update_data = array(
            'status' => self::TASK_STATUS_XTGB
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_LIULIANG, $update_data)) {
            error_log('clean liuliang task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    private function clean_task_zuodan_pinduoduo_info_xtgb($task_id)
    {
        $update_data = array(
            'status' => self::TASK_STATUS_XTGB
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_PINDUODUO, $update_data)) {
            error_log('clean liuliang task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    private function clean_task_zuodan_dianfu_info_xtgb($task_id)
    {
        $update_data = array(
            'status' => self::TASK_STATUS_XTGB
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_DIANFU, $update_data)) {
            error_log('clean dianfu task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    private function clean_task_zuodan_duotian_info_xtgb($task_id, $status)
    {
        $update_data = array(
            'status' => $status
        );

        $this->db->where('id', $task_id);
        if (!$this->db->update(self::DB_TASK_DIANFU, $update_data)) {
            error_log('clean duotian task zuodan info failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    function get_audit_task_list($r)
    {
        if (empty($r['seller_id'])) {
            return null;
        }

        if (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DF) {
            $db_name = self::DB_TASK_DIANFU;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_LL) {
            $db_name = self::DB_TASK_LIULIANG;
        } else {
            return null;
        }

        $this->db->select(array('id', 'parent_order_id', 'item_title', 'item_url', 'item_pic', 'gmt_taking_task', 'buyer_tb_nick', 'status'));
        $this->db->where('seller_id', $r['seller_id']);
        $this->db->where_in('status', array(self::TASK_STATUS_MJSH, self::TASK_STATUS_HPSH));
        $this->db->order_by('id', 'DESC');
        return $this->db->get($db_name)->result();
    }

    function update_task_status($i, $n_status, $task_type)
    {
        if (empty($i) || empty($n_status) || empty($task_type)) {
            return false;
        }

        $this->db->set('status', $n_status);
        $this->db->where('id', $i);

        if ($task_type == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            return $this->db->update(self::DB_TASK_LIULIANG);
        }
        return false;
    }

    function cancel_task($i, $task_type, $seller_id)
    {
        if ($this->check_task_seller_n_status($i, $task_type, $seller_id, self::TASK_STATUS_DJD)) {
            return $this->update_task_status($i, self::TASK_STATUS_YCX, $task_type);
        }
        return false;
    }

    function check_task_seller_n_status($i, $task_type, $seller_id, $t_status)
    {
        if (empty($i) || empty($seller_id) || empty($task_type) || empty($t_status)) {
            return false;
        }

        $this->db->select(array('seller_id', 'status'));
        $this->db->where('id', $i);
        $this->db->limit(1);

        if ($task_type == TASK_TYPE_DF) {
            $result = $this->db->get(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            $result = $this->db->get(self::DB_TASK_LIULIANG);
        } else {
            return false;
        }

        if ($result->num_rows() > 0) {
            if ($result->row()->status != $t_status) {
                return false;
            }
            if ($result->row()->seller_id != $seller_id) {
                return false;
            }
            return true;
        }

        return false;
    }


    function get_promote_relation($promote_seller_id){
        $this->db->select('owner_id')->where(['promote_id' => $promote_seller_id, 'status' => STATUS_PASSED]);
        $query = $this->db->get(self::DB_PROMOTE_RELATION);
        if ($query->num_rows() > 0) {
            return $query->row()->owner_id;
        }else{
            return null;
        }
        return null;
    }

    function get_seller_agent($owner_seller_id){
        $this->db->select(['seller_id', 'seller_name', 'pdd_prepaid', 'tb_flow', 'tb_prepaid'])->where(['seller_id' => $owner_seller_id, 'status' => STATUS_PASSED]);
        $query = $this->db->get(self::DB_SELLER_AGENT);
        if ($query->num_rows() > 0) {
            return $query->row();
        }else{
            return null;
        }
        return null;



        $promoteAgentSql = sprintf('SELECT %s FROM %s WHERE %s = %d LEFT JOIN %s ON %s = %s', '*', self::DB_PROMOTE_RELATION, self::DB_PROMOTE_RELATION.'.promote_id', $promote_seller_id, self::DB_SELLER_AGENT, self::DB_SELLER_AGENT.'.seller_id', self::DB_PROMOTE_RELATION.'.owner_id');
        return $this->db->query($promoteAgentSql);
    }

    function get_buyer_agent($owner_buyer_id){
        $this->db->select(['buyer_id', 'buyer_name', 'pdd_prepaid', 'tb_flow', 'tb_prepaid'])->where(['buyer_id' => $owner_buyer_id, 'status' => STATUS_PASSED]);
        $query = $this->db->get(self::DB_BUYER_AGENT);
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function update_task_status_backto_check($id)
    {
        $this->db->set('status', self::TASK_STATUS_MJSH);
        $this->db->where('id', intval($id));
        $this->db->where('status', self::TASK_STATUS_SSZ);
        return $this->db->update(self::DB_TASK_DIANFU);
    }

    public function update_dianfu_task($id, $data)
    {
        $this->db->where('id', intval($id));
        return $this->db->update(self::DB_TASK_DIANFU, $data);
    }

    public function update_appeal_task($id, $data)
    {
        $data['status'] = self::TASK_STATUS_MJSH;
        $this->db->where('id', intval($id));
        return $this->db->update(self::DB_TASK_DIANFU, $data);
    }

    public function get_dianfu_task_info_field($id, $aFields = [])
    {
        if (!empty($aFields)) $this->db->select($aFields);
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_DIANFU)->row();
    }

    public function get_val_dianfu($id, $task_type, $sField)
    {
        $this->db->select($sField);
        $this->db->where('id', $id);
        $this->db->limit(1);

        switch ($task_type) {
            case TASK_TYPE_DF:
                $tb = self::DB_TASK_DIANFU;
                break;
            case TASK_TYPE_LL:
                $tb = self::DB_TASK_LIULIANG;
                break;
            case TASK_TYPE_PDD:
                $tb = self::DB_TASK_PINDUODUO;
                break;
            default: return '';
                break;
        }

        $query = $this->db->get($tb);
        if ($query->num_rows() > 0) {
            return $query->row()->$sField;
        }
        return '';
    }

    public function checkTASKClose($id, $task_type)
    {
        $status = $this->get_val_dianfu($id, $task_type, 'status');
        if (in_array($status, [self::TASK_STATUS_XTGB, self::TASK_STATUS_YCX])) {
            return true;
        }
        return false;
    }


}