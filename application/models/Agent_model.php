<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午10:01
 */
use CONSTANT\Agent AS AGENT_CONSTANT;
class Agent_model extends Hilton_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList($search)
    {
        if ($search['seller_name']){
            $this->db->where('seller_name', $search['seller_name']);
        }

        if ($search['status']){
            $this->db->where('status', $search['status']);
        }

        if (!empty($search['i_page']) && is_numeric($search['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($search['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id desc');
        return $this->db->get(AGENT_CONSTANT::TABLE_NAME)->result();
    }

    public function getInfo($id)
    {
       $this->db->where('id', $id);
       return $this->db->get(AGENT_CONSTANT::TABLE_NAME)->row();
    }

    public function save($data)
    {
        unset($data['id']);
        $data['ctime'] = time();
        $data['mtime'] = time();
        $data['status'] = AGENT_CONSTANT::STATUS_NORMAL;
        return $this->db->insert(AGENT_CONSTANT::TABLE_NAME, $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);

        return $this->db->update(AGENT_CONSTANT::TABLE_NAME, $data);
    }

    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id);

        $data = array(
            'status' => $status,
            'mtime' => time()
        );

        return $this->db->update(AGENT_CONSTANT::TABLE_NAME, $data);
    }

    public function countByAgentName($seller_name)
    {
        $this->db->where('seller_name', $seller_name);
        return $this->db->get(AGENT_CONSTANT::TABLE_NAME)->num_rows();
    }


    public function getListBuyer($search)
    {
        if ($search['buyer_name']){
            $this->db->where('buyer_name', $search['buyer_name']);
        }

        if ($search['status']){
            $this->db->where('status', $search['status']);
        }

        if (!empty($search['i_page']) && is_numeric($search['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($search['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id desc');
        return $this->db->get(AGENT_CONSTANT::TB_BUYER)->result();
    }

    public function getInfoBuyer($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(AGENT_CONSTANT::TB_BUYER)->row();
    }

    public function saveBuyer($data)
    {
        unset($data['id']);
        $data['ctime'] = time();
        $data['mtime'] = time();
        $data['status'] = AGENT_CONSTANT::STATUS_NORMAL;
        return $this->db->insert(AGENT_CONSTANT::TB_BUYER, $data);
    }

    public function updateBuyer($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);

        return $this->db->update(AGENT_CONSTANT::TB_BUYER, $data);
    }



}