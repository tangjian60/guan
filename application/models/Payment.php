<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-9-20
 * Time: 上午11:13
 */
class Payment extends Hilton_Model
{
    const DB_BILLS = 'hilton_bills';

    public function __construct()
    {
        parent::__construct();
    }

    private function balance($balance, $k, $amount)
    {
        foreach($amount as $key => $v){
            if ($key <= $v){
                \bcadd($balance, $v, 2);
            }
        }

        return $balance;
    }

    private function billData($balance, $bill_type, $amount, $note)
    {
        $bill_data = [
            'user_id' => SYSTEM_PAYMENT,
            'oper_id' => SYSTEM_USER_ID,
            'bill_type' => $bill_type,
            'amount' => $amount,
            'balance' => \bcadd($balance, $amount, 2),
            'balance_capital' => 0,
            'balance_commission' => 0,
            'memo' => $note
        ];

        return $bill_data;
    }

    public function transaction($bill_type, $amount, $note)
    {
        if (\bccomp($amount ,0, 2) <= 0){
            return Paycore::PAY_CODE_SUCCESS;
        }

        $this->db->select(['balance','id'])->where('id', SYSTEM_PAYMENT);
        $user = $this->db->get(self::DB_USER_MEMBER)->row();
        if (!$user->id){
            error_log("get user info failed , User id = " . SYSTEM_PAYMENT);
            return Paycore::PAY_CODE_BAD_USER_ID;
        }

        if (!$this->db->insert(self::DB_BILLS, $this->billData($user->balance, $bill_type,$amount, $note))) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return Paycore::PAY_CODE_FAILED;
        }

        $this->db->set('balance', \bcadd($user->balance, $amount, 2));
        $this->db->where('id', SYSTEM_PAYMENT);
        if (!$this->db->update(self::DB_USER_MEMBER)) {
            error_log("update user balance failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return Paycore::PAY_CODE_FAILED;
        }

        return Paycore::PAY_CODE_SUCCESS;
    }

}