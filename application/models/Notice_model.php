<?php
use CONSTANT\Notice as Notice;
class Notice_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function top_notice($notice_id, $is_top)
    {
        if (empty($notice_id)){
            return 0;
        }

        if (!in_array($is_top, array(
            Notice::TOP_STATUS_ENUM_NO,
            Notice::TOP_STATUS_ENUM_OK))){
            return 0;
        }

        $this->db->where('id', $notice_id);
        return $this->db->update(Notice::TABLE_NAME, array('is_top' => $is_top));
    }

}