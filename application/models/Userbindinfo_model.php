<?php
class Userbindinfo_model extends CI_Model
{
    const TABLE_NAME = 'user_bind_info';

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserBindInfoById($bind_info_id)
    {
        if (empty($bind_info_id) || !is_numeric($bind_info_id)) {
            return false;
        }

        return $this->db->where('id', $bind_info_id)->get(self::TABLE_NAME)->row();
    }

}