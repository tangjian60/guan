<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-27
 * Time: 下午6:05
 */
namespace CONSTANT;
class Audit
{
    const TABLE_NAME = 'platform_audit';

    //通过1，2拒绝，3不予处理

    const AUDIT_SUCCESS = 1;

    const AUDIT_REJECT = 2;

    const AUDIT_NO_HANDLE = 3;

    private static $HUABEI_REJECT = array(
        '未开通花呗',
        '非指定审核页面上传',
        '其他',
    );

    private static $CERTIFICATE_REJECT = array(
        '1' => '遮挡身份证正面',
        '2' => '未露脸手持身份证拍照上传',
        '3' => '非本人或非有效身份证件',
        '4' => '该地区名额饱和，暂时停止入驻',
        '5' => '照片模糊不合格，请上传清晰认证照',
        '99' => '其他',
    );

    private static $TAOBAO_REJECT = array(
        '账号安全不符合要求，请提交其他淘宝号审核',
        '真实淘宝账号名与平台输入的淘宝号不相符，请重新提交，注意淘宝旺旺号一致',
        '账号信用等级或者注册时长没有一年，请提交其他淘宝号审核',
        '支付宝实名认证下淘宝账号不对应，请提交正确对应的淘宝账号进行审核',
        '其他',
    );

    private static $SHOP_REJECT = array(
        '真实店铺名与平台绑定店铺名不一致，请从新提交审核，注意对应相符',
        '真实旺旺名与平台留注旺旺名不一致，请重新提交审核，注意对应相符',
        '淘宝后台截图未规范上传，请提交正确的截图进行审核',
        '其他',
    );

    private static $WITHDRAW_REJECT = array(
        '收款人信息有误',
        '当前绑定银行卡的支行信息不完整，请向银行咨询正确完整的开户行信息，并联系平台客服进行修改',
        '当前银行卡为邮政储蓄银行卡，应系统返款要求请联系平台客服换绑其他银行的银行卡',
        '账号资金存在风险，待平台核实后进行人工反馈再行操作',
        '其他',
    );

    public static function getRejectText($type)
    {
        switch($type){
            case "huabei":
                return self::$HUABEI_REJECT;
                break;
            case "certificate":
                return self::$CERTIFICATE_REJECT;
                break;
            case "taobao":
                return self::$TAOBAO_REJECT;
                break;
            case "shop":
                return self::$SHOP_REJECT;
                break;
            case "withdraw":
                return self::$WITHDRAW_REJECT;
                break;
        }
    }
}