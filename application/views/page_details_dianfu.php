<h1 class="page-header">垫付任务详情</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务信息</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>任务单号</th>
                        <th>父订单号</th>
                        <th>接单时间</th>
                        <th>接单淘宝账号</th>
                        <th>任务状态</th>
                        <th>任务做单提交时间</th>
                        <th>最后一次操作时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo encode_id($data->id); ?></td>
                        <td><?php echo encode_id($data->parent_order_id); ?></td>
                        <td><?php if (empty($data->gmt_taking_task)) echo '还未接单'; else echo $data->gmt_taking_task; ?></td>
                        <td><?php if (empty($data->buyer_tb_nick)) echo '还未接单'; else echo $data->buyer_tb_nick; ?></td>
                        <td style="color:red;font-size:22px;"><?php echo Taskengine::get_status_name($data->status); ?></td>
                        <td><?php if (empty($data->task_submit_time)) echo '还未做单'; else echo $data->task_submit_time; ?></td>
                        <td><?php if (!empty($data->gmt_update)) echo $data->gmt_update; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务金额</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered" style="width: 30%;">
                    <thead>
                    <tr>
                        <th>任务本金</th>
                        <th>实付金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="font-size: 20px;"><?php echo $data->single_task_capital; ?></td>
                        <?php if ($data->real_task_capital > $data->single_task_capital): ?>
                            <td style="color: #FF0000;font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php elseif ($data->real_task_capital < $data->single_task_capital): ?>
                            <td style="color: #009900;font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php else: ?>
                            <td style="font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php endif ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务条件</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>过滤黑号</th>
                        <th>收藏商品</th>
                        <th>加入购物车下单</th>
                        <th>假聊</th>
                        <th>竞品收藏</th>
                        <th>竞品加购物车</th>
                        <th>放单模式</th>
                        <th>花呗设置</th>
                        <th>性别限制</th>
                        <th>年龄限制 </th>
                        <th>等级限制</th>
                        <th>快递方式</th>
                        <th>评价方式</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $data->is_blacklist==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_fake_chat==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_compete_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_compete_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_preferred==1 ? '优先模式，优先派送给会员接单' : '普通模式' ;?></td>
                        <td><?php echo $data->is_huabei==1 ? '只允许开通花呗的会员接单' : '不限制' ;?></td>
                        <td><?php echo $data->sex_limit=='na' ? '不限' : $data->sex_limit ;?></td>
                        <td><?php
                            switch ($data->age_limit) {
                                case '15':
                                    echo '15-25岁';
                                    break;
                                case '26':
                                    echo '26-35岁';
                                    break;
                                case '36':
                                    echo '36-45岁';
                                    break;
                                case '46':
                                    echo '46-55岁';
                                    break;
                                case '56':
                                    echo '56岁以上';
                                    break;
                                default:
                                    echo '不限制';
                                    break;
                            }
                            ?>
                        </td>
                        <td><?php
                            switch ($data->tb_rate_limit) {
                                case 3:
                                    echo '3心';
                                    break;
                                case 4:
                                    echo '4心';
                                    break;
                                case 5:
                                    echo '5心';
                                    break;
                                case 6:
                                    echo '1钻';
                                    break;
                                case 7:
                                    echo '2钻';
                                    break;
                                case 8:
                                    echo '3钻';
                                    break;
                                case 9:
                                    echo '4钻';
                                    break;
                                case 10:
                                    echo '5钻';
                                    break;
                                case 11:
                                    echo '1皇冠';
                                    break;
                                case 12:
                                    echo '2皇冠';
                                    break;
                                case 13:
                                    echo '3皇冠';
                                    break;
                                case 14:
                                    echo '4皇冠';
                                    break;
                                case 15:
                                    echo '5皇冠 ';
                                    break;
                                default:
                                    echo '不限制';
                                    break;
                            }
                            ?></td>
                        <td><?php echo $data->express_type == 'na' ? '商家快递' : '平台快递' ;?></td>
                        <td><?php
                            switch ($data->comment_type) {
                                case '1':
                                    echo '普通好评';
                                    break;
                                case '2':
                                    echo '指定内容';
                                    break;
                                case '3':
                                    echo '指定图片';
                                    break;
                            }
                            ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务进展</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>操作内容</th>
                        <th>操作者</th>
                        <th>结果</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>主搜页面</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->zhusou_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>货比第一家</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->huobi_1st_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>货比第二家</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->huobi_2nd_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>货比第三家</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->huobi_3rd_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>商品核对结果</td>
                        <td>买家</td>
                        <td><?php if ($data->item_check_status == STATUS_ENABLE) echo '已核对成功'; else echo '还未核对'; ?></td>
                    </tr>
                    <tr>
                        <td>主宝贝详情</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->zhubaobei_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>副宝贝详情</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->fubaobei_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>付款截图</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->fukuan_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>订单号</td>
                        <td>买家</td>
                        <td><?php echo $data->order_number; ?></td>
                    </tr>
                    <tr>
                        <td>好评截图</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->haoping_prove_pic); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $(".fancybox").fancybox();
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>