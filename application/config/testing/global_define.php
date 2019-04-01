<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Global defines
|--------------------------------------------------------------------------
|
| Public defines for platform
|
*/
define('SYSTEM_CODE_NAME', 'ZCM');
define('HILTON_NAME', '吉娃娃');
define('HILTON_SLOGAN', '吉娃娃');
define('CUSTOM_SERVICE_QQ', '800853375');
define('CUSTOM_SERVICE_GROUP', '800853375');
define('SELLER_PROMOTE_LINK', 'http://www.zcm889.test.com/shang/user/register?r=');
define('BUYER_PROMOTE_LINK', 'http://www.zcm889.test.com/buyer/user?r=');

define('MASTER_SWITCH', true);
define('WITHDRAW_MASTER_SWITCH', true);
define('USER_REGISTRY_SWITCH', true);
define('SELLER_REGISTRY_SWITCH', true);
define('MIN_WITHDRAW_AMOUNT', 100);
define('SELLER_WITHDRAW_FEE', 0.003);
define('MIN_COMMISSION_DISCOUNT', 90);

define('PROMOTION_VALIDITY_DAYS', 180);
define('PROMOTION_FIRST_REWARD', 5);
define('PROMOTION_PROPORTION_OF_PROCEEDS', 0.05);
define('PROMOTION_TOP_UP_BONUS', 0.002);

define('SELLER_TO_NICK_INTERVAL', 1);
define('SELLER_TO_BUYER_INTERVAL', 1);
define('SHOP_TO_NICK_INTERVAL', 30);
define('SHOP_TO_BUYER_INTERVAL', 30);
define('GOODS_TO_NICK_INTERVAL', 35);
define('GOODS_TO_BUYER_INTERVAL', 35);

define('SHOP_LIULIANG_INTERVAL', 1);
define('SHOP_ADD_CART_INTERVAL', 15);
define('MAX_INTERVAL_DAYS', 99);
define('ZUODAN_SHIJIAN_MIN', 90);

define('TOP_UP_INTERVAL_MINS', 10);
define('ITEMS_PER_LOAD', 20);
define('MAX_BIND_ACCOUNT_CNT', 3);
define('MAX_CLAIM_TASK_LIMIT', 1);
define('MAX_DF_TASKS_PER_NICK', 4);

define('PLATFORM_TYPE_TAOBAO', 1);
define('PLATFORM_TYPE_JD', 2);
define('PLATFORM_TYPE_PINDUODUO', 3);
define('PLATFORM_TYPE_AMAZON', 4);

define('CDN_DOMAIN', '//cdn.zcm889.test.com');
define('CDN_BINARY_URL', '//cdn.zcm889.test.com/binary/');
define('HELP_LINK', 'http://help.zcm889.test.com/');

define('REDIS_SERVER', 'redis.zcm889.test.com');
define('REDIS_PORT', 6379);
define('REDIS_TIME_OUT', 2);
define('REDIS_TTL', 3600);
define('PERMISSION_PREFIX', 'ADMIN-PER-');
define('TASK_PREFIX', 'TASK-');
define('TASK_TYPE_DF', 'DIANFU');
define('TASK_TYPE_LL', 'LIULIANG');
define('TASK_TYPE_PDD', 'PINDUODUO');
define('TASK_TYPE_DT', 'DUOTIAN'); // 多天垫付任务
define('TASK_TYPE_CANCELLED', 'QUXIAO');
define('DEVICE_TYPE_MOBILE', 1);
define('DEVICE_TYPE_COMPUTER', 2);
define('CACHE_PREFIX_TJ_DIR', 'statistics');
define('CACHE_PREFIX_TJ', 'TJ-');

define('SESSION_IS_BOSS', 'SESSION_IS_BOSS');
define('SESSION_MANAGER_ID', 'SESSION_MANAGER_ID');
define('SESSION_MANAGER_NAME', 'SESSION_MANAGER_NAME');
define('SESSION_USER_ID', 'SESSION_USER_ID');
define('SESSION_USER_NAME', 'SESSION_USER_NAME');
define('SESSION_AUTH_STATUS', 'SESSION_AUTH_STATUS');
define('SESSION_IS_KAOSHI', 'SESSION_IS_KAOSHI');
define('SESSION_SELLER_ID', 'SESSION_SELLER_ID');
define('SESSION_SELLER_NAME', 'SESSION_SELLER_NAME');
define('COOKIE_RECOMMEND_ID', 'r_recommend_id');

define('EMPLOYEE_ROLE_BOSS', 'boss');
define('EMPLOYEE_ROLE_STAFF', 'staff');

define('CODE_SUCCESS', 0);
define('CODE_BAD_PASSWORD', 1001);
define('CODE_BANED', 1002);
define('CODE_WAIT_PER', 1003);
define('CODE_DB_ERROR', 1004);
define('CODE_BAD_REQUEST', 1005);
define('CODE_USER_CONFLICT', 1006);
define('CODE_UNKNOWN_ERROR', 1007);
define('CODE_SESSION_EXPIRED', 1008);
define('CODE_BAD_PROVCODE', 1009);
define('CODE_OUT_OF_RANGE', 1010);

define('CODE_AUTHCODE_ERROR', 0);
define('CODE_PERMISSION_DENIED', 2001);
define('CODE_BAD_PARAMETER', 2002);
define('CODE_SIGN_FAILED', 3);
define('CODE_DB_FAILED', 4);

define('CODE_INSUFFICIENT_BALANCE', 3001);
define('CODE_PAY_FAILED', 3009);

define('USER_TYPE_BUYER', 1);
define('USER_TYPE_SELLER', 2);
define('USER_TYPE_SYSTEM', 3);
define('SYSTEM_USER_ID', 1);

define('STATUS_ENABLE', 1);
define('STATUS_DISABLE', 0);

define('NOTICE_TYPE_ALL', 1);
define('NOTICE_TYPE_BUYER', 2);
define('NOTICE_TYPE_SELLER', 3);

define('STATUS_DEFAULT', 0);
define('STATUS_PASSED', 1);
define('STATUS_CHECKING', 2);
define('STATUS_FAILED', 3);
define('STATUS_CANCEL', 4);
define('STATUS_BAN', 5);

define('BTN_TYPE_LOGOUT', 1);
define('BTN_TYPE_BACK', 2);

define('SHOP_TYPE_TAOBAO', 1);
define('SHOP_TYPE_TMALL', 2);
define('SHOP_TYPE_PINDUODUO', 3);

define('COMMENT_TYPE_NORMAL', 1);
define('COMMENT_TYPE_TEXT', 2);
define('COMMENT_TYPE_PICTURE', 3);

define('NOT_AVAILABLE', 'na');

define('SELLER_CONCLUSION_TASK_OK', 1);
define('SELLER_CONCLUSION_TASK_BAD', 2);
define('SELLER_CONCLUSION_REVIEW_OK', 3);
define('SELLER_CONCLUSION_REVIEW_BAD', 4);

define('PROMOTE_TYPE_REG', 1);
define('PROMOTE_TYPE_TASK', 2);
define('PROMOTE_TYPE_TOP_UP', 3);

define('BUYER_WITHDRAW_SERVICE_FEE',0.01);  // 买家提现收取1%的手续费