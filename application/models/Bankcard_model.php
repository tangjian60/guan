<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午10:01
 */
use CONSTANT\Agent AS AGENT_CONSTANT;
class Bankcard_model extends Hilton_Model
{
    const TABLE_NAME = 'seller_bind_bankcards';
    const TABLE_NAME1 = 'user_certification';
    const TABLE_NAME2 = 'user_bind_info';
    const TABLE_NAME3 = 'promote_relation';
    const TABLE_NAME4 = 'user_members';

    public function __construct()
    {
        parent::__construct();
    }

    //获取商家银行卡信息
    public function getList($search)
    {
        if ($search['true_name']){
            $this->db->where('true_name', $search['true_name']);
        }

        if ($search['seller_id']){
            $this->db->where('seller_id', decode_id($search['seller_id']));
        }

        if ($search['bank_card_num']){
            $this->db->where('bank_card_num', $search['bank_card_num']);
        }

        if (!empty($search['i_page']) && is_numeric($search['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($search['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id desc');
        return $this->db->get(self::TABLE_NAME)->result();
    }

    //获取买手银行卡信息
    public function getlist_buyer($search)
    {
        if(empty($search['true_name']) && empty($search['user_id'])  && empty($search['bank_card_num'])){
            return false;
        }

        if ($search['true_name']){
            $this->db->where('true_name', $search['true_name']);
        }

        if ($search['user_id']){
            $this->db->where('user_id', decode_id($search['user_id']));
        }

        if ($search['bank_card_num']){
            $this->db->where('bank_card_num', $search['bank_card_num']);
        }

        if (!empty($search['i_page']) && is_numeric($search['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($search['i_page'] - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id desc');
        return $this->db->get(self::TABLE_NAME1)->result();
    }

    public function getInfo($id)
    {
       $this->db->where('id', $id);
       return $this->db->get(self::TABLE_NAME)->row();
    }

    public function getInfo_buyer($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(self::TABLE_NAME1)->row();
    }

    public function updateBankcard($bankcard, $id)
    {
        $this->db->where('id', $id);
        $this->db->set('bank_card_num', $bankcard);

        return $this->db->update(self::TABLE_NAME);
    }

    //更新买手银行卡信息
    public function updateBuyer($data)
    {
       if(empty($data)){
           return false;
       }

        $this->db->where('id', $data['id']);

        if(!empty($data['true_name'])){
            $this->db->set('true_name', $data['true_name']);
        }

        if(!empty($data['id_card_num'])){
            $this->db->set('id_card_num', $data['id_card_num']);
        }

        if(!empty($data['bank_card_num'])){
            $this->db->set('bank_card_num', $data['bank_card_num']);
        }

        if(!empty($data['bank_name'])){
            $this->db->set('bank_name', $data['bank_name']);
        }

        if(!empty($data['bank_province'])){
            $this->db->set('bank_province', $data['bank_province']);
        }

        if(!empty($data['bank_city'])){
            $this->db->set('bank_city', $data['bank_city']);
        }

        if(!empty($data['bank_county'])){
            $this->db->set('bank_county', $data['bank_county']);
        }

        if(!empty($data['bank_branch'])){
            $this->db->set('bank_branch', $data['bank_branch']);
        }

        return $this->db->update(self::TABLE_NAME1);

    }

    //修改买手帐号信息
    public function bind_info($search)
    {
        if(empty($search['user_id']) && empty($search['tb_nick'])){
            return false;
        }
        if ($search['user_id']){
            $this->db->where('user_id', decode_id($search['user_id']));
        }

        if ($search['tb_nick']){
            $this->db->where('tb_nick', $search['tb_nick']);
        }

        return $this->db->get(self::TABLE_NAME2)->result();

    }

    public function getInfo_bind($id)
    {
        $this->db->where('id',  $id);
        return $this->db->get(self::TABLE_NAME2)->row();
    }

    public function update_bind($data)
    {
        if(empty($data)){
            return false;
        }

        $this->db->where('id', $data['id']);

        if(!empty($data['tb_nick'])){
            $this->db->set('tb_nick', $data['tb_nick']);
        }

        if(!empty($data['tb_receiver_name'])){
            $this->db->set('tb_receiver_name', $data['tb_receiver_name']);
        }

        if(!empty($data['tb_receiver_tel'])){
            $this->db->set('tb_receiver_tel', $data['tb_receiver_tel']);
        }

        if(!empty($data['receiver_province'])){
            $this->db->set('receiver_province', $data['receiver_province']);
        }

        if(!empty($data['receiver_city'])){
            $this->db->set('receiver_city', $data['receiver_city']);
        }

        if(!empty($data['receiver_county'])){
            $this->db->set('receiver_county', $data['receiver_county']);
        }

        if(!empty($data['tb_receiver_addr'])){
            $this->db->set('tb_receiver_addr', $data['tb_receiver_addr']);
        }

        return $this->db->update(self::TABLE_NAME2);


    }

    public function is_exist($id)
    {
         if(empty($id)){
             return false;
         }

        $this->db->where('id', $id);

        return $this->db->get(self::TABLE_NAME4)->result();

    }

    //查询是否已经是上下级关系
    public function is_owner($owner_id)
    {
        $this->db->select('owner_id')->where('promote_id', $owner_id);
        
        return $this->db->get(self::DB_PROMOTE_RELATION)->row();
    }

    //查询该被推荐人是否已经被人推荐过
    public function is_promote($promote_id)
    {
        $this->db->where('promote_id', $promote_id);

        return $this->db->get(self::DB_PROMOTE_RELATION)->row();

    }

    //新增一条推广关系
    public function insert_relation($owner_id,$promote_id)
    {
         if(empty($owner_id) || empty($promote_id)){
             return false;
         }

        $insert_data = array(
            'owner_id' => $owner_id,
            'promote_id' => $promote_id,
            'first_reward' => '0',
            'first_reward_time' => null,
            'promote_time' => date("Y-m-d H:i:s"),
            'validity_time' => date("Y-m-d H:i:s", strtotime('+' . PROMOTION_VALIDITY_DAYS . ' days')),
            'status' => STATUS_ENABLE
        );

        return $this->db->insert(self::DB_PROMOTE_RELATION, $insert_data);


    }


}
