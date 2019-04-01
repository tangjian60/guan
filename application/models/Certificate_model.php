<?php
class Certificate_model extends CI_Model
{
    const TABLE_NAME = 'user_certification';

    public function __construct()
    {
        parent::__construct();
    }

    public function getCertInfoById($cert_info_id)
    {
        if (empty($cert_info_id) || !is_numeric($cert_info_id)) {
            return false;
        }

        return $this->db->where('id', $cert_info_id)->get(self::TABLE_NAME)->row();
    }
}