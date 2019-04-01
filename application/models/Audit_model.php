<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午10:01
 */
class Audit_model extends Hilton_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save($data)
    {
        $data['ctime'] = time();
        return $this->db->insert(\CONSTANT\Audit::TABLE_NAME, $data);
    }
}