<h1 class="page-header">父任务管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>订单ID</label>
        <input type="text" class="form-control filter-control" name="order_id" placeholder="订单ID" value="<?php if (!empty($order_id)) echo $order_id; ?>">
    </div>
    <div class="form-group">
        <label>商家ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="商家ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>开始时间</label>
        <input type="text" class="form-control filter-control format_date" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
    </div>
    <div class="form-group">
        <label>结束时间</label>
        <input type="text" class="form-control filter-control format_date" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
    </div>
    <div class="form-group">
        <label>订单状态</label>
        <select name="status" class="form-control">
            <option value="">全部</option>
            <?php
            $task_array = array(
                Taskengine::TASK_STATUS_DZF => '待支付',
                Taskengine::TASK_STATUS_DJD => '已支付'
            );

            foreach ($task_array as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($status) && $k == $status) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            $taskStatus = isset($status) && $status == Taskengine::TASK_STATUS_DJD ? '已支付' : '待支付';
            ?>
        </select>
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号<?php echo $status; ?></th>
                <th>订单编号</th>
                <th>商家ID</th>
                <th>放单时间</th>
                <th>任务起止时间</th>
                <th>宝贝</th>
                <th>放单时间间隔</th>
                <th>任务单量</th>
                <th>任务金额</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->id); ?></td>
                    <td><?php echo encode_id($v->seller_id); ?></td>
                    <td><?php echo $v->gmt_create; ?></td>
                    <td><?php echo $v->start_time . ' ~ ' . $v->end_time; ?></td>
                    <td>
                        <a href="<?php echo CDN_DOMAIN . $v->item_pic; ?>" class="fancybox"><img class="item-pic-box" src="<?php echo CDN_DOMAIN . $v->item_pic; ?>"></a>
                        <a href="<?php echo $v->item_url; ?>" title="<?php echo $v->item_title; ?>" target="_blank"><?php echo beauty_display($v->item_title, 6); ?></a>
                    </td>
                    <td>
                        <?php
                        if ($v->hand_out_interval > 0) {
                            echo $v->hand_out_interval . '分钟';
                        } else {
                            echo '无间隔';
                        }
                        ?>
                    </td>
                    <td><?php echo $v->task_cnt; ?></td>
                    <td><?php echo '本金' . $v->fee_order_total_capital . '佣金' . $v->fee_order_total_commission; ?></td>
                    <td><?php echo $v->status == Taskengine::TASK_STATUS_DJD ? '已支付' : '待支付';?></td>
                    <td>
                        <a href="<?php echo base_url('task_manage?order_id=' . encode_id($v->id) . '&task_type=' . $v->task_type); ?>" class="btn btn-sm btn-primary">订单详情</a>
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
    });
</script>