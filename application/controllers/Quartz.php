<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Quartz extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('taskcachemanager');
        $this->load->model('taskengine');
        $this->load->model('paycore');
        $this->load->model('rebatecenter');
        $this->load->library('YTOExpress');
    }

    public function index(){

        if (empty($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
            error_log('TRESPASS SYSTEM from ip : ' . $_SERVER['REMOTE_ADDR']);
            die(build_response_str(CODE_BAD_REQUEST, 'TRESPASS SYSTEM'));
        }
        error_log('ZCM Quartz Job Start at : ' . date("Y-m-d H:i:s.u"));
        
        // 撤销已经过了截止时间的订单
        $this->taskengine->cancel_timeout_DJD_tasks();

        // 超时接单任务处理
        $this->taskengine->cancel_timeout_DCZ_tasks();

        // 清空任务缓存队列
        $this->taskcachemanager->clean_task_pool();
        error_log('Timer clean task pool.');

        // 更新任务缓存队列
        $this->taskcachemanager->push_liuliang_tasks($this->taskengine->get_liuliang_DJD_tasks());
        $this->taskcachemanager->push_dianfu_tasks($this->taskengine->get_dianfu_DJD_tasks());
        $this->taskcachemanager->push_pinduoduo_tasks($this->taskengine->get_pinduoduo_DJD_tasks());
        error_log('Timer reload task pool.');

        // 自动审核提交超过24小时的单子-请求快递
        $this->taskengine->autoaudit_24timeout_tasks();
        
        // 自动审核提交超过48小时的好评单子-垫付单、拼多多
        $this->taskengine->autoaudit_haoping_48timeout_tasks();
        
        // 1.1 撤销流量任务的退款清算
        $t11 = $this->taskengine->get_unclearing_canceled_liuliang_tasks();//查询所有已撤销未清算的流量单
        if (!empty($t11)) {
            foreach ($t11 as $v) {
                if ($this->taskengine->liuliang_task_refund_clearing_complete($v->id)) { //将未清算状态改为已清算
                    $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                }
            }
        }
        // 1.2 流量任务佣金清算
        $t12 = $this->taskengine->get_unclearing_commission_liuliang_tasks();//查询所有已完成未清算的流量单
        if (!empty($t12)) {
            foreach ($t12 as $v) {
                if ($this->taskengine->liuliang_task_commission_clearing_complete($v->id)) { //将未清算状态改为已清算
                    $this->paycore->commission_payoff_buyer($v->buyer_id, TASK_TYPE_LL, $v->commission_to_buyer, '任务' . encode_id($v->id) . '佣金');//买手的佣金支付
                    // 1. 商家上级是否是代理商
                    $owner_id = $this->taskengine->get_promote_relation($v->seller_id);//获取该商家上级推荐人的id（商家代理）
                    if ($owner_id) {
                        $agent_data = $this->taskengine->get_seller_agent($owner_id);//获取该商家代理的信息（id,姓名,拼多多垫付单率,淘宝流量单率,淘宝垫付单率等）
                        if ($agent_data) {
                           $this->paycore->agent_promote_payoff_seller($agent_data->seller_id, $agent_data->tb_flow, '代理商流量任务' . encode_id($v->id) . '奖励');
                        }
                    }
                    $this->paycore->commission_payoff(SYSTEM_USER_ID, $v->commission_to_platform, '任务' . encode_id($v->id) . '佣金');//将每笔平台抽成金额加到平台余额账户内
                    $this->paycore->service_fee_payoff(SYSTEM_USER_ID, $v->service_to_platform, '任务' . encode_id($v->id) . '服务费');//将每笔平台平台服务费加到平台余额账户内
                    // 2. 推广费用清算
                    $this->_process_promote_money($v);
                }
            }
        }


        // 2.1 撤销垫付任务的退款清算
        $t21 = $this->taskengine->get_unclearing_canceled_dianfu_tasks();//查询所有已撤销未清算的垫付单
        if (!empty($t21)) {
            foreach ($t21 as $v) {
                if ($this->taskengine->dianfu_task_refund_clearing_complete($v->id)) {
                    // 退回垫付任务本金
                    $this->paycore->capital_refund($v->seller_id, $v->single_task_capital, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '本金');
                    // 退回垫付任务的佣金和服务费
                    $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                    // 退回垫付任务的快递费
                    $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                }
            }
        }
        // 2.2 垫付任务本金清算
        $t22 = $this->taskengine->get_unclearing_capital_dianfu_tasks();//获取所有状态为（待评价，好评审核，好评审核不通过，已完成）且任务本金清算状态为0（未清算）的单子
        if (!empty($t22)) {
            foreach ($t22 as $v) {
                if ($this->taskengine->dianfu_task_capital_clearing_complete($v->id)) { //将任务本金清算状态改为1 （已清算）
                    $this->paycore->capital_payoff_buyer(
                        $v->buyer_id, TASK_TYPE_DF,
                        $v->real_task_capital,
                        '任务' . encode_id($v->id) . '本金'
                    );  //将买手“实际本金付款金额”返回到买手账户的“本金余额”内
                }
            }
        }
        // 2.3 垫付任务佣金清算
        $t23 = $this->taskengine->get_unclearing_commission_dianfu_tasks();//获取所有状态为“已完成”且任务拥金清算状态为0（未清算）的单子
        if (!empty($t23)) {
            foreach ($t23 as $v) {
                if ($this->taskengine->dianfu_task_commission_clearing_complete($v->id)) { //将任务拥金清算状态改为1 （已清算）
                    $this->paycore->commission_payoff_buyer($v->buyer_id, TASK_TYPE_DF, $v->commission_to_buyer, '任务' . encode_id($v->id) . '佣金'); //将“支付给买手金额”加到买手账户的“佣金余额”内
                    $owner_id = $this->taskengine->get_promote_relation($v->seller_id); // 查询该订单的商家是否存在上级推荐人（商家代理商）
                    if ($owner_id) {
                        $agent_data = $this->taskengine->get_seller_agent($owner_id);//获取该商家代理的信息（id,姓名,拼多多垫付单率,淘宝流量单率,淘宝垫付单率等）
                        // 代理商
                        if ($agent_data) {
                           $this->paycore->agent_promote_payoff_seller($agent_data->seller_id, $agent_data->tb_prepaid, '代理商垫付任务' . encode_id($v->id) . '奖励');//根据商家代理的“淘宝垫付单率”将奖励金额加到代理商的“余额”内
                        }
                    }

                    $this->paycore->commission_payoff(SYSTEM_USER_ID, $v->commission_to_platform, '支付任务' . encode_id($v->id) . '佣金');//将每笔平台抽成金额加到平台余额账户内
                    $this->paycore->service_fee_payoff(SYSTEM_USER_ID, $v->service_to_platform, '任务' . encode_id($v->id) . '服务费');//将每笔平台服务费加到平台余额账户内
                    // 推广费用清算
                    $this->_process_promote_money($v);
                }
            }
        }
        // 2.4 垫付任务快递费清算
        $t24 = $this->taskengine->get_unclearing_express_dianfu_tasks();
        if (!empty($t24)) {
            foreach ($t24 as $v) {
                if ($this->taskengine->dianfu_task_express_clearing_complete($v->id)) {
                    $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                }
            }
        }

        // 3.1 撤销拼多多任务的退款清算
        $t31 = $this->taskengine->get_unclearing_canceled_pinduoduo_tasks();
        if (!empty($t31)) {
            foreach ($t31 as $v) {
                if ($this->taskengine->pinduoduo_task_refund_clearing_complete($v->id)) {
                    // 退回拼多多任务本金
                    $this->paycore->capital_refund($v->seller_id, $v->single_task_capital, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '本金');
                    // 退回拼多多任务的佣金和服务费
                    $this->paycore->commission_refund($v->seller_id, $v->single_task_commission_paid + $v->service_to_platform, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '佣金');
                    // 退回拼多多快递费
                    $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                }
            }
        }
        // 3.2 拼多多任务本金清算
        $t32 = $this->taskengine->get_unclearing_capital_pinduoduo_tasks();
        if (!empty($t32)) {
            foreach ($t32 as $v) {
                if ($this->taskengine->pinduoduo_task_capital_clearing_complete($v->id)) {
                    $this->paycore->capital_payoff_buyer(
                        $v->buyer_id, TASK_TYPE_PDD, 
                        $v->real_task_capital > $v->single_task_capital ? $v->real_task_capital : $v->single_task_capital,
                        '任务' . encode_id($v->id) . '本金');
                }
            }
        }
        // 3.3 拼多多任务佣金清算
        $t33 = $this->taskengine->get_unclearing_commission_pinduoduo_tasks();
        if (!empty($t33)) {
            foreach ($t33 as $v) {
                if ($this->taskengine->pinduoduo_task_commission_clearing_complete($v->id)) {
                    $this->paycore->commission_payoff_buyer($v->buyer_id, TASK_TYPE_PDD, $v->commission_to_buyer, '任务' . encode_id($v->id) . '佣金');
                    // 邀请人是否是代理商
                    $owner_id = $this->taskengine->get_promote_relation($v->seller_id);
                    if ($owner_id) {
                        $agent_data = $this->taskengine->get_seller_agent($owner_id);
                        // 代理商
                        if ($agent_data) {
                           $this->paycore->agent_promote_payoff_seller($agent_data->seller_id, $agent_data->pdd_prepaid, '代理商拼多多任务' . encode_id($v->id) . '奖励');
                        }
                    }

                    $this->paycore->commission_payoff(SYSTEM_USER_ID, $v->commission_to_platform, '任务' . encode_id($v->id) . '佣金');
                    $this->paycore->service_fee_payoff(SYSTEM_USER_ID, $v->service_to_platform, '任务' . encode_id($v->id) . '服务费');
                    // 推广费用清算
                    $this->_process_promote_money($v);
                }
            }
        }
        // 3.4 拼多多任务快递费清算
        $t34 = $this->taskengine->get_unclearing_express_pinduoduo_tasks();
        if (!empty($t34)) {
            foreach ($t34 as $v) {
                if ($this->taskengine->pinduoduo_task_express_clearing_complete($v->id)) {
                    $this->paycore->express_refund($v->seller_id, $v->single_task_express, '退回订单' . encode_id($v->parent_order_id) . '任务' . encode_id($v->id) . '快递费');
                }
            }
        }
        error_log('ZCM Quartz Job Finished at : ' . date("Y-m-d H:i:s.u"));
        echo build_response_str(CODE_SUCCESS, 'OK');
    }

    private function _process_promote_money($task_obj)
    {
        // 1. 判断买手上级是否代理商
        // 代理商买手  下线每单1.5元提成（通过管理端后台设置）
        // 1. 获取买手推荐人ID
        // 获取买手的推广关系
        $buyer_promote_relation = $this->rebatecenter->get_promote_relations($task_obj->buyer_id);//获取买手与上级推荐人（买手代理商）的关系信息
        if (empty($buyer_promote_relation)) {
            return false;
        }
        $agent_data = $this->taskengine->get_buyer_agent($buyer_promote_relation->owner_id);//获取该买手代理的信息（id,姓名,拼多多垫付单率,淘宝流量单率,淘宝垫付单率等）
        if ($agent_data) {
            // 首单奖励清算
            $this->rebatecenter->process_first_reward($task_obj, $buyer_promote_relation); //给买手代理“余额”和“佣金余额”加上首单奖励金额
            // 获取任务类型信息
            $reward_info = $this->_get_agent_reward_info($agent_data, $task_obj->task_type);
            // 代理商买手推荐人 - 推广费用清算（提成）
            $this->paycore->agent_promote_payoff_buyer($agent_data->buyer_id, $reward_info['amount'], $reward_info['memo'] . encode_id($task_obj->id) . '奖励');
        } else {
            // 普通买手推荐人 - 推广费用清算
            $this->rebatecenter->process_promote_money($task_obj, $buyer_promote_relation);
        }
    }

    private function _get_agent_reward_info($agent_data, $task_type)
    {
        $data = ['amount' => 0.00, 'memo'=>'未知任务'];
        switch ($task_type) {
            case TASK_TYPE_LL:
                $data['amount'] = $agent_data->tb_flow;
                $data['memo'] = '买手代理商流量任务';
                break;
            case TASK_TYPE_DF:
                $data['amount'] = $agent_data->tb_prepaid;
                $data['memo'] = '买手代理商垫付任务';
                break;
            case TASK_TYPE_PDD:
                $data['amount'] = $agent_data->pdd_prepaid;
                $data['memo'] = '拼多多买手代理商垫付任务';
                break;
        }
        return $data;
    }

}