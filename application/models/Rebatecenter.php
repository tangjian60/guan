<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rebatecenter extends Hilton_Model
{

    const DB_PROMOTE_RECORDS = 'promote_records';

    function __construct()
    {
        parent::__construct();
    }

    public function process_promote_money($task_obj, $buyer_promote_relation)
    {
        if (empty($task_obj) || $task_obj->commission_to_buyer <= 0) {
            return false;
        }

        // 获取买手的推广关系
//        $buyer_promote_relation = $this->get_promote_relations($task_obj->buyer_id);
//        if (empty($buyer_promote_relation)) {
//            return false;
//        }

        // 给买手推荐人的首单奖励
        if ($buyer_promote_relation->first_reward == STATUS_DISABLE && $this->set_first_reward_complete($buyer_promote_relation->owner_id, $buyer_promote_relation->promote_id)) {
            $promote_record = array();
            $promote_record['owner_id']         = $buyer_promote_relation->owner_id;
            $promote_record['promote_id']       = $buyer_promote_relation->promote_id;
            $promote_record['promote_type']     = PROMOTE_TYPE_REG;
            $promote_record['task_type']        = $task_obj->task_type;
            $promote_record['task_id']          = $task_obj->id;
            $promote_record['amount']           = PROMOTION_FIRST_REWARD;
            if ($this->add_promote_record($promote_record)) {
                $memo = '首单奖励';
                $this->paycore->promote_fee_payoff($buyer_promote_relation->owner_id, PROMOTION_FIRST_REWARD, $memo);
                // 推荐人数+1
                $this->setIncPromoteCnt($buyer_promote_relation->owner_id);
            }
        }

        // 买手推荐人的任务返利
        // 计算推广人数
        $promote_cnt = $this->get_user_promote_cnt($buyer_promote_relation->owner_id);
        //echo $promote_cnt;exit;
        // 计算阶梯推广奖励
        $buyer_amount_of_reward = $this->get_step_promote_reward($task_obj->commission_to_buyer, $promote_cnt);
        //print_r($buyer_amount_of_reward);exit;
        if ($buyer_amount_of_reward > 0) {
            $promote_record = array();
            $promote_record['owner_id'] = $buyer_promote_relation->owner_id;
            $promote_record['promote_id'] = $buyer_promote_relation->promote_id;
            $promote_record['promote_type'] = PROMOTE_TYPE_TASK;
            $promote_record['task_type'] = $task_obj->task_type;
            $promote_record['task_id'] = $task_obj->id;
            $promote_record['amount'] = $buyer_amount_of_reward;
            if ($this->add_promote_record($promote_record)) {
                $memo = '推广会员完成任务' . encode_id($task_obj->id) . '奖励';
                $this->paycore->promote_fee_payoff($buyer_promote_relation->owner_id, $buyer_amount_of_reward, $memo);
            }
        }

//        $buyer_amount_of_reward = round($task_obj->commission_to_buyer * PROMOTION_PROPORTION_OF_PROCEEDS, 2);
//        if ($buyer_amount_of_reward > 0) {
//            $promote_record = array();
//            $promote_record['owner_id']         = $buyer_promote_relation->owner_id;
//            $promote_record['promote_id']       = $buyer_promote_relation->promote_id;
//            $promote_record['promote_type']     = PROMOTE_TYPE_TASK;
//            $promote_record['task_type']        = $task_obj->task_type;
//            $promote_record['task_id']          = $task_obj->id;
//            $promote_record['amount']           = $buyer_amount_of_reward;
//            if ($this->add_promote_record($promote_record)) {
//                $memo = '推广会员完成任务' . encode_id($task_obj->id) . '奖励';
//                $this->paycore->promote_fee_payoff($buyer_promote_relation->owner_id, $buyer_amount_of_reward, $memo);
//            }
//        }
    }

    // 首单奖励清算
    public function process_first_reward($task_obj, $buyer_promote_relation)
    {
        if (empty($task_obj) || $task_obj->commission_to_buyer <= 0) {
            return false;
        }

        // 给买手推荐人的首单奖励
        if ($buyer_promote_relation->first_reward == STATUS_DISABLE && $this->set_first_reward_complete($buyer_promote_relation->owner_id, $buyer_promote_relation->promote_id)) {
            $promote_record = array();
            $promote_record['owner_id']         = $buyer_promote_relation->owner_id;
            $promote_record['promote_id']       = $buyer_promote_relation->promote_id;
            $promote_record['promote_type']     = PROMOTE_TYPE_REG;
            $promote_record['task_type']        = $task_obj->task_type;
            $promote_record['task_id']          = $task_obj->id;
            $promote_record['amount']           = PROMOTION_FIRST_REWARD;
            if ($this->add_promote_record($promote_record)) {
                $memo = '首单奖励';
                $this->paycore->promote_fee_payoff($buyer_promote_relation->owner_id, PROMOTION_FIRST_REWARD, $memo);
                // 推荐人数+1
                $this->setIncPromoteCnt($buyer_promote_relation->owner_id);
            }
        }
    }

    //TODO...
    public function tmp_op_first_reward($task_id, $owner_id, $promote_id)
    {
        $promote_record = array();
        $promote_record['owner_id']         = $owner_id;
        $promote_record['promote_id']       = $promote_id;
        $promote_record['promote_type']     = PROMOTE_TYPE_REG;
        $promote_record['task_type']        = TASK_TYPE_DF;
        $promote_record['task_id']          = $task_id;
        $promote_record['amount']           = PROMOTION_FIRST_REWARD;
        if ($this->add_promote_record($promote_record)) {
            $memo = '首单奖励';
            $this->paycore->promote_fee_payoff($owner_id, PROMOTION_FIRST_REWARD, $memo);
            // 推荐人数+1
            $this->setIncPromoteCnt($owner_id);
        }
    }


    // 商家充值-推荐人返现
    public function seller_top_up_bonus($top_up_obj)
    {
        if (empty($top_up_obj) || $top_up_obj->transfer_amount <= 0) {
            return false;
        }

        // 获取商家的推广关系
        $seller_promote_relation = $this->get_promote_relations($top_up_obj->seller_id);
        if (empty($seller_promote_relation)) {
            return false;
        }

        // 商家推荐人的充值返利
        $seller_amount_of_reward = round($top_up_obj->transfer_amount * PROMOTION_TOP_UP_BONUS, 2);
        if ($seller_amount_of_reward > 0) {
            $promote_record = array();
            $promote_record['owner_id']         = $seller_promote_relation->owner_id;
            $promote_record['promote_id']       = $seller_promote_relation->promote_id;
            $promote_record['promote_type']     = PROMOTE_TYPE_TOP_UP;
            $promote_record['task_type']        = NOT_AVAILABLE;
            $promote_record['task_id']          = NOT_AVAILABLE;
            $promote_record['amount']           = $seller_amount_of_reward;
            if ($this->add_promote_record($promote_record)) {
                $memo = '推广商家充值奖励';
                $this->paycore->promote_fee_payoff_seller($seller_promote_relation->owner_id, $seller_amount_of_reward, $memo);
            }
        }
    }

    public function get_step_promote_reward($amount, $promote_cnt)
    {
        return round($amount * $this->get_step_promote_rate($promote_cnt), 2);
    }

    public function get_step_promote_rate($promote_cnt)
    {
        if ($promote_cnt <= 80) {
            return 0.05;
        } elseif ($promote_cnt > 80 && $promote_cnt <= 280) {
            return 0.1;
        } elseif ($promote_cnt > 280 && $promote_cnt <= 580) {
            return 0.15;
        } elseif ($promote_cnt > 580) {
            return 0.2;
        } else {
            return 0.05;
        }
    }

    /**
     * 获取用户有效推荐人数
     * @param $member_id
     * @return int
     */
    public function get_user_promote_cnt($member_id)
    {
        $this->db->select('promote_cnt')->where('id', $member_id);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() > 0) {
            return $query->row()->promote_cnt;
        }
        return 0;
    }

    public function get_promote_relations($member_id)
    {
        $this->db->where('promote_id', $member_id);
        $this->db->where('status', STATUS_ENABLE);
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        return $this->db->get(self::DB_PROMOTE_RELATION)->row();
    }

    private function setIncPromoteCnt($member_id)
    {
        $this->db->where(array('id' => $member_id));
        $this->db->set('promote_cnt', 'promote_cnt + 1', FALSE);
        $this->db->update(self::DB_USER_MEMBER);
    }


    public function set_first_reward_complete($owner_id, $promote_id)
    {
        $this->db->set('first_reward', STATUS_ENABLE);
        $this->db->where('owner_id', $owner_id);
        $this->db->where('promote_id', $promote_id);
        return $this->db->update(self::DB_PROMOTE_RELATION);
    }

    private function add_promote_record($parameters)
    {
        if (invalid_parameter($parameters)) {
            return false;
        }

        $promote_data = array(
            'owner_id' => $parameters['owner_id'],
            'promote_id' => $parameters['promote_id'],
            'promote_type' => $parameters['promote_type'],
            'task_type' => $parameters['task_type'],
            'task_id' => $parameters['task_id'],
            'amount' => $parameters['amount']
        );

        return $this->db->insert(self::DB_PROMOTE_RECORDS, $promote_data);
    }
}