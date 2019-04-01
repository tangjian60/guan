<?php
class Message_model extends CI_Model
{
    const TABLE_NAME = 'user_messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function add($memo, $oper_id, $member_id, $title)
    {
        $data = array(
            'member_id' => $member_id,
            'oper_id' => $oper_id,
            'read_status' => 0,
            'title' => $title,
            'content' => $memo,
        );

        return $this->db->insert(self::TABLE_NAME, $data);
    }

}