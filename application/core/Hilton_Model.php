<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hilton_Model extends CI_Model
{

    const DB_ADMIN_MEMBER = 'hilton_managers';
    const DB_ADMIN_PERMISSION = 'manager_authorize';
    const DB_USER_MEMBER = 'user_members';
    const DB_PROMOTE_RELATION = 'promote_relation';
    const DB_SELLER_AGENT = 'seller_agent';
    const DB_BUYER_AGENT = 'buyer_agent';
    const DB_OPERATION_LOG = 'operation_log';

    const DB_PLATFORM_NOTICE = 'platform_notice';
    const DB_USER_MSG = 'user_messages';
    const DB_USER_CERT = 'user_certification';
    const DB_USER_BIND = 'user_bind_info';
    const DB_SHOP_BIND = 'seller_bind_shops';
    const DB_TASK_TEMPLATES = 'task_templates';
    const DB_WITHDRAW_RECORD = 'user_withdraw_record';
    const DB_TOP_UP_RECORD = 'user_top_up_records';
    const DB_WITHDRAW_TIME = 'user_withdraw_time';

    function __construct()
    {
        parent::__construct();
    }
}