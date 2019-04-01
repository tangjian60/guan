<?php
class Reject extends CI_Model
{
    const DB_SELLER_REJECT_RECORDS = 'seller_reject_records';
    const DB_SELLER_REJECT_RECORDS_HP = 'seller_reject_records_hp';
    const DB_OPERATION_LOG = 'operation_log';

    public function __construct()
    {
        parent::__construct();
    }

    public function add_seller_reject_records($data)
    {
        $data['state'] = 1;
        $data['is_handle'] = 0;
        $data['gmt_create'] = date('Y-m-d H:i:s');
        return $this->db->insert(self::DB_SELLER_REJECT_RECORDS, $data);
    }

    public function add_seller_reject_records_hp($data)
    {
        $data['state'] = 1;
        $data['is_handle'] = 0;
        $data['gmt_create'] = date('Y-m-d H:i:s');
        return $this->db->insert(self::DB_SELLER_REJECT_RECORDS_HP, $data);
    }

    public function get_reject_list($parameters, $aFields = [])
    {
        if (!empty($aFields)) $this->db->select($aFields);

        if (!empty($parameters['seller_name'])) {
            $this->db->where('seller_mobile', $parameters['seller_name']);
        }

        if (!empty($parameters['buyer_name'])) {
            $this->db->where('buyer_mobile', $parameters['buyer_name']);
        }

        if (!empty($parameters['createDate'])) {
            $this->db->where('substr(gmt_create,1,10)', $parameters['createDate']);
        }

        if (isset($parameters['status']) && $parameters['status'] != '') {
            $this->db->where('state', $parameters['status']);
        }

        if (!empty($parameters['i_page']) && is_numeric($parameters['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($parameters['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get(self::DB_SELLER_REJECT_RECORDS)->result();
    }

    public function cancel_apply($id)
    {
        $this->db->where('id', intval($id));
        return $this->db->update(self::DB_SELLER_REJECT_RECORDS, ['state' => 4, 'is_handle' => 1]);
    }

    public function close_task($id)
    {
        $this->db->where('id', intval($id));
        return $this->db->update(self::DB_SELLER_REJECT_RECORDS, ['state' => 3, 'is_handle' => 1]);
    }

    public function goon_task($id)
    {
        $this->db->where('id', intval($id));
        return $this->db->update(self::DB_SELLER_REJECT_RECORDS, ['state' => 2, 'is_handle' => 1]);
    }

    public function add_oper_log($user_id, $oper_id, $oper_name, $oper_type, $oper_type_sub, $oper_content){
        $oper_data = [
            'user_id'       => $user_id,
            'oper_id'       => $oper_id,
            'oper_name'     => $oper_name,
            'oper_type'     => $oper_type,
            'oper_type_sub'     => $oper_type_sub,
            'oper_content'  => $oper_content,
            'ctime'         => time(),
        ];
        return $this->db->insert(self::DB_OPERATION_LOG, $oper_data);
    }



}