<h1 class="page-header">卖家申诉管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">

    <div class="form-group">
        <label>申诉状态</label>
        <select class="form-control filter-control" name="status">
            <option value="">不限</option>
            <?php
            $options = array(
                1 => '申诉中',
                2 => '已处理-订单继续',
                3 => '已处理-关闭订单',
                4 => '放弃申诉'
            );

            foreach ($options as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($status) && $status != '' && $k == $status) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>买手手机</label>
        <input type="text" class="form-control filter-control" name="buyer_name" placeholder="买手手机" value="<?php if (!empty($buyer_name)) echo $buyer_name; ?>">
    </div>
    <div class="form-group">
        <label>商家手机</label>
        <input type="text" class="form-control filter-control" name="seller_name" placeholder="商家手机" value="<?php if (!empty($seller_name)) echo $seller_name; ?>">
    </div>
    <div class="form-group">
        <label>发起时间</label>
        <input type="text" class="form-control filter-control format_date" name="createDate" value="<?php if (!empty($createDate)) echo $createDate; ?>">
    </div>


    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>子任务编号</th>
                <th>父任务编号</th>
                <th>商家手机</th>
                <th>买手手机</th>
                <th>需垫付金额</th>
                <th>佣金</th>
                <th>发起时间</th>
                <th>申诉理由</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo $v->id; ?></td>
                    <td><?php echo encode_id($v->task_id); ?></td>
                    <td><?php echo encode_id($v->task_pid); ?></td>
                    <td><?php echo $v->seller_mobile; ?></td>
                    <td><?php echo $v->buyer_mobile; ?></td>
                    <td><?php echo $v->task_capital; ?></td>
                    <td><?php echo $v->task_commission; ?></td>
                    <td><?php echo $v->gmt_create; ?></td>
                    <td><?php echo $v->reject_reason_txt; ?></td>
                    <td><?php echo isset($options[$v->state]) ? $options[$v->state] : '未知状态'; ?></td>
                    <td>
                        <?php if ($v->state != 3 && $v->state != 4):?>
                            <a class="btn btn-sm btn-warning" href="<?php echo base_url('seller_reject_manage/update_order_info?id=' . encode_id($v->id)) . '&task_id=' . encode_id($v->task_id) . '&' . $params_get; ?>">修改信息</a>
                            <?php if ($v->state == 1):?>
                                <a href="javascript:;" class="btn btn-sm btn-danger btn-cancel-task" data-id="<?php echo encode_id($v->id); ?>" data-tskid="<?php echo encode_id($v->task_id); ?>" data-url="<?php echo base_url('seller_reject_manage/cancel_task'); ?>">关闭订单</a>
                                <a href="javascript:;" class="btn btn-sm btn-info btn-cancel-apply" data-id="<?php echo encode_id($v->id); ?>" data-tskid="<?php echo encode_id($v->task_id); ?>" data-url="<?php echo base_url('seller_reject_manage/cancel_apply'); ?>">取消申诉</a>
                            <?php endif;?>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('fragment_pagination'); ?>
<?php endif; ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".btn-cancel-task").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定要关闭此订单吗？")) {
                that.addClass('disabled');
                that.attr("disabled", true);
                ajax_request(
                    that.data('url'),
                    {
                        apply_id: that.data('id'),
                        task_id: that.data('tskid')
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

        $(".btn-cancel-apply").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定要取消申诉吗？")) {
                that.addClass('disabled');
                that.attr("disabled", true);
                ajax_request(
                    that.data('url'),
                    {
                        apply_id: that.data('id'),
                        task_id: that.data('tskid')
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


        $(".format_date").datetimepicker({
            minView: 'month',
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>