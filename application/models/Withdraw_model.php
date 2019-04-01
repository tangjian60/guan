<?php
class Withdraw_model extends CI_Model
{
    const TABLE_NAME = 'user_withdraw_record';
    const TB_USER_WITHDRAW_TIME = 'user_withdraw_time';

    public function __construct()
    {
        parent::__construct();
    }

    public function getWithdrawrecordInfoById($withdraw_id)
    {
        if (empty($withdraw_id) || !is_numeric($withdraw_id)) {
            return false;
        }

        return $this->db->where('id', $withdraw_id)->get(self::TABLE_NAME)->row();
    }

    public function updateWithRecordByExcel($data)
    {
        $this->db->where('real_name', $data['real_name']);
        $this->db->where('amount', $data['amount']);
        $this->db->where('bank_card_num', $data['bank_card_num']);
        $this->db->where('status', STATUS_CHECKING);

        return $this->db->update(self::TABLE_NAME, array('status' => STATUS_CANCELING));
    }

    public function update_withdraw_status($data)
    {
        if (empty($data)){
            return 0;
        }

        $this->db->where('real_name', $data['real_name']);
        $this->db->where('status', STATUS_PASSED);
        $this->db->where('amount', $data['amount']);
        $this->db->where('bank_card_num', $data['bank_card_num']);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        return $this->db->update(self::TABLE_NAME, array('status' => STATUS_CANCELING));
    }

    public function update_withdraw_statu($id)
    {
        if (empty($id)){
            return false;
        }

        // 记录退票时间  TODO... Ryan
        $this->db->where('id', $id);
        $this->db->where('status', STATUS_PASSED);
        if ($this->db->update(self::TABLE_NAME, array('status' => STATUS_CANCELING))) {
            $this->db->where('withdraw_id', $id);
            return $this->db->update(self::TB_USER_WITHDRAW_TIME, array('refund_time' => time()));
        }
        return false;
    }

    public function hasRecordStatusIsRemitIng()
    {
        $this->db->where('status', STATUS_REMITING);
        return $this->db->get(self::TABLE_NAME)->num_rows();
    }

    public function updateExcelDataStatus($parameters)
    {
        if (empty($parameters['start_time']) || empty($parameters['end_time'])){
            return 0;
         }

        $this->db->select('id');
        $this->db->where('status', STATUS_CHECKING);
        $this->db->where('create_time >=', $parameters['start_time']);
        $this->db->where('create_time <=', $parameters['end_time']);

        $list = $this->db->get(self::TABLE_NAME)->result();
        if ($list) {
            $ids = [];
            foreach ($list as $val) {
                $ids[] = $val->id;
            }

            $this->db->trans_begin();
            try {
                $this->db->where_in('id', $ids);
                $this->db->update(self::TABLE_NAME, array('status' => STATUS_REMITING));

                $this->db->where_in('withdraw_id', $ids);
                $this->db->update(self::TB_USER_WITHDRAW_TIME, array('accept_time' => time()));
                $this->db->trans_commit();
                return true;
            }
            catch (Exception $e) {
                $this->db->trans_rollback();
            }
        }
        return false;
    }

    public function doAcceptWithdraw($wid)
    {
        if (!$wid) return false;
        $this->db->where('withdraw_id', $wid);
        if ($this->db->update(self::TB_USER_WITHDRAW_TIME, array('accept_time' => time()))) {
            $this->db->where('id', $wid);
            $this->db->where('status', STATUS_CHECKING);
            return $this->db->update(self::TABLE_NAME, array('status' => STATUS_REMITING));
        }
    }


    function batch_approve_withdraw($oper_id, $status)
    {
        if (empty($oper_id)) {
            return false;
        }

        $this->db->select('id');
        $this->db->where('status', $status);
        $list = $this->db->get(self::TABLE_NAME)->result();
        if ($list) {
            $ids = [];
            foreach ($list as $val) {
                $ids[] = $val->id;
            }

            try {
                $this->db->where_in('id', $ids);
                $this->db->set('status', STATUS_PASSED);
                $this->db->set('oper_id', $oper_id);
                $this->db->update(self::TABLE_NAME);

                $this->db->where_in('withdraw_id', $ids);
                $this->db->update(self::TB_USER_WITHDRAW_TIME, array('transfer_time' => time()));
                $this->db->trans_commit();
                return true;
            }
            catch (Exception $e) {
                $this->db->trans_rollback();
            }
        }
        return false;
    }


}