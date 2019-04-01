<h1 class="page-header">拼多多任务详情</h1>
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
                        <th>接单拼多多账号</th>
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