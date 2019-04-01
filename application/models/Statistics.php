<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends Hilton_Model
{
    const DB_USER_MEMBERS           = 'user_members';
    const DB_USER_BIND_INFO         = 'user_bind_info';
    const DB_USER_CERTIFICATION     = 'user_certification';
    const DB_TASK_DIANFU            = 'hilton_task_dianfu';
    const DB_TASK_LIULIANG            = 'hilton_task_liuliang';
    const DB_PROMOTE_RELATION          = 'promote_relation';
    const DB_HILTON_BILLS          = 'hilton_bills';
    const DB_STATISTICS_RECHARGE = 'statistics_recharge';
    const DB_STATISTICS_RECHARGE_DETAIL = 'statistics_recharge_detail';
    const DB_STATISTICS_RECHARGE_CORRECT_DETAIL = 'statistics_recharge_correct_detail';
    const DB_STATISTICS_WITHDRAW_DETAIL = 'statistics_withdraw_detail';

    private static $CACHE_PREFIX_TJ = NULL;
    private static $TJ_PROMOTE = 'PROMOTE';
    private static $TJ_PROFIT = 'PROFIT';
    private static $TJ_RECHARGE = 'RECHARGE';
    private static $TJ_OPERATION = 'OPERATION';
    private static $TJ_SYSTEM = 'SYSTEM';

    function __construct()
    {
        parent::__construct();
        $this->load->library('redismanager');
        self::$CACHE_PREFIX_TJ = CACHE_PREFIX_TJ_DIR . ':';
    }

    /**
     * 推广统计
     * @param int $memberId 上线会员ID
     * @param array $aDays 日期数组
     * @return array list
     */
    public function promoteList($memberId, $aDays)
    {
        $memberId = decode_id($memberId);
        $userIds = $this->_getMemberPromoteIds($memberId);
        if (empty($userIds)) {
            return [];
        }
        $memberInfo = $this->getMemberInfo($memberId);
        if (empty($memberInfo)) {
            return [];
        }

        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_PROMOTE);
        $today = date('Y-m-d');

        $list = [];
        foreach ($aDays as $k => $day) {

            if ($day > $today) continue;
            if ($today == $day) {
                $aStartEndTime = $this->_getStartEndTime($day);
                $list[$k]['date'] = $day;
                $list[$k]['userName'] = $memberInfo->user_name;
                $list[$k]['promoteCnt'] = $this->getMemberPromoteCnt($memberId, $aStartEndTime);
                //echo $this->db->last_query();exit;
                $list[$k]['certificationPassCnt'] = $this->getCertificationPassCnt($userIds, $aStartEndTime);
                $list[$k]['tbPassCnt'] = $this->getTBPassCnt($userIds, $aStartEndTime);
                $list[$k]['firstRewardCntTotal'] = $this->getFirstTasksDone($userIds, $aStartEndTime);
                $list[$k]['tasksDone'] = $this->getPromoteTasksDone($userIds, $aStartEndTime);
            } else {
                $sHashKey = $day . '_' . $memberId;
                // 从Redis缓存获取
                if (TRUE === $this->redismanager->hashExists($sHashKey)) {
                    $list[$k] = json_decode($this->redismanager->hashGet($sHashKey), true);
                } else {
                    $aStartEndTime = $this->_getStartEndTime($day);
                    $list[$k]['date'] = $day;
                    $list[$k]['userName'] = $memberInfo->user_name;
                    $list[$k]['promoteCnt'] = $this->getMemberPromoteCnt($memberId, $aStartEndTime);
                    $list[$k]['certificationPassCnt'] = $this->getCertificationPassCnt($userIds, $aStartEndTime);
                    $list[$k]['tbPassCnt'] = $this->getTBPassCnt($userIds, $aStartEndTime);
                    $list[$k]['firstRewardCntTotal'] = $this->getFirstTasksDone($userIds, $aStartEndTime);
                    $list[$k]['tasksDone'] = $this->getPromoteTasksDone($userIds, $aStartEndTime);
                    // 写入redis缓存
                    $this->redismanager->hashSet($sHashKey, $list[$k]);
                }
            }
        }
        return $list;
    }

    /**
     * 平台利润统计
     * @param array $aDays 日期数组
     * @return array list
     */
    public function profitList($aDays)
    {
        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_PROFIT);
        $today = date('Y-m-d');
        $list = [];
        foreach ($aDays as $k => $day) {
            if ($day > $today) continue;
            if ($today == $day) {
                $aStartEndTime = $this->_getStartEndTime($day);
                $list[$k]['date'] = $day;
                $list[$k]['sumCommission'] = $this->getSumForSystem(2, $aStartEndTime);// 佣金
                $list[$k]['sumServiceFee'] = $this->getSumForSystem(7, $aStartEndTime);// 服务费
                $list[$k]['sumFirstReward'] = $this->getSumForUser(8, $aStartEndTime);// 首单奖励
                $list[$k]['sumPromoteFee'] = $this->getSumForUser(3, $aStartEndTime);// 推广费用
                $list[$k]['sumAgentReward'] = $this->getSumForUser(10, $aStartEndTime); // 商家代理奖励
            } else {
                // 从Redis缓存获取
                if (TRUE === $this->redismanager->hashExists($day)) {
                    $list[$k] = json_decode($this->redismanager->hashGet($day), true);
                } else {
                    $aStartEndTime = $this->_getStartEndTime($day);
                    $list[$k]['date'] = $day;
                    $list[$k]['sumCommission'] = $this->getSumForSystem(2, $aStartEndTime);// 佣金
                    $list[$k]['sumServiceFee'] = $this->getSumForSystem(7, $aStartEndTime);// 服务费
                    $list[$k]['sumFirstReward'] = $this->getSumForUser(8, $aStartEndTime);// 首单奖励
                    $list[$k]['sumPromoteFee'] = $this->getSumForUser(3, $aStartEndTime);// 推广费用
                    $list[$k]['sumAgentReward'] = $this->getSumForUser(10, $aStartEndTime); // 商家代理奖励
                    // 写入redis缓存
                    $this->redismanager->hashSet($day, $list[$k]);
                }
            }
        }
        return $list;
    }

    /**
     * 运营统计
     * @param array $aDays 日期数组
     * @return array list
     */
    //TODO...
    public function operationList($aDays)
    {
        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_OPERATION);
        $today = date('Y-m-d');
        $list = [];
        foreach ($aDays as $k => $day) {
            if ($day > $today) continue;
            if ($today == $day) {
                $aStartEndTime = $this->_getStartEndTime($day);
                $list[$k]['date'] = $day;
                $list[$k]['sumSubtask'] = $this->getSumSubtask($aStartEndTime); //发布任务数
                $list[$k]['sumOrdertaking'] = $this->getSumOrdertaking($aStartEndTime);// 接单任务数
                $list[$k]['sumUnanswered'] = $list[$k]['sumSubtask'] - $list[$k]['sumOrdertaking'];//未被接单数
                $list[$k]['sumBuyer'] = $this->getSumBuyer($aStartEndTime);//接单买手数
                $list[$k]['sumFlapper'] = $this->getSumFlapper($aStartEndTime); //接单买号数
                $list[$k]['sumSeller'] = $this->getSumSeller($aStartEndTime); //发布商家数
                $list[$k]['sumShop'] = $this->getSumShop($aStartEndTime); //发布店铺数
                $list[$k]['sumNewbuyer'] = $this->getNewbuyer($aStartEndTime); //新注册买手数
                $list[$k]['sumNewbuyers'] = $this->getNewbuyers($aStartEndTime); //当天注册并接单的买手数
                $list[$k]['sumNewseller'] = $this->getNewseller($aStartEndTime); //新注册商家数
                $list[$k]['sumNewsellers'] = $this->getNewsellers($aStartEndTime); //当天注册并放单的商家数


            } else {
                // 从Redis缓存获取
                if (TRUE === $this->redismanager->hashExists($day)) {
                    $list[$k] = json_decode($this->redismanager->hashGet($day), true);
                } else {
                    $aStartEndTime = $this->_getStartEndTime($day);
                    $list[$k]['date'] = $day;
                    $list[$k]['sumSubtask'] = $this->getSumSubtask($aStartEndTime); //发布任务数
                    $list[$k]['sumOrdertaking'] = $this->getSumOrdertaking($aStartEndTime);// 接单任务数
                    $list[$k]['sumUnanswered'] = $list[$k]['sumSubtask'] - $list[$k]['sumOrdertaking'];//未被接单数
                    $list[$k]['sumBuyer'] = $this->getSumBuyer($aStartEndTime);//接单买手数
                    $list[$k]['sumFlapper'] = $this->getSumFlapper($aStartEndTime); //接单买号数
                    $list[$k]['sumSeller'] = $this->getSumSeller($aStartEndTime); //发布商家数
                    $list[$k]['sumShop'] = $this->getSumShop($aStartEndTime); //发布店铺数
                    $list[$k]['sumNewbuyer'] = $this->getNewbuyer($aStartEndTime); //新注册买手数
                    $list[$k]['sumNewbuyers'] = $this->getNewbuyers($aStartEndTime); //当天注册并接单的买手数
                    $list[$k]['sumNewseller'] = $this->getNewseller($aStartEndTime); //新注册商家数
                    $list[$k]['sumNewsellers'] = $this->getNewsellers($aStartEndTime); //当天注册并放单的商家数
                    // 写入redis缓存
                        $this->redismanager->hashSet($day, $list[$k]);
                }
            }
        }
        return $list;

    }

    /**
     * 系统余额统计
     * @param array $aDays 日期数组
     * @return array list
     */
    //TODO...
    public function systemList($aDays)
    {
        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_SYSTEM);
        $today = date('Y-m-d');
        $list = [];
        foreach ($aDays as $k => $day) {
            if ($day > $today) continue;
            if ($today == $day) {
                $list[$k]['date'] = $day; //日期
                $list[$k]['sumbalanceCommission'] = $this->getbalanceCommission(); //买手佣金总和
                $list[$k]['sumbalanceCapital'] = $this->getbalanceCapital();//买手本金总和
                $list[$k]['sumbalance'] = $this->getbalance();//商家余额总和
                $list[$k]['agent'] = 0;//代理账户资金总和
                $list[$k]['sumauditing'] = $this->getauditing();//补单余额（未审核垫付单本金总和）
                $list[$k]['sumfactCommission'] = $this->getfactCommission(); //佣金余额（未完成垫付单实付佣金总和）
                $list[$k]['sumCommission'] = $this->getCommission(); //流量余额（未完成流量单实付佣金总和）
                $list[$k]['sumwithdrawBalance'] = $this->getwithdrawBalance(); //提现余额
                $list[$k]['total'] = $list[$k]['sumbalanceCommission'] + $list[$k]['sumbalanceCapital'] + $list[$k]['sumbalance'] +
                    $list[$k]['agent'] + $list[$k]['sumauditing'] + $list[$k]['sumfactCommission'] + $list[$k]['sumCommission'] + $list[$k]['sumwithdrawBalance']; //总额
            } else {
                // 从Redis缓存获取
                if (TRUE === $this->redismanager->hashExists($day)) {
                    $list[$k] = json_decode($this->redismanager->hashGet($day), true);
                } else {
                    $list[$k]['date'] = $day; //日期
                    $list[$k]['sumbalanceCommission'] = $this->getbalanceCommission(); //买手佣金总和
                    $list[$k]['sumbalanceCapital'] = $this->getbalanceCapital();//买手本金总和
                    $list[$k]['sumbalance'] = $this->getbalance();//商家余额总和
                    $list[$k]['agent'] = 0;//代理账户资金总和
                    $list[$k]['sumauditing'] = $this->getauditing();//补单余额（未审核垫付单本金总和）
                    $list[$k]['sumfactCommission'] = $this->getfactCommission(); //佣金余额（未完成垫付单实付佣金总和）
                    $list[$k]['sumCommission'] = $this->getCommission(); //流量余额（未完成流量单实付佣金总和）
                    $list[$k]['sumwithdrawBalance'] = $this->getwithdrawBalance(); //提现余额
                    $list[$k]['total'] = $list[$k]['sumbalanceCommission'] + $list[$k]['sumbalanceCapital'] + $list[$k]['sumbalance'] +
                        $list[$k]['agent'] + $list[$k]['sumauditing'] + $list[$k]['sumfactCommission'] + $list[$k]['sumCommission'] + $list[$k]['sumwithdrawBalance']; //总额
                    // 写入redis缓存
                    $this->redismanager->hashSet($day, $list[$k]);
                }
            }
        }
        return $list;
    }

    //获取提现余额
    public function getwithdrawBalance()
    {
        $this->db->select('sum(amount) AS amount');
        $this->db->where_in('status',[2,21]);//  提现处理中/打款处理中
        $query = $this->db->get(self::DB_WITHDRAW_RECORD);
        $result = $query->result();
        $amount = $result[0]->amount;
        return $amount > 0 ? $amount : 0;
    }

    //获取流量余额
    public function getCommission()
    {
        $this->db->select('sum(single_task_commission_paid) AS single_task_commission_paid');
        $this->db->where_in('status',[2,3,4,6,8,9,20]);//待接单-待操作-卖家审核-平台审核-待评价-好评审核-申诉中
        $query = $this->db->get(self::DB_TASK_LIULIANG);
        $result = $query->result();
        $single_task_commission_paid = $result[0]->single_task_commission_paid;
        return $single_task_commission_paid > 0 ? $single_task_commission_paid : 0;
    }

    //获取佣金余额
    public function getfactCommission()
    {
        $this->db->select('sum(single_task_commission_paid) AS single_task_commission_paid');
        $this->db->where_in('status',[2,3,4,6,8,9,20]);//待接单-待操作-卖家审核-平台审核-待评价-好评审核-申诉中
        $query = $this->db->get(self::DB_TASK_DIANFU);
        $result = $query->result();
        $single_task_commission_paid = $result[0]->single_task_commission_paid;
        return $single_task_commission_paid > 0 ? $single_task_commission_paid : 0;
    }

    //获取补单余额
    public function getauditing()
    {
        $this->db->select('sum(task_capital) AS task_capital');
        $this->db->where_in('status',[2,3,4]);//待接单-待操作-卖家审核
        $query = $this->db->get(self::DB_TASK_DIANFU);
        $result = $query->result();
        $task_capital = $result[0]->task_capital;
        return $task_capital > 0 ? $task_capital : 0;
    }

    //获取买手佣金总和
    public function getbalanceCommission()
    {
        $this->db->select('sum(balance_commission) AS balance_commission');
        $this->db->where('user_type', USER_TYPE_BUYER);// 用户类型：1
        $query = $this->db->get(self::DB_USER_MEMBERS);
        $result = $query->result();
        $balance_commission = $result[0]->balance_commission;
        return $balance_commission > 0 ? $balance_commission : 0;
    }

    //获取买手本金总和
    public function getbalanceCapital()
    {
        $this->db->select('sum(balance_capital) AS balance_capital');
        $this->db->where('user_type', USER_TYPE_BUYER);// 用户类型：1
        $query = $this->db->get(self::DB_USER_MEMBERS);
        $result = $query->result();
        $balance_capital = $result[0]->balance_capital;
        return $balance_capital > 0 ? $balance_capital : 0;
    }

    //获取商家余额总和
    public function getbalance()
    {
        $this->db->select('sum(balance) AS balance');
        $this->db->where('user_type', USER_TYPE_SELLER);// 用户类型：2
        $query = $this->db->get(self::DB_USER_MEMBERS);
        $result = $query->result();
        $balance = $result[0]->balance;
        return $balance > 0 ? $balance : 0;
    }



    //获取发布任务数
    public function getSumSubtask($aStartEndTime)
    {
        $this->db->select('count(id) AS subtask');
        $this->db->where('(gmt_create BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return !empty($query->row()->subtask) ? $query->row()->subtask : 0;
    }
    //获取接单任务数
    public function getSumOrdertaking($aStartEndTime)
    {
        $this->db->select('count(buyer_id) AS Ordertaking');
        $this->db->where('buyer_id !=',null);
        $this->db->where('(gmt_taking_task BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return !empty($query->row()->Ordertaking) ? $query->row()->Ordertaking : 0;
    }
    //获取接单买手数
    public function getSumBuyer($aStartEndTime)
    {
        $this->db->select('count(id) AS SumBuyer');
        $this->db->where('buyer_id !=','');
        $this->db->where('(gmt_taking_task BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('buyer_id');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }
    //接单买号数
    public function getSumFlapper($aStartEndTime)
    {
        $this->db->select('count(id) AS SumFlapper');
        $this->db->where('buyer_tb_nick !=','');
        $this->db->where('(gmt_taking_task BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('buyer_tb_nick');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }
    //发布商家数
    public function getSumSeller($aStartEndTime)
    {
        $this->db->select('count(id) AS SumSeller');
        $this->db->where('seller_id !=','');
        $this->db->where('(gmt_create BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('seller_id');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }
    //发布店铺数
    public function getSumShop($aStartEndTime)
    {
        $this->db->select('count(id) AS SumSeller');
        $this->db->where('shop_id !=','');
        $this->db->where('(gmt_create BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('shop_id');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }

    //新注册买手数
    public function getNewbuyer($aStartEndTime)
    {
        $this->db->select('id AS SumBuyer');
        $this->db->where('user_type', USER_TYPE_BUYER);// 用户类型：1
        $this->db->where('(reg_time BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_USER_MEMBERS);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }

    //当天注册并接单的买手数
    public function getNewbuyers($aStartEndTime)
    {
        $this->db->select('count(*) AS SumBuyer');
        $this->db->join(self::DB_TASK_DIANFU . ' AS b', 'a.id = b.buyer_id', 'left');
        $this->db->where('a.user_type', USER_TYPE_BUYER);// 用户类型：1
        $this->db->where('(a.reg_time BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->where('(b.gmt_taking_task BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('b.buyer_id');
        $query = $this->db->get(self::DB_USER_MEMBERS . ' AS a');
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }

    //新注册商家数
    public function getNewseller($aStartEndTime)
    {
        $this->db->select('id AS SumBuyer');
        $this->db->where('user_type', USER_TYPE_SELLER);// 用户类型：2
        $this->db->where('(reg_time BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_USER_MEMBERS);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }

    //当天注册并放单的商家数
    public function getNewsellers($aStartEndTime)
    {
        $this->db->select('count(*) AS SumBuyer');
        $this->db->join(self::DB_TASK_DIANFU . ' AS b', 'a.id = b.seller_id', 'left');
        $this->db->where('a.user_type', USER_TYPE_SELLER);// 用户类型：2
        $this->db->where('(a.reg_time BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->where('(b.gmt_create BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('b.seller_id');
        $query = $this->db->get(self::DB_USER_MEMBERS . ' AS a');
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }











    /**
     * 充值统计
     * @param array $aDays 日期数组
     * @param boolean $bSensitive 脱敏处理
     * @return array list
     */
    public function rechargeList($aDays, $bSensitive = false)
    {
        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_RECHARGE);
        $list = [];
        foreach ($aDays as $k => $day) {
            if ($day >= date('Y-m-d')) continue;
            if (TRUE === $this->redismanager->hashExists($day)) {

                $aData = json_decode($this->redismanager->hashGet($day), true);
                if (true === $bSensitive) {
                    // 脱敏倍数
                    $iSensitiveTimes = 100;
                    // 金额脱敏处理
                    $aData['sumRecharge'] = $aData['sumRecharge'] / $iSensitiveTimes;
                    $aData['sumRechargeCorrect'] = $aData['sumRechargeCorrect'] / $iSensitiveTimes;
                    $aData['sumRechargeTotal'] = $aData['sumRecharge'] + $aData['sumRechargeCorrect'];
                    $aData['sumWithdrawTotal'] = $aData['sumWithdrawTotal'] / $iSensitiveTimes;

                    if (!empty($aData['chargeDetails'])) {
                        foreach ($aData['chargeDetails'] as & $val) {
                            $val['amount'] = $val['amount'] / $iSensitiveTimes;
                        }
                        unset($val);
                    }
                    if (!empty($aData['chargeCorrectDetails'])) {
                        foreach ($aData['chargeCorrectDetails'] as & $val) {
                            $val['amount'] = $val['amount'] / $iSensitiveTimes;
                        }
                        unset($val);
                    }
                    if (!empty($aData['withdrawDetails'])) {
                        foreach ($aData['withdrawDetails'] as & $val) {
                            $val['amount'] = $val['amount'] / $iSensitiveTimes;
                        }
                        unset($val);
                    }
                }
                $list[$k] = $aData;
                unset($aData);
            }
        }
        return $list;
    }

    /**
     * 充值统计
     * @param string $checkDate 日期
     * @param string $checkDate2 日期
     * @param boolean $bSensitive 脱敏处理
     * @return array list
     */
    public function rechargeListDB($aDays, $page, $bSensitive = false)
    { // TODO...
        if (count($aDays) == 1) {
            $this->db->where('day', $aDays[0]);
        } else {
            $this->db->where('day BETWEEN "' . $aDays[0] . '" AND "' . $aDays[1] . '"');
        }
        $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($page - 1));
        $this->db->order_by('id', 'DESC');
        $list = $this->db->select([
                'day AS date','sum_recharge AS sumRecharge','sum_recharge_correct AS sumRechargeCorrect',
                'sum_recharge_total AS sumRechargeTotal','sum_withdraw_total AS sumWithdrawTotal'
            ])->get(self::DB_STATISTICS_RECHARGE)->result_array();
        if ($list) {
            if (true === $bSensitive) {
                // 脱敏倍数
                $iSensitiveTimes = 100;
                // 金额脱敏处理
                foreach ($list as $k => $val) {
                    $list[$k]['sumRecharge'] = $val['sumRecharge'] / $iSensitiveTimes;
                    $list[$k]['sumRechargeCorrect'] = $val['sumRechargeCorrect'] / $iSensitiveTimes;
                    $list[$k]['sumRechargeTotal'] = $val['sumRechargeTotal']  / $iSensitiveTimes;
                    $list[$k]['sumWithdrawTotal'] = $val['sumWithdrawTotal'] / $iSensitiveTimes;
                }
            }
        }
        return $list;
    }

    public function rechargeDetailListDB($aDays, $type, $bSensitive = false, $page = 0)
    { // TODO...
        switch ($type) {
            case '1':
                $db = self::DB_STATISTICS_RECHARGE_DETAIL;
                break;
            case '2':
                $db = self::DB_STATISTICS_RECHARGE_CORRECT_DETAIL;
                break;
            case '3':
                $db = self::DB_STATISTICS_WITHDRAW_DETAIL;
                break;
            default:
                return [];
                break;
        }

        if (count($aDays) == 1) {
            $this->db->where('day', $aDays[0]);
        } else {
            $this->db->where('day BETWEEN "' . $aDays[0] . '" AND "' . $aDays[1] . '"');
        }
        $page > 0 && $this->db->limit(100, 100 * ($page - 1));

        $this->db->order_by('id', 'DESC');
        $list = $this->db->get($db)->result_array();
        if ($list) {
            if (true === $bSensitive) {
                // 脱敏倍数
                $iSensitiveTimes = 100;
                // 金额脱敏处理
                foreach ($list as $k => $val) {
                    $list[$k]['date']  = $val['day'];
                    $list[$k]['amount'] = $val['amount'] / $iSensitiveTimes;
                }
            }
        }
        return $list;
    }

    /**
     * 获取充值&提现相关明细（当天实时数据）
     * @param int $type 明细类型 1充值明细 2充值校正明细 3提现明细
     * @param int $iSensitiveTimes 脱敏倍数
     * @return array
     */
    public function getRealTimeRechargeDetails($type, $iSensitiveTimes = 1)
    {
        $aStartEndTime = $this->_getStartEndTime(date('Y-m-d'));
        $managersMap = $this->getManagersIdsMap();
        switch ($type) {
            case 1:
                $list = $this->getBillList($aStartEndTime, 4);// 充值
                if ($list) {
                    foreach ($list as & $val) {
                        $val->amount = $val->amount / $iSensitiveTimes;
                        $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    }
                    unset($val);
                }
                break;
            case 2:
                $list = $this->getBillList($aStartEndTime, 13);// 充值校正
                foreach ($list as & $val) {
                    $val->amount = $val->amount / $iSensitiveTimes;
                    $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                }
                unset($val);
                break;
            case 3:
                $list = $this->getBillList($aStartEndTime,5);
                if ($list) {
                    foreach ($list as & $val) {
                        $val->amount = $val->amount / $iSensitiveTimes;
                        $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    }
                    unset($val);
                }
                break;
            default:
                return [];
                break;
        }
        return object_to_array($list);
    }

    /**
     * 获取充值统计数据, 并设置缓存
     * @param array $aDays 日期数组 <['2018-10-25','2018-10-26',...]>
     * @param int $iSensitiveTimes 脱敏倍数
     * @param bool $cache 是否对查询结果设置缓存
     * @return array|bool
     */
    public function setRecharge($aDays, $iSensitiveTimes = 1, $cache = true)
    {
        $this->redismanager->assignCacheKey(self::$CACHE_PREFIX_TJ . self::$TJ_RECHARGE);

        $list = [];
        $managersMap = $this->getManagersIdsMap();
        foreach ($aDays as $k => $day) {
            // 从Redis缓存获取
            if (TRUE === $this->redismanager->hashExists($day))
            {
                continue;
            }
            else
            {
                $aStartEndTime = $this->_getStartEndTime($day);
                $list[$k]['date'] = $day;
                $list[$k]['sumRecharge'] = 0;// 充值
                $list[$k]['sumRechargeCorrect'] = 0;// 充值校正
                $list[$k]['sumRechargeTotal'] = 0;
                $list[$k]['sumWithdrawTotal'] = 0;

                $list[$k]['chargeDetails'] = [];
                $list[$k]['chargeCorrectDetails'] = [];
                $list[$k]['withdrawDetails'] = [];

                // 1. 查询一段时间内所有充值明细
                $list2 = $this->getBillList($aStartEndTime, 4);//
                if ($list2) {
                    foreach ($list2 as & $val) {
                        $val->amount = $val->amount / $iSensitiveTimes;
                        $list[$k]['sumRecharge'] += $val->amount;// 充值
                        $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    }
                    unset($val);
                    $list[$k]['chargeDetails'] = $list2;
                }
                unset($list2);

                // 2. 查询一段时间内所有充值校正明细
                $list2 = $this->getBillList($aStartEndTime, 13);
                if ($list2) {
                    foreach ($list2 as & $val) {
                        $val->amount = $val->amount / $iSensitiveTimes;
                        $list[$k]['sumRechargeCorrect'] += $val->amount;// 充值
                        $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    }
                    unset($val);
                    $list[$k]['chargeCorrectDetails'] = $list2;
                }
                unset($list2);

                // 3. 充值合计
                $list[$k]['sumRechargeTotal'] = $list[$k]['sumRecharge'] + $list[$k]['sumRechargeCorrect'];

                // 4. 查询一段时间内所有提现明细
                $list2 = $this->getBillList($aStartEndTime,5); //提现
                if ($list2) {
                    foreach ($list2 as & $val) {
                        $val->amount = $val->amount / $iSensitiveTimes;
                        $list[$k]['sumWithdrawTotal'] += $val->amount;// 提现
                        $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    }
                    unset($val);
                    $list[$k]['withdrawDetails'] = $list2;
                }
                unset($list2);

                // 写入redis缓存
                (true === $cache) && $this->redismanager->hashSet($day, $list[$k]);
            }
        }
        //print_r($list);exit;
        if (false === $cache) return $list;
        return true;
    }


    /**
     * 获取充值统计数据, 并设置缓存
     * @param array $aDays 日期数组 <['2018-10-25','2018-10-26',...]>
     * @param int $iSensitiveTimes 脱敏倍数
     * @param bool $cache 是否对查询结果设置缓存
     * @return array|bool
     */
    public function setRechargeDB($aDays, $iSensitiveTimes = 1)
    { // TODO...

        $list = [];
        $managersMap = $this->getManagersIdsMap();
        foreach ($aDays as $k => $day) {

            $aStartEndTime = $this->_getStartEndTime($day);
            $list[$k]['date'] = $day;
            $list[$k]['sumRecharge'] = 0;// 充值
            $list[$k]['sumRechargeCorrect'] = 0;// 充值校正
            $list[$k]['sumRechargeTotal'] = 0;
            $list[$k]['sumWithdrawTotal'] = 0;

            $list[$k]['chargeDetails'] = [];
            $list[$k]['chargeCorrectDetails'] = [];
            $list[$k]['withdrawDetails'] = [];

            // 1. 查询一段时间内所有充值明细
            $list2 = $this->getBillList($aStartEndTime, 4);//
            //print_r($this->db->last_query());exit;
            if ($list2) {
                foreach ($list2 as & $val) {
                    $val->amount = $val->amount / $iSensitiveTimes;
                    $list[$k]['sumRecharge'] += $val->amount;// 充值
                    $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    $this->_insertRechargeDetail($val, $day);
                }
                unset($val);
                //$list[$k]['chargeDetails'] = $list2;
            }
            unset($list2);

            // 2. 查询一段时间内所有充值校正明细
            $list2 = $this->getBillList($aStartEndTime, 13);
            if ($list2) {
                foreach ($list2 as & $val) {
                    $val->amount = $val->amount / $iSensitiveTimes;
                    $list[$k]['sumRechargeCorrect'] += $val->amount;// 充值
                    $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    $this->_insertRechargeCorrectDetail($val, $day);
                }
                unset($val);
                //$list[$k]['chargeCorrectDetails'] = $list2;
            }
            unset($list2);

            // 3. 充值合计
            $list[$k]['sumRechargeTotal'] = $list[$k]['sumRecharge'] + $list[$k]['sumRechargeCorrect'];

            // 4. 查询一段时间内所有提现明细
            $list2 = $this->getBillList($aStartEndTime, 5); //提现
            if ($list2) {
                foreach ($list2 as & $val) {
                    $val->amount = $val->amount / $iSensitiveTimes;
                    $list[$k]['sumWithdrawTotal'] += $val->amount;// 提现
                    $val->op_man = isset($managersMap[$val->oper_id]) ? $managersMap[$val->oper_id] : '--';
                    $this->_insertWithdrawDetail($val, $day);
                }
                unset($val);
                //$list[$k]['withdrawDetails'] = $list2;
            }
            unset($list2);

            $this->_insertRecharge($list[$k]);

            // 写入redis缓存
            //(true === $cache) && $this->redismanager->hashSet($day, $list[$k]);
        }
        //print_r($list);exit;
        return true;
    }


    public function getRechargeList($day)
    {
        return $this->db->where('day', $day)->get(self::DB_STATISTICS_RECHARGE)->result();
    }

    public function getRechargeDetailList($day)
    {
        return $this->db->where('day', $day)->get(self::DB_STATISTICS_RECHARGE_DETAIL)->result();
    }

	// 飞单买号查询
    public function getFeiDanList($checkDate)
    {
        $aStartEndTime = $this->_getStartEndTime($checkDate); // 获取审核通过当天查询日期时间段
        $aTaskCheckTime = $this->_getCheckTimeFromDate2Now($checkDate); // 获取从查询日期 至今 的时间段
        if (empty($aStartEndTime) || empty($aTaskCheckTime)) {
            return [];
        }

        $aNewList = [];

//        $this->db->select('user_id as buyer_id,tb_nick as buyer_tb_nick');
//        $this->db->where('status', STATUS_PASSED);
//        $this->db->where('(tb_passed_time BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
//        $list = $this->db->get(self::DB_USER_BIND_INFO)->result();


        $sql = "select * from (SELECT id, `buyer_id`, `buyer_tb_nick`,gmt_taking_task FROM `hilton_task_dianfu` GROUP BY `buyer_id`) tmp 
                  WHERE (`gmt_taking_task` BETWEEN '".$aStartEndTime[0]."' AND '".$aStartEndTime[1]."')";
        $list = $this->db->query($sql)->result();
        //echo $this->db->last_query();exit;

        if (!empty($list)) {
            foreach ($list as $k => $val) {
                // 查询
                $this->db->where('buyer_id', intval($val->buyer_id));
                $this->db->where_in('status', [9,10,11]);
                $this->db->where('(gmt_taking_task BETWEEN "' . $aTaskCheckTime[0] . '" AND "' . $aTaskCheckTime[1] . '")');
                if ($this->db->count_all_results(self::DB_TASK_DIANFU) < 1) {
                    $aNewList[$k] = $val;
                    $aNewList[$k]->buyer_id = encode_id($val->buyer_id);
                }
            }
        }
        //print_r($aNewList);exit;
        unset($list);
        return array_values($aNewList);
    }


    private function _insertRecharge($params)
    {
        $this->db->insert(self::DB_STATISTICS_RECHARGE, [
            'day' => $params['date'],
            'sum_recharge' => $params['sumRecharge'],
            'sum_recharge_correct' => $params['sumRechargeCorrect'],
            'sum_recharge_total' => $params['sumRechargeTotal'],
            'sum_withdraw_total' => $params['sumWithdrawTotal'],
        ]);
    }

    private function _insertRechargeDetail($params, $day)
    {
        $this->db->insert(self::DB_STATISTICS_RECHARGE_DETAIL, [
            'day' => $day,
            'user_name' => $params->user_name,
            'amount' => $params->amount,
            'memo' => $params->memo,
            'op_man' => $params->op_man,
            'op_time' => $params->op_time,
        ]);
    }

    private function _insertRechargeCorrectDetail($params, $day)
    {
        $this->db->insert(self::DB_STATISTICS_RECHARGE_CORRECT_DETAIL, [
            'day' => $day,
            'user_name' => $params->user_name,
            'amount' => $params->amount,
            'memo' => $params->memo,
            'op_man' => $params->op_man,
            'op_time' => $params->op_time,
        ]);
    }

    private function _insertWithdrawDetail($params, $day)
    {
        $this->db->insert(self::DB_STATISTICS_WITHDRAW_DETAIL, [
            'day' => $day,
            'user_name' => $params->user_name,
            'amount' => $params->amount,
            'memo' => $params->memo,
            'op_man' => $params->op_man,
            'op_time' => $params->op_time,
        ]);
    }


    public function getBillList($aStartEndTime, $billType)
    {
        $this->db->select('a.amount, a.oper_id, a.gmt_pay AS op_time, a.memo , b.user_name');
        $this->db->join(self::DB_USER_MEMBER . ' AS b', 'a.user_id = b.id', 'left');
        $this->db->where('a.bill_type', $billType);// 账单类型
        $this->db->where('(a.gmt_pay BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->order_by('a.id', 'ASC');
        return $this->db->get(self::DB_HILTON_BILLS . ' AS a')->result();
    }

    public function getWithdrawDetails($aStartEndTime)
    {
        $this->db->select('a.user_name, a.amount, a.withdraw_service_fee, a.oper_id, b.transfer_time AS op_time');
        $this->db->join(self::DB_WITHDRAW_TIME . ' AS b', 'a.id = b.withdraw_id', 'left');
        $this->db->where('a.status', STATUS_PASSED);// 账单类型
        $this->db->where('(b.transfer_time BETWEEN ' . strtotime($aStartEndTime[0]) . ' AND ' . strtotime($aStartEndTime[1]) . ')');
        $this->db->order_by('a.id', 'ASC');
        return $this->db->get(self::DB_WITHDRAW_RECORD . ' AS a')->result();
    }







    public function getManagersIdsMap()
    {
        $this->db->select('id, real_name');
        $list = $this->db->get(self::DB_ADMIN_MEMBER)->result();

        $map = [];
        foreach ($list as $val) {
            $map[$val->id] = $val->real_name;
        }
        return $map;
    }



    /**
     * 获取某项平台收支项目总和
     * @param int $billType
     * @param array $aStartEndTime
     * @return float 总金额
     */
    public function getSumForSystem($billType, $aStartEndTime)
    {
        $this->db->select('SUM(amount) AS total');
        $this->db->where('user_id', SYSTEM_USER_ID);
        $this->db->where('bill_type', $billType);// 佣金
        $this->db->where('(gmt_pay BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_HILTON_BILLS);
        return !empty($query->row()->total) ? $query->row()->total : 0.00;
    }

    /**
     * 获取某项用户收支项目总和
     * @param int $billType
     * @param array $aStartEndTime
     * @param boolean $bSensitive 脱敏处理
     * @return float 总金额
     */
    public function getSumForUser($billType, $aStartEndTime, $bSensitive = false)
    {
        $this->db->select('SUM(amount) AS total');
        $this->db->where('bill_type', $billType);// 账单类别
        $this->db->where('(gmt_pay BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_HILTON_BILLS);

        if (!empty($query->row()->total)) {
            return  (true === $bSensitive) ? $query->row()->total / 100 : $query->row()->total;
        }
        return 0.00;
    }


    public function getMemberPromoteCnt($memberId, $aStartEndTime)
    {
        $aWhere = ['owner_id' => $memberId, 'promote_time>=' => $aStartEndTime[0], 'promote_time<=' => $aStartEndTime[1]];
        $this->db->select('count(1) AS total')
            ->where($aWhere);

        $query = $this->db->get(self::DB_PROMOTE_RELATION);
        return $query->num_rows() > 0 ? $query->row()->total : 0;
    }

    public function getCertificationPassCnt($ids, $aStartEndTime)
    {
        $this->db->select('count(1) AS total');
        $this->db->where_in('user_id', $ids);
        $this->db->where('status', STATUS_PASSED);
        $this->db->where('(gmt_update BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_USER_CERTIFICATION);
        return $query->num_rows() > 0 ? $query->row()->total : 0;
    }

    public function getTBPassCnt($ids, $aStartEndTime)
    {
        $this->db->select('count(1) AS total');
        $this->db->where_in('user_id', $ids);
        $this->db->where('account_type', PLATFORM_TYPE_TAOBAO);
        $this->db->where('status', STATUS_PASSED);
        $this->db->where('(gmt_update BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_USER_BIND_INFO);
        return $query->num_rows() > 0 ? $query->row()->total : 0;
    }

    public function getMemberInfo($member_id)
    {
        $this->db->select('id, user_name, promote_cnt')->where('id', $member_id);
        return $this->db->get(self::DB_USER_MEMBER)->row();
    }

    public function getPromoteTasksDone($ids, $aStartEndTime)
    {
        $this->db->select('count(1) AS total');
        $this->db->where_in('buyer_id', $ids);
        $this->db->where('status', 11);// 已完成
        $this->db->where('(gmt_update BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->row()->total : 0;
    }

    public function getFirstTasksDone($ids, $aStartEndTime)
    {
        $this->db->select('id, buyer_id');
        $this->db->where_in('buyer_id', $ids);
        $this->db->where('status', 11);// 已完成
        $this->db->where('(gmt_update BETWEEN "' . $aStartEndTime[0] . '" AND "' . $aStartEndTime[1] . '")');
        $this->db->group_by('buyer_id');
        $query = $this->db->get(self::DB_TASK_DIANFU);
        return $query->num_rows() > 0 ? $query->num_rows() : 0;
    }


    //=========================================
    //============    私有方法    ==============
    //=========================================

    private function _getMemberPromoteIds($memberId)
    {
        $ids = $this->db->select('promote_id')->where('owner_id', intval($memberId))->get(self::DB_PROMOTE_RELATION)->result();
        $newIds = [];
        if ($ids) {
            foreach ($ids as $id) {
                $newIds[] = $id->promote_id;
            }
        }
        return $newIds;
    }

    private function _getStartEndTime($date)
    {
        return [$date . ' 00:00:00', $date . ' 23:59:59'];
    }

    private function _getCheckTimeFromDate2Now($date)
    {
        return [$date . ' 00:00:00', date('Y-m-d H:i:s')];
    }


}