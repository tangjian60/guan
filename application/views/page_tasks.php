<h1 class="page-header">子任务管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>任务类型: </label>
        <select class="form-control" name="task_type">
            <?php
            $task_array = array(
                TASK_TYPE_LL => '流量单',
                TASK_TYPE_DF => '垫付单',
                TASK_TYPE_DT => '多天垫付单',
                TASK_TYPE_PDD => '拼多多'
            );

            foreach ($task_array as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($task_type) && $k == $task_type) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label>任务ID</label>
        <input type="text" class="form-control filter-control" name="task_id" placeholder="任务ID" value="<?php if (!empty($task_id)) echo $task_id; ?>">
    </div>
    <div class="form-group">
        <label>订单ID</label>
        <input type="text" class="form-control filter-control" name="order_id" placeholder="订单ID" value="<?php if (!empty($order_id)) echo $order_id; ?>">
    </div>
    <div class="form-group">
        <label>商家ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="商家ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>会员ID</label>
        <input type="text" class="form-control filter-control" name="buyer_id" placeholder="会员ID" value="<?php if (!empty($buyer_id)) echo $buyer_id; ?>">
    </div>
    <div class="form-group">
        <label>接单时间起</label>
        <input type="text" class="form-control filter-control format_date" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
    </div>
    <div class="form-group">
        <label>接单时间止</label>
        <input type="text" class="form-control filter-control format_date" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
    </div>
    <div class="form-group">
        <label>任务状态</label>
        <select name="status" class="form-control">
            <option value="">全部</option>
            <?php
            foreach (Taskengine::get_all_status() as $k => $v) {
                if ($k == Taskengine::TASK_STATUS_DZF) continue;
                echo '<option value="' . $k . '"';
                if (isset($status) && $k == $status) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
    </div>
    <div>
    <div class="form-group">
        <label>发单时间起</label>
        <input type="text" class="form-control filter-control format_date" name="create_start_time" value="<?php if (!empty($create_start_time)) echo $create_start_time; ?>">
    </div>
    <div class="form-group">
        <label>发单时间止</label>
        <input type="text" class="form-control filter-control format_date" name="create_end_time" value="<?php if (!empty($create_end_time)) echo $create_end_time; ?>">
    </div>
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>任务编号</th>
                <th>父订单编号</th>
                <th>商家ID</th>
                <th>宝贝</th>
                <th>会员ID</th>
                <th>任务起止时间</th>
                <th>接单时间</th>
                <th>任务做单提交时间</th>
                <th>快递单号是否成功</th>
                <th>状态</th>
                <th>支付时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->id); ?></td>
                    <td><?php echo encode_id($v->parent_order_id); ?></td>
                    <td><?php echo encode_id($v->seller_id); ?></td>
                    <td>
                        <a href="<?php echo CDN_DOMAIN . $v->item_pic; ?>" class="fancybox"><img class="item-pic-box" src="<?php echo CDN_DOMAIN . $v->item_pic; ?>"></a>
                        <a href="<?php echo $v->item_url; ?>" title="<?php echo $v->item_title; ?>" target="_blank"><?php echo beauty_display($v->item_title, 6); ?></a>
                    </td>
                    <td><?php echo ($v->buyer_id > 0) ? encode_id($v->buyer_id) : ''; ?></td>

                    <td><?php echo $v->start_time . ' ~ ' . $v->end_time; ?></td>

                    <td><?php echo $v->gmt_taking_task; ?></td>
                    <td><?php echo $v->task_submit_time; ?></td>
                    <td><?php
                        if($v->express_success == ''){
                            echo '';
                        }elseif($v->express_success == 1){
                           echo '成功';
                        }elseif($v->express_success == 0){
                            echo '失败';
                        }else{
                            echo '未知';
                        }
                    ?>
                    </td>
                    <td><?php echo Taskengine::get_status_name($v->status); ?></td>
                    <td><?php echo $v->gmt_create; ?></td>
                    <td>
                        <a href="<?php echo base_url('task_details?task_id=' . encode_id($v->id) . '&task_type=' . $task_type); ?>" class="btn btn-sm btn-primary"><?php if ($v->status == Taskengine::TASK_STATUS_PTSH) echo '审核'; else echo '详情'; ?></a>
                        <?php
                            if($v->status == Taskengine::TASK_STATUS_MJSH_BTG){
                                ?>
                                <button data-url="<?php echo base_url('Task_guanbichongzhi')?>" data-id="<?php echo $v->id;?>" data-type="<?php echo $v->task_type;?>" class="btn btn-sm btn-danger" id="guanbi" data-act="guanbi">关闭此订单</button>
                                <button data-url="<?php echo base_url('Task_guanbichongzhi')?>" data-id="<?php echo $v->id;?>" data-type="<?php echo $v->task_type;?>" class="btn btn-sm btn-danger" id="chongzhi" data-act="chongzhi">重置此订单</button>
                                <?php
                            }
                        ?>

                        <?php if($v->status == Taskengine::TASK_STATUS_DPJ || $v->status == Taskengine::TASK_STATUS_YWC || $v->status == Taskengine::TASK_STATUS_HPSH) {
                            if ($v->express_success != 1 && $v->is_express == 1) {
                                ?>
                                <a href="javascript;" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('Shop_manage/reassert_express'); ?>" data-type="<?php echo $v->task_type;?>" class="btn btn-sm btn-danger btn-yto">重申快递单号</a>
                            <?php
                           }
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $this->load->view('fragment_pagination'); ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });

        $(".fancybox").fancybox();

        $("#guanbi").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("订单关闭后，买手不再接此单，费用将退还给商家，确定关闭吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        id: that.data('id'),
                        type: that.data('type'),
                        act: that.data('act')
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });

        $("#chongzhi").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("订单重置后，订单数据将被清空，订单回到接单池，准备重新发放,确认重置吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        id: that.data('id'),
                        type: that.data('type'),
                        act:that.data('act'),
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });


        $('.btn-yto').click(function (e) {
            e.preventDefault();
            if (confirm("确定要重新申请快递单号吗")) {
                yto($(this).data('url'), $(this).data('id'), $(this).data('type'));
            }
        });

        function yto(u,id, t)
        {
            ajax_request(
                u
                , {id: id, type: t}
                ,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        alert('重申快递单号成功！');
                        location.reload();
                    } else {
                        alert(e.msg);
                        location.reload();
                       /* alert(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);*/
                    }
                   /* alert(data.msg);
                    location.reload();*/
                });
        }
    });
</script>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>