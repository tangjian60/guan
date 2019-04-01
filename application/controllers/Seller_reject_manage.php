<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Seller_reject_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('reject');
    }

    public function index()
    {
        $params_get = [];
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['seller_name'] = $this->input->get('seller_name', TRUE);
            $this->Data['buyer_name'] = $this->input->get('buyer_name', TRUE);
            $this->Data['createDate'] = $this->input->get('createDate', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);

            $params_get = $this->Data;
            unset($params_get['member_id'] ,$params_get['real_name'], $params_get['menus']);
            $params_get = $this->_build_query($params_get);

            $this->Data['data'] = $this->reject->get_reject_list($this->Data);
        }

        $this->Data['TargetPage'] = 'seller/reject_list';
        $this->Data['params_get'] = $params_get;
        unset($params_get);
        $this->load->view('frame_main', $this->Data);
    }

    public function update_order_info()
    {
        $this->Data['apply_id'] = $this->input->get('id', TRUE);
        $this->Data['task_id'] = $this->input->get('task_id', TRUE);
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);

        $this->Data['seller_name'] = $this->input->get('seller_name', TRUE);
        $this->Data['buyer_name'] = $this->input->get('buyer_name', TRUE);
        $this->Data['createDate'] = $this->input->get('createDate', TRUE);
        $this->Data['status'] = $this->input->get('status', TRUE);

        $params_get = $this->Data;
        unset($params_get['real_name'], $params_get['menus'],$params_get['apply_id'],$params_get['task_id']);
        $params_get = $this->_build_query($params_get);
        $this->load->model('taskengine');
        $this->Data['task_detail'] = $this->taskengine->get_dianfu_task_info($this->Data['task_id']);
        //print_r($this->Data['task_detail']);exit;

        $this->Data['TargetPage']   = 'seller/page_update_task_info';
        $this->Data['params_get'] = $params_get;
        unset($params_get);
        $this->load->view('frame_main', $this->Data);
    }

    public function update_order_info_handle()
    {
        //die(build_response_str(CODE_BAD_REQUEST, "1232435"));
        if (!$this->input->is_ajax_request()) { die(build_response_str(CODE_BAD_REQUEST, "非法请求")); }
        // 接收参数
        $post_data = $this->input->post();
        if (invalid_parameter($post_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $apply_id = decode_id($post_data['apply_id']);
        $task_id = decode_id($post_data['task_id']);
        if (empty($apply_id) || empty($task_id)) {
            die(build_response_str(CODE_BAD_REQUEST, "参数错误"));
        }

        $aKeys = [
            'order_number', 'single_task_capital', 'zhusou_prove_pic', 'huobi_1st_prove_pic', 'huobi_2nd_prove_pic', 'huobi_3rd_prove_pic',
            'zhubaobei_prove_pic', 'fubaobei_prove_pic', 'fukuan_prove_pic', 'haoping_prove_pic'
        ];
        foreach ($aKeys as $key) {
            $aData[$key] = $post_data[$key];
        }

        $this->load->model('taskengine');
        if ( true === $this->taskengine->checkTASKClose($task_id, TASK_TYPE_DF) ) {
            echo build_response_str(CODE_BAD_REQUEST, "订单已不关闭，不允许修改！");return;
        }

        if ($this->taskengine->update_appeal_task($task_id, $aData)) {
            $this->reject->goon_task($apply_id);
            $this->reject->add_oper_log(0, $this->get_admin_id(), $this->get_admin_name(), 5, 1, "修改申诉单信息");
            echo build_response_str(CODE_SUCCESS, "修改成功");return;
        }
        echo build_response_str(CODE_BAD_REQUEST, "修改失败");return;
    }


    public function cancel_task()
    {
        if (!$this->input->is_ajax_request()) { throw new Exception("非法请求", CODE_BAD_REQUEST); }
        $apply_id = decode_id($this->input->post('apply_id', TRUE));
        $task_id = decode_id($this->input->post('task_id', TRUE));
        if ( empty($apply_id) || empty($task_id) ) { throw new Exception("非法请求", CODE_BAD_REQUEST); }

        $this->load->model('taskengine');
        $this->load->model('paycore');

        // 修改申诉单状态3 已处理-关闭订单
        if ($this->reject->close_task($apply_id)) {

            if ( true === $this->taskengine->checkTASKClose($task_id, TASK_TYPE_DF) ) {
                echo build_response_str(CODE_BAD_REQUEST, "请勿重复关闭订单！");return;
            }

            // 修改垫付到单状态  13 撤销
            $this->taskengine->cancel_shenhejj_GBDD_tasks($task_id, TASK_TYPE_DF);
            // 商家审核不通过买家不继续操作系统关闭墊付单退款清算
            $t9 = $this->taskengine->get_unclearing_xitonggb_dianfu_tasks($task_id);
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

            // 操作日志
            $this->reject->add_oper_log(0, $this->get_admin_id(), $this->get_admin_name(), 5, 2, "关闭申诉单任务");

            echo build_response_str(CODE_SUCCESS, "修改成功");return;
        }
        echo build_response_str(CODE_BAD_REQUEST, "修改失败");return;
    }

    public function cancel_apply()
    {
        if (!$this->input->is_ajax_request()) { throw new Exception("非法请求", CODE_BAD_REQUEST); }
        $apply_id = decode_id($this->input->post('apply_id', TRUE));
        $task_id = decode_id($this->input->post('task_id', TRUE));
        if ( empty($apply_id) || empty($task_id) ) { throw new Exception("非法请求", CODE_BAD_REQUEST); }

        $this->load->model('taskengine');
        if ($this->taskengine->update_task_status_backto_check($task_id)) {
            $this->reject->cancel_apply($apply_id);
            // 操作日志
            $this->reject->add_oper_log(0, $this->get_admin_id(), $this->get_admin_name(), 5, 3, "申诉单放弃申诉");
            echo build_response_str(CODE_SUCCESS, "修改成功");return;
        }
        echo build_response_str(CODE_BAD_REQUEST, "修改失败");return;
    }

    private function _build_query($query_data, $encoding = false)
    {
        $res = '';
        $count = count($query_data);
        $i = 0;
        foreach ($query_data as $k => $v) {
            if ($encoding === true) {
                $v = urlencode($v);
            }
            if ($i < $count - 1) {
                $res .= $k . '=' . $v . '&';
            } else {
                $res .= $k . '=' . $v;
            }
            $i++;
        }
        return $res;
    }

}