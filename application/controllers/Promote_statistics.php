<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-10-16
 * Time: 上午9:59
 */
class Promote_statistics extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('Statistics');
    }

    // 推广统计
    public function index()
    {
        $i_page = $this->input->get('i_page', TRUE);
        $i_page = empty($i_page) ? 1 : $i_page;

        $member_id = $this->input->get('member_id', true);
        $checkDate = $this->input->get('checkDate', TRUE);
        $checkDate2 = $this->input->get('checkDate2', TRUE);

        $aDays = getDays($checkDate, $checkDate2);

        $this->Data['data'] = $this->Statistics->promoteList($member_id, $aDays);

        $this->Data['i_page'] = $i_page;
        $this->Data['member_id'] = $member_id;
        $this->Data['checkDate'] = $checkDate;
        $this->Data['checkDate2'] = $checkDate2;

        $this->Data['PageTitle'] = '推广统计';
        $this->Data['TargetPage'] = 'statistics/promote';
        $this->load->view('frame_main', $this->Data);
    }

    // 利润统计
    public function profit()
    {
        $checkDate = $this->input->get('checkDate', TRUE);
        $checkDate2 = $this->input->get('checkDate2', TRUE);

        $this->Data['data'] = [];
        if ($checkDate && $checkDate2) {
            $aDays = getDays($checkDate, $checkDate2);
            $this->Data['data'] = $this->Statistics->profitList($aDays);
        }

        $this->Data['checkDate'] = $checkDate;
        $this->Data['checkDate2'] = $checkDate2;

        $this->Data['PageTitle'] = '平台利润统计';
        $this->Data['TargetPage'] = 'statistics/profit';
        $this->load->view('frame_main', $this->Data);
    }

    // 运营统计
    public function operation()
    {
        $checkDate = $this->input->get('checkDate', TRUE);
        $checkDate2 = $this->input->get('checkDate2', TRUE);

        $this->Data['data'] = [];
        if ($checkDate && $checkDate2) {
            $aDays = getDays1($checkDate2, $checkDate);
            $this->Data['data'] = $this->Statistics->operationList($aDays);
        }

        $this->Data['checkDate'] = $checkDate;
        $this->Data['checkDate2'] = $checkDate2;

        $this->Data['PageTitle'] = '平台利润统计';
        $this->Data['TargetPage'] = 'statistics/operation';
        $this->load->view('frame_main', $this->Data);

    }

    //系统余额统计
    public function system()
    {
        $checkDate = $this->input->get('checkDate', TRUE);
        $checkDate2 = $this->input->get('checkDate2', TRUE);
        $now =  date('Y-m-d', time());
        $this->Data['data'] = [];

        if($checkDate == $now || $checkDate2 == $now){ //阻止用户手动查询当天的数据
            $this->Data['data'] = '';
        }else{
            if ($checkDate && $checkDate2) {
                $aDays = getDays1($checkDate2, $checkDate);
                $this->Data['data'] = $this->Statistics->systemList($aDays);
            }
        }

        $this->Data['checkDate'] = $checkDate;
        $this->Data['checkDate2'] = $checkDate2;

        $this->Data['PageTitle'] = '系统余额统计';
        $this->Data['TargetPage'] = 'statistics/system';
        $this->load->view('frame_main', $this->Data);

    }

    // 充值统计
    public function recharge()
    {
        $this->Data['data'] = [];
        $checkDate = $this->input->get('checkDate', TRUE);
        $checkDate2 = $this->input->get('checkDate2', TRUE);
        $today = $this->input->get('today', TRUE);
        $page = $this->input->get('i_page', TRUE) ;
        $page = !empty($page) ?: 1;

        if (1 == $today) {
            $this->Data['data'] = $this->Statistics->setRecharge([date('Y-m-d')], 100, false);
        } else {
            if ($checkDate && $checkDate2 && $checkDate2 >= $checkDate) {
                $this->Data['data'] = $this->Statistics->rechargeListDB([$checkDate, $checkDate2], $page, true);
            }
        }

        $this->Data['checkDate'] = $checkDate;
        $this->Data['checkDate2'] = $checkDate2;
        $this->Data['today'] = $today;

        $this->Data['PageTitle'] = '充值统计';
        $this->Data['TargetPage'] = 'statistics/recharge';
        $this->load->view('frame_main', $this->Data);
    }

    public function recharge_detail()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['date'] = $this->input->get('date', TRUE);
        $this->Data['type'] = $this->input->get('type', TRUE);
        $this->Data['today'] = $this->input->get('today', TRUE);

        if ($this->Data['today'] == 1) {
            $this->Data['data'] = $this->Statistics->getRealTimeRechargeDetails($this->Data['type'], 100);
        } else {
            $this->Data['data'] = $this->Statistics->rechargeDetailListDB([$this->Data['date']],$this->Data['type'], true, $this->Data['i_page']);
        }

        switch ($this->Data['type']) {
            case '1':
                $this->Data['title'] = '充值';
                break;
            case '2':
                $this->Data['title'] = '充值';
                break;
            case '3':
                $this->Data['title'] = '提现';
                break;
            default:
                $this->Data['title'] = '';
                break;
        }
        $this->Data['PageTitle'] = $this->Data['title'] . '明细';
        $this->Data['TargetPage'] = 'statistics/recharge_detail';
        $this->load->view('frame_main', $this->Data);
    }

    // 获取充值&提现记录明细
    public function ajaxGetRechargeDetail()
    {
        $post_data = $this->input->post();
        if (invalid_parameter($post_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        switch ($post_data['type']) {
            case '1':
                $key = 'chargeDetails';
                break;
            case '2':
                $key = 'chargeCorrectDetails';
                break;
            case '3':
                $key = 'withdrawDetails';
                break;
            default:
                echo build_response_str(CODE_DB_FAILED, '请求错误！');return;
                break;
        }

        if ($post_data['today'] == 1) {
            $list[0][$key] = $this->Statistics->getRealTimeRechargeDetails($post_data['type'], 100);
        } else {
            $list = $this->Statistics->rechargeList([$post_data['date']], true);
        }


        if (!empty($list[0][$key])) {
            echo build_response_str(CODE_SUCCESS, $post_data['type'], $list[0][$key]);
            return;
        }
        echo build_response_str(CODE_DB_FAILED, '没有找到相关记录！');
    }

    // 获取充值&提现记录明细
    public function ajaxGetRechargeDetailDB()
    {
        $post_data = $this->input->post();
        if (invalid_parameter($post_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if (!in_array($post_data['type'], ['1','2','3'])) {
            echo build_response_str(CODE_DB_FAILED, '请求错误！');return;
        }

        if ($post_data['today'] == 1) {
            $list = $this->Statistics->getRealTimeRechargeDetails($post_data['type'], 100);
        } else {
            $list = $this->Statistics->rechargeDetailListDB([$post_data['date']],$post_data['type'], true);
        }
        if (!empty($list)) {
            echo build_response_str(CODE_SUCCESS, $post_data['type'], $list);
            return;
        }
        echo build_response_str(CODE_DB_FAILED, '没有找到相关记录！');
    }





}