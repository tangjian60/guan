<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Withdraw extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('paycore');
        $this->load->model('withdraw_model');
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = trim($this->input->get('i_page', TRUE));
            $this->Data['member_id'] = trim($this->input->get('member_id', TRUE));
            $this->Data['member_name'] = trim($this->input->get('member_name', TRUE));
            $this->Data['name'] = trim($this->input->get('name', TRUE)); //conflict with real_name
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['excel'] = $this->input->get('excel', TRUE);
            if ($this->Data['excel'] == 1) { unset($this->Data['i_page']) ;}
            $this->Data['data'] = $this->hiltoncore->get_withdraw_records($this->Data);
            $this->Data['reject'] = \CONSTANT\Audit::getRejectText('withdraw');
        }

        if (!isset($this->Data['status'])){
            $this->Data['status'] = STATUS_CHECKING;
        }

        if ($this->input->get('excel')){
            $search['start_time'] = $this->input->get('start_time', TRUE);
            $search['end_time'] = $this->input->get('end_time', TRUE);
            $search['status'] = $this->input->get('status', TRUE);
            $handle = $this->withdraw_model->updateExcelDataStatus($search);

            //$handle = true; // TODO... 暂时不改状态

            (true == $handle) && $this->dump($this->Data['data']);
            //return;
        }

        $this->Data['TargetPage'] = 'page_withdraw';
        $this->load->view('frame_main', $this->Data);
    }

    public function doDumpCheck()
    {
        if ($this->withdraw_model->hasRecordStatusIsRemitIng()){
            echo(build_response_str(CODE_BAD_REQUEST, "还有数据未打款，请先处理再导出"));
        }else{
            echo build_response_str(CODE_SUCCESS, "操作成功");
        }
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

        if ($act == 'withdraw_approve') {
            $withdraw_id = $this->input->post('withdraw_id', true);
            if ($this->hiltoncore->approve_withdraw($withdraw_id, $this->get_admin_id())) {
                // 提现审核通过后记录平台手续费收益 TODO... 暂时注释， 搁置
//                $withdraw_record_info = $this->hiltoncore->get_withdraw_info($withdraw_id);
//                if (!empty($withdraw_record_info) && $withdraw_record_info->status == STATUS_PASSED) {
//                    $this->paycore->withdraw_service_fee_payoff(
//                        SYSTEM_INCOME, $withdraw_record_info->withdraw_service_fee,
//                        '提现单' . encode_id($withdraw_id) . '手续费'
//                    );
//                }
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'withdraw_reject') {

            $withdraw_id = $this->input->post('withdraw_id', true);
            if (empty($withdraw_id)) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }
            $tixian_type = $this->input->post('tixian_type', true);
            if (empty($tixian_type)) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }
            $withdraw_record_info = $this->hiltoncore->get_withdraw_info($withdraw_id);
            if (empty($withdraw_record_info) || $withdraw_record_info->status != STATUS_CHECKING) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if (!$this->hiltoncore->reject_withdraw($withdraw_id, $this->get_admin_id())) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if ($this->paycore->withdraw_failed_return($withdraw_record_info->user_id, $withdraw_record_info->amount + $withdraw_record_info->withdraw_service_fee, $this->get_admin_id(),$tixian_type,$withdraw_record_info->amount) != Paycore::PAY_CODE_SUCCESS) {
                die(build_response_str(CODE_BAD_REQUEST, "系统错误，提现预扣资金未退回"));
            }

            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }



    private function dump($data)
    {

        $config = array(
            'charActors' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            'widthSize' => [20, 20, 20, 20, 70, 30, 30, 20, 20, 20],
            'titleName' => ['提现用户名', '提现时间', '收款账户列', '收款户名列', '转账金额列', '备注列', '收款银行列', '收款银行支行列', '收款省/直辖市列','收款市县列']
        );

        $this->load->library('excel', $config);
        $this->load->library('PHPExcel');

        $a = 2;
        $excel = new PHPExcel();
        $file_name = sprintf("%s-%s", $this->get_admin_name(), date("Y-m-d-H"));
        $ids_arr =  array();
        foreach($data as $i => $v){
            $arr = $this->address($v->bank_address);
            $excel->getActiveSheet()->setCellValue('A' . $a, $v->user_name);
            $excel->getActiveSheet()->setCellValue('B' . $a, $v->create_time);
            $excel->getActiveSheet()->setCellValue('C' . $a, ' ' . $v->bank_card_num);
            $excel->getActiveSheet()->setCellValue('D' . $a, $v->real_name);
            $excel->getActiveSheet()->setCellValue('E' . $a, $v->amount);
            $excel->getActiveSheet()->setCellValue('F' . $a, '');
            $excel->getActiveSheet()->setCellValue('G' . $a, $v->bank_name);
            $excel->getActiveSheet()->setCellValue('H' . $a, $v->bank_branch);
            $excel->getActiveSheet()->setCellValue('I' . $a, $arr['province']);
            $excel->getActiveSheet()->setCellValue('J' . $a, $arr['city']);
            $a++;
            array_push($ids_arr, $v->id);
        }

        $this->excel->dump($excel, $file_name);
    }

    //没有考虑自治州等其他情况
    private function address($address)
    {
        preg_match('/(.*?(西藏|广西|内蒙古|新疆|宁夏|北京市|天津市|上海市|重庆市|省))/', $address, $matches);
        if (count($matches) > 1) {
            $province = $matches[0];
        }

        $pos = mb_stripos($address, $province);
        $len = mb_strlen($address);
        $len2 = mb_strlen($province);

        $address = mb_substr($address,$pos+$len2, $len);
        preg_match('/(.*?(市|自治州|地区|区划|县|盟|单位))/', $address, $matches);
        if (count($matches) > 1) {
            $city = $matches[0];
        }

        return [
            'province' => preg_replace('/(市|省)/', '', $province),
            'city' => preg_replace('/(市|自治州|地区|区划|县|盟|单位)/', '', $city),
        ];
    }

    // 通过提现申请审核
    public function accept_handle()
    {
        $this->load->model('withdraw_model');
        if ($this->withdraw_model->doAcceptWithdraw(intval($this->input->post('withdraw_id', true)))) {
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }
        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败(请稍后再试)");
    }

    public function reject()
    {
        $this->load->model('withdraw_model');
        $this->Data['withdraw_info'] = $this->withdraw_model->getWithdrawrecordInfoById(trim($this->input->get('id', true)));
        $this->Data['TargetPage'] = 'withdraw/reject';
        $this->load->view('frame_main', $this->Data);
    }

    public function reject_handle()
    {
        $memo = trim($this->input->post('memo', true));
        if (mb_strlen($memo) <= 0){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请填写拒绝原因");
            return;
        }

        $withdraw_id = trim($this->input->post('id', true));
        $this->load->model('message_model');
        $this->load->model('withdraw_model');
        $withdraw_info = $this->withdraw_model->getWithdrawrecordInfoById($withdraw_id);
        if (empty($withdraw_info)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "未知的错误[数据不存在]");
            return;
        }

        if (STATUS_PASSED == $withdraw_info->status || $withdraw_info->status == STATUS_FAILED){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请勿重复审核");
            return;
        }

        $oper_id = $this->get_admin_id();
        $title = '会员提现管理审核--拒绝';
        $flag = $this->message_model->add($memo, $oper_id, $withdraw_info->user_id, $title);

        if ($flag &&
            $this->hiltoncore->reject_withdraw($withdraw_info->id, $oper_id) &&
            ($this->paycore->withdraw_failed_return($withdraw_info->user_id, $withdraw_info->amount + $withdraw_info->withdraw_service_fee, $this->get_admin_id(),$withdraw_info->tixian_type,$withdraw_info->amount)
            == Paycore::PAY_CODE_SUCCESS))
        {
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }else{
            echo build_response_str(CODE_UNKNOWN_ERROR, "系统出错，请稍后重试");
            return;
        }
    }



    public function batch_approve()
    {
        if ($this->withdraw_model->batch_approve_withdraw($this->get_admin_id(), trim($this->input->post('status', true)))){
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败(请稍后再试)");
    }

    public function import()
    {
        $this->load->model('withdraw_model');
        $this->load->model('refund_model');
        $this->load->library('excel', []);
        $this->load->library('PHPExcel/IOFactory');

        if (empty($_FILES)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请上传文件");
            return;
        }


        $data = array();

        try{
            $excel_sheet = $this->excel->import($_FILES);
        }catch (\Exception $e) {
            echo build_response_str(CODE_UNKNOWN_ERROR, $e->getMessage());
            return;
        }

        foreach($excel_sheet as $excel_data){
            $excel_data[7] = '';
            $excel_data[8] = '';
            $excel_data[9] = '';
            $excel_data[10] = '';
            $excel_data[11] = time();
            $refund = array_combine(Refund_model::$field1, $excel_data);
            $flag = $this->refund_model->check_excel_data($refund);
            if ($flag){
                continue;
            }

            array_push($data, $refund);
        }

        $this->db->trans_begin();

        $this->refund_model->batch_add($data);

        foreach ($data as $val) {
            $this->withdraw_model->update_withdraw_status($val);
        }

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败(请稍后再试)");
            return;
        }
        else
        {
            $this->db->trans_commit();
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }
    }

    public function return_ticket()
    {

        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $withdraw_id = trim($this->input->post('withdraw_id', true));

        $this->load->model('withdraw_model');
        $k = $this->withdraw_model->update_withdraw_statu($withdraw_id);

        if ($k == FALSE)
        {
            echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败(请稍后再试)");
        }
        else
        {
            echo build_response_str(CODE_SUCCESS, "操作成功");
        }
    }
}