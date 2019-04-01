<h1 class="page-header">垫付任务审核</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
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
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>任务ID</th>
                <th>接单时间</th>
                <th>商品单价</th>
                <th>购买数量</th>
                <th>实付本金</th>
                <th>买家提交的实付本金</th>
                <th>卖家同意支付的本金</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->id); ?></td>
                    <td><?php echo $v->gmt_taking_task; ?></td>
                    <td><?php echo $v->task_capital; ?></td>
                    <td><?php echo $v->num_of_pkg; ?></td>
                    <td><?php echo $v->single_task_capital; ?></td>
                    <td><?php echo $v->real_task_capital; ?></td>
                    <td><?php echo $v->real_task_capital; ?></td>
                    <td>
                        <a href="javascript:;" class="btn btn-sm btn-success btn-approve" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('task_review/operation_handle'); ?>">通过</a>
                        <a href="javascript:;" class="btn btn-sm btn-danger btn-reject" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('task_review/operation_handle'); ?>">拒绝</a>
                        <a href="<?php echo base_url('task_details?task_id=' . encode_id($v->id) . '&task_type=' . TASK_TYPE_DF); ?>" class="btn btn-sm btn-primary">详情</a>
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

        $(".btn-approve").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定通过此垫付任务吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'dianfu_task_approve',
                        dianfu_task_id: that.data('id')
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

        $(".btn-reject").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定拒绝此垫付任务吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'dianfu_task_reject',
                        dianfu_task_id: that.data('id')
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
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });
    });
</script>