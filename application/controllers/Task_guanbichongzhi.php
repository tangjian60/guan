<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_guanbichongzhi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('taskcachemanager');
        $this->load->model('taskengine');
        $this->load->model('paycore');
    }

    public function index()
    {
        //重置或关闭商家审核不通过，买家又不操作的单子
        $id = $this->input->post('id', true);
        $act = $this->input->post('act', true);
        $type = $this->input->post('type', true);
        if ($act == "chongzhi") {
            if (!empty($id) && is_numeric($id)) {
                $this->taskengine->cancel_shenhejj_CZDD_tasks($id,$type);
            }
        } else if ($act == "guanbi") {
            if (!empty($id) && is_numeric($id)) {
                $this->taskengine->cancel_shenhejj_GBDD_tasks($id,$type);
            }
        }
        if ($type == "LIULIANG") {
            // 商家审核不通过买家不继续操作系统关闭流量单退款清算
            $t8 = $this->taskengine->get_unclearing_xitonggb_liuliang_tasks($id);
            if (!empty($t8)) {
                foreach ($t8 as $v) {
                    if ($this->taskengine->liuliang_task_refund_clearing_complete($v->id)) {
                        $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '商家审核不通过买家不继续操作系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                    }
                }
            }
        } else if ($type == "DIANFU" || $type == 'DUOTIAN') {
            // 商家审核不通过买家不继续操作系统关闭墊付单退款清算
            $t9 = $this->taskengine->get_unclearing_xitonggb_dianfu_tasks($id);
            if (!empty($t9)) {
                foreach ($t9 as $v) {
                    if ($this->taskengine->dianfu_task_refund_clearing_complete($v->id)) {
                        // 退回垫付任务本金
                        $this->paycore->capital_refund($v->seller_id, $v->single_task_capital, '商家审核不通过买家不继续操作系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '本金');
                        // 退回垫付任务的佣金和服务费
                        $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '商家审核不通过买家不继续操作系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                        // 退回垫付任务快递费
                        if ($v->is_express == '1') {
                            $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                        }
                    }
                }
            }

            // TODO... 商家如果之前垫付了快递费，则返还 【选择了平台快递】
//            $row = $this->taskengine->get_unclearing_xitonggb_dianfu_tasks_express($id);
//            if(!empty($row)) {
//                if ($this->taskengine->dianfu_task_express_clearing_complete($id)) {
//                    $this->paycore->express_refund($row->seller_id, $row->single_task_express, '退回订单' . encode_id($row->parent_order_id) . '任务' . encode_id($row->id) . '快递费');
//                }
//            }

        } else if ($type == "PINDUODUO") {
            // 商家审核不通过买家不继续操作系统关闭拼多多单退款清算
            $t10 = $this->taskengine->get_unclearing_xitonggb_pinduoduo_tasks($id);
            if (!empty($t10)) {
                foreach ($t10 as $v) {
                    if ($this->taskengine->dianfu_task_refund_clearing_complete($v->id)) {
                        // 退回拼多多任务本金
                        $this->paycore->capital_refund($v->seller_id, $v->single_task_capital, '商家审核不通过买家不继续操作系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '本金');
                        // 退回拼多多任务的佣金和服务费
                        $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '商家审核不通过买家不继续操作系统关闭单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                    }
                }
            }
        }
        echo build_response_str(CODE_SUCCESS, 'OK');
    }



}