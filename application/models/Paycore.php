<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paycore extends Hilton_Model
{

    const DB_BILLS = 'hilton_bills';
    const DB_TASK_CANCELLED = 'hilton_task_cancelled';

    const PAY_CODE_SUCCESS = 0;
    const PAY_CODE_FAILED = 1;
    const PAY_CODE_BAD_AMOUNT = 2;
    const PAY_CODE_INJUSTICE_USER = 3;
    const PAY_CODE_INSUFFICIENT_BALANCE = 5;
    const PAY_CODE_BAD_USER_ID = 6;
    const PAY_CODE_BAD_AMOUNT_TYPE = 7;

    const PAY_TYPE_BJ = 1;
    const PAY_TYPE_YJ = 2;
    const PAY_TYPE_TG = 3;
    const PAY_TYPE_CZ = 4;
    const PAY_TYPE_TX = 5;
    const PAY_TYPE_EW = 6;
    const PAY_TYPE_FWF = 7;
    const PAY_TYPE_FO = 8;
    const PAY_TYPE_KD = 9;
    const PAY_TYPE_AG = 10;
    const PAY_TYPE_SXF = 11;
    const PAY_TYPE_AGB = 12;
    const PAY_TYPE_SRC = 13;

    private static $PAY_BILL_TYPE = array(
        self::PAY_TYPE_BJ => "本金",
        self::PAY_TYPE_YJ => "佣金",
        self::PAY_TYPE_TG => "推广",
        self::PAY_TYPE_CZ => "充值",
        self::PAY_TYPE_TX => "提现",
        self::PAY_TYPE_EW => "额外",
        self::PAY_TYPE_FWF => "服务费",
        self::PAY_TYPE_FO => "首单",
        self::PAY_TYPE_KD => "快递费",
        self::PAY_TYPE_AG => "商家代理任务奖励",
        self::PAY_TYPE_SXF => "提现手续费",
        self::PAY_TYPE_AGB => "买手代理任务奖励",
        self::PAY_TYPE_SRC => "商家充值校正",
    );

    function __construct()
    {
        parent::__construct();
    }

    public static function get_bill_type()
    {
        return self::$PAY_BILL_TYPE;
    }

    public static function get_bill_type_name($i)
    {
        return self::$PAY_BILL_TYPE[$i];
    }

    public static function get_bill_type_short_name($i)
    {
        return substr(self::$PAY_BILL_TYPE[$i], 0, 1);
    }

    public function get_balance($member)
    {
        $this->db->select('balance')->where('id', $member);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() > 0) {
            return $query->row()->balance;
        }

        error_log("get balance for bad user id , User id = " . $member);
        return null;
    }

    public function get_bills($parameters)
    {
        if (empty($parameters['member_id']) || !is_numeric($parameters['member_id'])) {
            return false;
        }

        $this->db->where('user_id', decode_id($parameters['member_id']));

        if (!empty($parameters['bill_type'])) {
            $this->db->where('bill_type', $parameters['bill_type']);
        }

        if (!empty($parameters['order_id'])) {
            $this->db->like('memo', $parameters['order_id']);
        }

        if (!empty($parameters['start_time'])) {
            $this->db->where('gmt_pay >=', $parameters['start_time']);
        }

        if (!empty($parameters['end_time'])) {
            $this->db->where('gmt_pay <=', $parameters['end_time']);
        }

        if (isset($parameters['start_amount']) && is_numeric($parameters['start_amount'])) {
            $this->db->where('amount >=', $parameters['start_amount']);
        }

        if (isset($parameters['end_amount']) && is_numeric($parameters['end_amount'])) {
            $this->db->where('amount <=', $parameters['end_amount']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_BILLS)->result();
    }

    public function top_up($member, $amount, $oper_id)
    {
        if (empty($member)) {
            error_log("Top up for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("Top up with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction_chongzhi($member, self::PAY_TYPE_CZ, $amount, '商家充值', $oper_id);
    }

    public function withdraw_failed_return($member, $amount, $oper_id, $tixian_type, $yongjin)
//                                  $$member, $amount, $oper_id,$tixian_type,$withdraw_record_info->amount
    {
        if (empty($member)) {
            error_log("Withdraw failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("Withdraw failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }
        if ($tixian_type == 1) {
            return $this->transaction_withdraw($member, self::PAY_TYPE_TX, $amount, '本金提现失败，退回余额', $oper_id, $tixian_type, $yongjin);
        } else if ($tixian_type == 2) {
            return $this->transaction_withdraw($member, self::PAY_TYPE_TX, $amount, '佣金提现失败，退回余额', $oper_id, $tixian_type, $yongjin);
        } else if ($tixian_type == 3) {
            return $this->transaction_withdraw($member, self::PAY_TYPE_TX, $amount, '商家提现失败，退回余额', $oper_id, $tixian_type, $yongjin);
        }
    }

    public function special_trans_method($member, $amount, $amount_type, $memo, $oper_id)
    {
        if (empty($member)) {
            error_log("Withdraw failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("Withdraw failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        if (!is_numeric($amount_type)) {
            error_log("Withdraw failed return bad amount type , User id = " . $member . " amount = " . $amount . " amount type = " . $amount_type);
            return self::PAY_CODE_BAD_AMOUNT_TYPE;
        }

        if (empty($memo)) {
            $memo = '转账';
        }
        if ($amount_type == 1 || $amount_type == 2) {        // 会员本金操作-会员佣金操作
            return $this->operation_transaction_buyer($member, self::PAY_TYPE_EW, $amount_type, $amount, $memo, $oper_id);
        }elseif ($amount_type == 3) {   // 商家金额操作
            return $this->transaction($member, self::PAY_TYPE_EW, $amount, $memo, $oper_id);
        }
    }

    // 商家充值校正
    public function recharge_correction_trans($member, $amount, $memo, $oper_id)
    {
        if (empty($member)) {
            error_log("Withdraw failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("Withdraw failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        if (empty($memo)) {
            $memo = '转账';
        }
        // 商家金额操作
        return $this->transaction($member, self::PAY_TYPE_SRC, $amount, $memo, $oper_id);
    }


    public function capital_refund($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("capital refund failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("capital refund failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }
    public function agent_promote_payoff_seller($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("agent return failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("agent return failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_AG, $amount, $memo, SYSTEM_USER_ID);
    }

    // 代理商买手推荐人 - 推广费用清算（提成）
    public function agent_promote_payoff_buyer($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("agent return failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("agent return failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction1($member, self::PAY_TYPE_AGB, $amount, $memo, SYSTEM_USER_ID);
    }

    // TODO... add by HKF.
    public function buyer_capital_diff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("agent return failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("agent return failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }

    public function express_refund($member, $amount, $memo){
        if (empty($member) || empty($memo)) {
            error_log("express refund failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("express refund failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_KD, $amount, $memo, SYSTEM_USER_ID);
    }


    public function commission_refund($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("commission refund failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("commission refund failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_YJ, $amount, $memo, SYSTEM_USER_ID);
    }

    public function capital_payoff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("capital payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("capital payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }

    // 买手本金支付
    public function capital_payoff_buyer($member, $taskType, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("capital payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("capital payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction_buyer($member, $taskType, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }

    // 买手佣金支付
    public function commission_payoff_buyer($member, $taskType, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("commission payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("commission payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction_buyer($member, $taskType, self::PAY_TYPE_YJ, $amount, $memo, SYSTEM_USER_ID);
    }

    public function commission_payoff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("commission payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("commission payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_YJ, $amount, $memo, SYSTEM_USER_ID);
    }

    // 提现手续费结算
    public function withdraw_service_fee_payoff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("withdraw service fee payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("withdraw service fee payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_SXF, $amount, $memo, SYSTEM_USER_ID);
    }

    public function agent_promote_payoff_system($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("agent payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("agent payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_AG, $amount, $memo, SYSTEM_USER_ID);
    }

    public function service_fee_payoff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("service fee payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("service fee payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_FWF, $amount, $memo, SYSTEM_USER_ID);
    }

    public function promote_fee_payoff($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("promote fee payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("promote fee payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction_buyer($member, '', self::PAY_TYPE_TG, $amount, $memo, SYSTEM_USER_ID);
    }

    public function promote_fee_payoff_seller($member, $amount, $memo)
    {
        if (empty($member) || empty($memo)) {
            error_log("promote fee payoff failed return for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("promote fee payoff failed return bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_TG, $amount, $memo, SYSTEM_USER_ID);
    }

    // 本金补扣差额（多退少补）
    public function pay_capital_diff($member, $amount, $taskId)
    {
        if (empty($member)) {
            error_log("pay capital for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("pay capital with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }
        $memo = $amount < 0 ? '支付任务单' . encode_id($taskId) . '本金差额' : '返还任务单' . encode_id($taskId) . '本金差额';
        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }

    public function buyer_pay_capital_diff_tmp($member, $amount, $taskId)
    {
        if (empty($member)) {
            error_log("buyer pay capital for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("buyer pay capital with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }
        $memo = $amount < 0 ? '任务' . encode_id($taskId) . '补扣买家本金差额' : '返还任务单' . encode_id($taskId) . '本金差额';
        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }



    //系统关闭  退还金额- 代理奖励
    private function transaction($member, $bill_type, $amount, $note, $oper_id)
    {

        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }

        $this->db->trans_start();
        $this->db->select('balance')->where('id', $member);

        $query = $this->db->get(self::DB_USER_MEMBER);

        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }

        $new_balance = $query->row()->balance + round($amount, 2);


        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->where('id', $member);
            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }
        $bill_data = [
            'user_id'       => $member,
            'oper_id'       => $oper_id,
            'bill_type'     => $bill_type,
            'amount'        => $amount,
            'balance'       => $new_balance,
            'memo'          => $note
        ];

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }

    //系统关闭  退还金额- 代理奖励（针对买手代理商奖励费用清算 zqh）
    private function transaction1($member, $bill_type, $amount, $note, $oper_id)
    {

        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }

        $this->db->trans_start();
        $this->db->select(['balance','balance_commission'])->where('id', $member);

        $query = $this->db->get(self::DB_USER_MEMBER);

        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }

        $new_balance = $query->row()->balance + round($amount, 2);
        $new_balance_commission = $query->row()->balance_commission + round($amount, 2);


        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->set('balance_commission', $new_balance_commission); // 买手代理商奖励应添加到佣金余额内
            $this->db->where('id', $member);
            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }
        $bill_data = [
            'user_id'       => $member,
            'oper_id'       => $oper_id,
            'bill_type'     => $bill_type,
            'amount'        => $amount,
            'balance'       => $new_balance,
            'balance_commission'   => $new_balance_commission,  //对应bills表内添加新的佣金信息
            'memo'          => $note
        ];

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }

    private function operation_transaction_buyer($member, $bill_type, $amount_type, $amount, $note, $oper_id){
        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }
        if (empty($amount_type)) {
            return self::PAY_CODE_BAD_AMOUNT_TYPE;
        }

        $this->db->trans_start();
        $this->db->select(['balance', 'balance_capital', 'balance_commission'])->where('id', $member);

        $query = $this->db->get(self::DB_USER_MEMBER);

        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }

        $new_balance                = $query->row()->balance + round($amount, 2);
        $new_balance_capital        = ($amount_type == 1) ? $query->row()->balance_capital      + round($amount, 2) : $query->row()->balance_capital;
        $new_balance_commission     = ($amount_type == 2) ? $query->row()->balance_commission   + round($amount, 2) : $query->row()->balance_commission;


        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->set('balance_capital', $new_balance_capital);
            $this->db->set('balance_commission', $new_balance_commission);
            $this->db->where('id', $member);
            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }
        $bill_data = [
            'user_id'               => $member,
            'oper_id'               => $oper_id,
            'bill_type'             => $bill_type,
            'amount'                => $amount,
            'balance'               => $new_balance,
            'balance_capital'       => $new_balance_capital,
            'balance_commission'    => $new_balance_commission,
            'memo'                  => $note,
        ];

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }

    //商家、买手提现失败退款

    /**
     * @name
     * @param $member 用户ID
     * @param $bill_type
     * @param $amount 提现+服务费
     * @param $note 备注
     * @param $oper_id 操作人ID
     * @param $tixian_type 提现类型:1-买手本金提现；2-买手佣金提现，3:商家提现
     * @param $yongjin 退款金额
     * @author chen.jian
     */
    private function transaction_withdraw($member, $bill_type, $amount, $note, $oper_id, $tixian_type, $yongjin){

        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }

        $this->db->trans_start();
        $this->db->select(['balance', 'balance_capital','balance_commission'])->where('id', $member);

        $query = $this->db->get(self::DB_USER_MEMBER);

        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }

        $new_balance = $query->row()->balance + round($amount, 2);
        $balance_capital = $query->row()->balance_capital;
        $balance_commission = $query->row()->balance_commission;
        $new_balance_capital = 0;
        $new_balance_commission = 0;
        switch ($tixian_type){
            case 1:
                $new_balance_capital = $balance_capital + round($amount, 2);
                $new_balance_commission = $balance_commission;
                break;
            case 2:
                $new_balance_capital = $balance_capital;
                $new_balance_commission = $balance_commission + round($amount, 2);
                break;
        }

        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->set('balance_capital', $new_balance_capital);
            $this->db->set('balance_commission', $new_balance_commission);
            $this->db->where('id', $member);
            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }
        $bill_data = [
            'user_id' => $member,
            'oper_id' => $oper_id,
            'bill_type' => $bill_type,
            'amount' => $amount,
            'balance' => $new_balance,
            'balance_capital' => $new_balance_capital,
            'balance_commission' => $new_balance_commission,
            'memo' => $note
        ];

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }

    private function transaction_buyer($member, $taskType, $bill_type, $amount, $note, $oper_id){
        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }

        $this->db->trans_start();
        $this->db->select(['balance', 'balance_capital', 'balance_commission', 'task_cnt'])->where('id', $member);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }
        if ($bill_type == self::PAY_TYPE_BJ) {
            $new_balance            = $query->row()->balance + round($amount, 2);
            $new_balance_capital    = $query->row()->balance_capital + round($amount, 2);
            $balance_commission     = $query->row()->balance_commission;
            $new_task_cnt           = $query->row()->task_cnt + 1;

            if ($amount < 0 && $new_balance < 0) {
                error_log("new balance is below 0, User id = " . $member);
                return self::PAY_CODE_INSUFFICIENT_BALANCE;
            }
            if ($amount != 0) {
                $this->db->set('balance', $new_balance);
                $this->db->set('balance_capital', $new_balance_capital);
                $this->db->set('task_cnt', $new_task_cnt);
                $this->db->where('id', $member);
                if (!$this->db->update(self::DB_USER_MEMBER)) {
                    error_log("update user balance and balance capital failed, last query : " . $this->db->last_query());
                    return self::PAY_CODE_FAILED;
                }
            }
            $bill_data[] = [
                'user_id'               => $member,
                'oper_id'               => $oper_id,
                'bill_type'             => $bill_type,
                'amount'                => $amount,
                'balance'               => $new_balance,
                'balance_capital'       => $new_balance_capital,
                'balance_commission'    => $balance_commission,
                'memo'                  => $note
            ];

            if ($new_task_cnt == 1) {
                    $this->db->set('balance', $new_balance + PROMOTION_FIRST_REWARD);
                    $this->db->set('balance_commission', $balance_commission + PROMOTION_FIRST_REWARD);
                    $this->db->where('id', $member);
                    if (!$this->db->update(self::DB_USER_MEMBER)) {
                        error_log("update user jiangli and balance failed, last query : " . $this->db->last_query());
                        return self::PAY_CODE_FAILED;
                    }
                    $bill_data[] = [
                        'user_id'               => $member,
                        'oper_id'               => $oper_id,
                        'bill_type'             => self::PAY_TYPE_FO,
                        'amount'                => PROMOTION_FIRST_REWARD,
                        'balance'               => $new_balance + PROMOTION_FIRST_REWARD,
                        'balance_capital'       => $new_balance_capital,
                        'balance_commission'    => $balance_commission + PROMOTION_FIRST_REWARD,
                        'memo' => '首单奖励'
                    ];
            }
        } elseif ($bill_type == self::PAY_TYPE_YJ) {
            $new_balance            = $query->row()->balance + round($amount, 2);
            $balance_capital        = $query->row()->balance_capital;
            $new_balance_commission = $query->row()->balance_commission + round($amount, 2);


            if ($amount < 0 && $new_balance < 0) {
                error_log("new balance is below 0, User id = " . $member);
                return self::PAY_CODE_INSUFFICIENT_BALANCE;
            }
            if ($amount != 0) {
                $this->db->set('balance', $new_balance);
                $this->db->set('balance_commission', $new_balance_commission);
                $this->db->where('id', $member);
                if (!$this->db->update(self::DB_USER_MEMBER)) {
                    error_log("update user balance and balance capital failed, last query : " . $this->db->last_query());
                    return self::PAY_CODE_FAILED;
                }
            }
            $bill_data[] = [
                'user_id'               => $member,
                'oper_id'               => $oper_id,
                'bill_type'             => $bill_type,
                'amount'                => $amount,
                'balance'               => $new_balance,
                'balance_capital'       => $balance_capital,
                'balance_commission'    => $new_balance_commission,
                'memo'                  => $note
            ];
        } elseif ($bill_type == self::PAY_TYPE_TG) {
            $new_balance            = $query->row()->balance + round($amount, 2);
            $balance_capital        = $query->row()->balance_capital;
            $new_balance_commission = $query->row()->balance_commission + round($amount, 2);

            if ($amount < 0 && $new_balance < 0) {
                error_log("new balance is below 0, User id = " . $member);
                return self::PAY_CODE_INSUFFICIENT_BALANCE;
            }
            if ($amount != 0) {
                $this->db->set('balance', $new_balance);
                $this->db->set('balance_commission', $new_balance_commission);
                $this->db->where('id', $member);
                if (!$this->db->update(self::DB_USER_MEMBER)) {
                    error_log("update user balance and balance capital failed, last query : " . $this->db->last_query());
                    return self::PAY_CODE_FAILED;
                }
            }
            $bill_data[] = [
                'user_id'               => $member,
                'oper_id'               => $oper_id,
                'bill_type'             => $bill_type,
                'amount'                => $amount,
                'balance'               => $new_balance,
                'balance_capital'       => $balance_capital,
                'balance_commission'    => $new_balance_commission,
                'memo'                  => $note
            ];
        }

        if (!$this->db->insert_batch(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }
        return self::PAY_CODE_SUCCESS;
    }

    // 商家充值-进账
    private function  transaction_chongzhi($member, $bill_type, $amount, $note, $oper_id){
        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }
        $this->db->trans_start();
        // 1. 商家充值进账
        $this->db->select('balance')->where('id', $member);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }
        $new_balance = $query->row()->balance + round($amount, 2);
        if ($new_balance < 0) {
            error_log("new balance is below 0, User id = " . $member);
            return self::PAY_CODE_INSUFFICIENT_BALANCE;
        }
        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->where('id', $member);

            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }
        // 2. 记录bill
        $bill_data = [
            'user_id'   => $member,
            'oper_id'   => $oper_id,
            'bill_type' => $bill_type,
            'amount'    => $amount,
            'balance'   => $new_balance,
            'memo'      => $note
        ];

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }


    public function get_task_cancelled($p)
    {
        $this->db->select(array('gmt_cancelled', 'task_id', 'item_id', 'item_title', 'buyer_id', 'cancel_reason'));
        if (!empty($p['gmt_cancelled'])) {
            $this->db->where('gmt_cancelled >=', $p['gmt_cancelled'].' 00:00:00');
            $this->db->where('gmt_cancelled <=', $p['gmt_cancelled'].' 23:59:59');
        }

        if (!empty($p['task_id'])) {
            $this->db->where('task_id', $p['task_id']);
        }

        if (!empty($p['buyer_id'])) {
            $this->db->where('buyer_id', $p['buyer_id']);
        }

        if (!empty($p['item_id'])) {
            $this->db->where('item_id', $p['item_id']);
        }

        if (!empty($p['i_page']) && is_numeric($p['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($p['i_page'] - 1));
        }

        $this->db->order_by('id', 'DESC');
        $task_list = $this->db->get(self::DB_TASK_CANCELLED)->result();
        /*  echo '<pre>';
          var_dump($task_list);die;*/
        /* foreach ($task_list as $v) {
             $this->db->where('parent_order_id', $v->id);
             $this->db->where_in('status', array(self::TASK_STATUS_DCZ, self::TASK_STATUS_MJSH, self::TASK_STATUS_MJSH_BTG, self::TASK_STATUS_DPJ, self::TASK_STATUS_HPSH, self::TASK_STATUS_HPSH_BTG, self::TASK_STATUS_YWC));
             if ($v->task_type == TASK_TYPE_DF) {
                 $v->task_yijie = $this->db->count_all_results(self::DB_TASK_DIANFU);
             } elseif ($v->task_type == TASK_TYPE_LL) {
                 $v->task_yijie = $this->db->count_all_results(self::DB_TASK_LIULIANG);
             } elseif ($v->task_type == TASK_TYPE_PDD) {
                 $v->task_yijie = $this->db->count_all_results(self::DB_TASK_PINDUODUO);
             }
         }*/
        return $task_list;
        /*  if(!empty($where)){
              $sql = "select gmt_cancelled,task_id,item_id,item_pic,buyer_id,cancel_reason from hilton_task_cancelled  where ".$where." limit 30";
          }else{
              $sql = "select gmt_cancelled,task_id,item_id,item_pic,buyer_id,cancel_reason from hilton_task_cancelled limit 30";
          }
          return $this->query_sql($sql);*/

    }


}