<?php
class Shop_model extends CI_Model
{
    const TABLE_NAME = 'seller_bind_shops';

    public function __construct()
    {
        parent::__construct();
    }

    public function getShopInfoById($shop_id)
    {
        if (empty($shop_id) || !is_numeric($shop_id)) {
            return false;
        }

        return $this->db->where('id', $shop_id)->get(self::TABLE_NAME)->row();
    }
}