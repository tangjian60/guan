<?php
class Refund_model extends CI_Model
{
    const TABLE_NAME = 'user_withdraw_refund';

    public static $field = array(
        'remit_time',
        'real_name',
        'bank_card_num',
        'bank_name',
        'amount',
        'service_fee',
        'category',
        'channel',
        'type',
        'refund_status',
        'memo',
        'ctime',
        );

    public static $field1 = array(
        'remit_time',
        'amount',
        'bank_card_num',
        'real_name',
        'bank_name',
        'category',
        'memo',
        'service_fee',
        'channel',
        'type',
        'refund_status',
        'ctime',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function batch_add($data)
    {
        if (empty($data)){
            return 0;
        }
        return $this->db->insert_batch(self::TABLE_NAME, $data);
    }

    public function check_excel_data($sheetData)
    {
        if (empty($sheetData)){
            return 0;
        }

        $this->db->where('remit_time', $sheetData['remit_time']);
        $this->db->where('real_name', $sheetData['real_name']);
        $this->db->where('bank_card_num', $sheetData['bank_card_num']);
        $this->db->where('amount', $sheetData['amount']);

        return $this->db->get(self::TABLE_NAME)->num_rows();
    }
}