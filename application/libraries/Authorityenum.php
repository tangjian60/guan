<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authorityenum
{
    private static $AUTHORITY_LIST = array(
        array('display' => true, 'menu_path' => 'staff_manage', 'menu_name' => '员工管理', 'auth_code' => '1002'),
        array('display' => false, 'menu_path' => 'authority_manage', 'menu_name' => '权限管理', 'auth_code' => '103'),
        array('display' => true, 'menu_path' => 'member_manage', 'menu_name' => '会员管理', 'auth_code' => '201'),
        array('display' => true, 'menu_path' => 'seller_manage', 'menu_name' => '商家管理', 'auth_code' => '202'),
        array('display' => true, 'menu_path' => 'order_manage', 'menu_name' => '父任务管理', 'auth_code' => '301'),
        array('display' => true, 'menu_path' => 'task_manage', 'menu_name' => '子任务管理', 'auth_code' => '302'),
        array('display' => false, 'menu_path' => 'task_details', 'menu_name' => '任务详情', 'auth_code' => '303'),
        array('display' => true, 'menu_path' => 'shop_manage', 'menu_name' => '店铺管理', 'auth_code' => '304'),
        array('display' => true, 'menu_path' => 'taobao_manage', 'menu_name' => '淘宝号/拼多多管理', 'auth_code' => '305'),
        array('display' => true, 'menu_path' => 'notice', 'menu_name' => '公告管理', 'auth_code' => '401'),
        array('display' => false, 'menu_path' => 'notice_edit', 'menu_name' => '公告管理', 'auth_code' => '402'),
        array('display' => true, 'menu_path' => 'certification_review', 'menu_name' => '实名审核', 'auth_code' => '501'),
        array('display' => true, 'menu_path' => 'shop_review', 'menu_name' => '店铺审核', 'auth_code' => '502'),
        array('display' => true, 'menu_path' => 'taobao_review', 'menu_name' => '淘宝/拼多多号审核', 'auth_code' => '503'),
        array('display' => true, 'menu_path' => 'huabei_review', 'menu_name' => '花呗审核', 'auth_code' => '504'),
        array('display' => true, 'menu_path' => 'task_review', 'menu_name' => '垫付任务审核', 'auth_code' => '505'),
        array('display' => true, 'menu_path' => 'spread_relation', 'menu_name' => '推广关系', 'auth_code' => '601'),
        array('display' => true, 'menu_path' => 'promote_statistics', 'menu_name' => '推广统计', 'auth_code' => '709'),
        array('display' => true, 'menu_path' => 'bills', 'menu_name' => '资金流水', 'auth_code' => '701'),
        array('display' => true, 'menu_path' => 'top_up', 'menu_name' => '充值管理', 'auth_code' => '702'),
        array('display' => true, 'menu_path' => 'withdraw', 'menu_name' => '提现管理', 'auth_code' => '703'),
        array('display' => false, 'menu_path' => 'transaction', 'menu_name' => '资金操作', 'auth_code' => '704'),
        array('display' => true, 'menu_path' => 'agent', 'menu_name' => '代理商管理', 'auth_code' => '705'),
        array('display' => true, 'menu_path' => 'agent_buyer', 'menu_name' => '买手代理商管理', 'auth_code' => '708'),
        array('display' => true, 'menu_path' => 'bank', 'menu_name' => '银行卡管理', 'auth_code' => '706'),
        array('display' => true, 'menu_path' => 'task_dispatch', 'menu_name' => '派单管理', 'auth_code' => '707'),
        array('display' => true, 'menu_path' => 'promote_statistics/profit', 'menu_name' => '平台利润统计', 'auth_code' => '710'),
        array('display' => true, 'menu_path' => 'promote_statistics/recharge', 'menu_name' => '充值统计', 'auth_code' => '711'),
        array('display' => true, 'menu_path' => 'bills/record', 'menu_name' => '买手取消任务单记录', 'auth_code' => '713'),
        array('display' => true, 'menu_path' => 'promote_statistics/operation', 'menu_name' => '运营统计', 'auth_code' => '712'),
        array('display' => true, 'menu_path' => 'seller_reject_manage', 'menu_name' => '商家申诉管理', 'auth_code' => '714'),
        //array('display' => true, 'menu_path' => 'seller_reject_hp_manage', 'menu_name' => '商家好评申诉管理', 'auth_code' => '715'),
        array('display' => true, 'menu_path' => 'assemble', 'menu_name' => '常见功能修改管理', 'auth_code' => '716'),
        array('display' => true, 'menu_path' => 'feidan', 'menu_name' => '飞单买号查询', 'auth_code' => '717'),
        array('display' => true, 'menu_path' => 'promote_statistics/system', 'menu_name' => '系统余额统计', 'auth_code' => '718'),
        array('display' => true, 'menu_path' => 'promote_statistics/margin', 'menu_name' => '补单利润统计', 'auth_code' => '719')
    );

    function __construct()
    {
    }

    public static function get_authority_list()
    {
        return self::$AUTHORITY_LIST;
    }

    public static function get_display_list()
    {
        $display_menus = array();
        foreach (self::$AUTHORITY_LIST as $menu_item) {
            if ($menu_item['display']) {
                array_push($display_menus, $menu_item);
            }
        }
        return $display_menus;
    }

    public static function get_auth_code($menu_path)
    {
        if (empty($menu_path)) {
            return CODE_AUTHCODE_ERROR;
        }

        foreach (self::$AUTHORITY_LIST as $menu_item) {
            if ($menu_item['menu_path'] == $menu_path) {
                return $menu_item['auth_code'];
            }
        }

        return CODE_AUTHCODE_ERROR;
    }
}