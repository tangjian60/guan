<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 下午2:56
 */
namespace CONSTANT;
class Agent
{
    const TABLE_NAME = 'seller_agent';

    const TB_BUYER = 'buyer_agent';

    const STATUS_NORMAL = 1;

    const STATUS_FROZEN = 2;

    /*
    const STATUS = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_FROZEN => '冻结',
    ];
    */

    const CODE_ERROR_FROZEN = 10001;

    const CODE_ERROR_UNFROZEN = 10001;

}