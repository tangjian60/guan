<h1 class="page-header">买手取消任务单记录</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>子任务编号</label>
        <input placeholder="点击输入" class="form-control" name="task_id" value="<?php if (!empty($task_id)) echo encode_id($task_id); ?>">
    </div>
    <div class="form-group">
        <label>会员id</label>
        <input placeholder="点击输入" class="form-control" name="buyer_id" value="<?php if (!empty($buyer_id)) echo encode_id($buyer_id); ?>">
    </div>
    <div class="form-group">
        <label>宝贝id</label>
        <input placeholder="点击输入" class="form-control" name="item_id" value="<?php if (!empty($item_id)) echo $item_id; ?>">
    </div>
    <div class="form-group">
        <label>取消日期</label>
        <input type="text" class="form-control filter-control format_date" name="gmt_cancelled" value="<?php if (!empty($gmt_cancelled)) echo $gmt_cancelled; ?>">
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="5%">序</th>
                <th>取消时间</th>
                <th>子任务编号</th>
                <th width="20%">宝贝id</th>
                <th>宝贝标题</th>
                <th>会员id</th>
                <th>取消原因</th>
                <!-- <th width="20%">操作</th>-->
            </tr>
            </thead>
            <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="3">无记录</td>
                </tr>
            <?php else: ?>
            <?php foreach ($data as $key=>$v): ?>
                <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo $v->gmt_cancelled; ?></td>
                    <td><?php echo encode_id($v->task_id); ?></td>
                    <td><?php echo $v->item_id; ?></td>
                    <td><?php echo $v->item_title; ?></td>
                    <td><?php echo encode_id($v->buyer_id); ?></td>
                    <td><?php echo $v->cancel_reason; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <!--
            <tr>
                <form class="form-inline form-staff-manage" style="line-height: 35px">
                    <td colspan="1"><a href="<?php echo base_url('member_manage/channel');?>" class="btn btn-sm btn-primary" >添加渠道商</a></td>
                </form>
            </tr>
            -->
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

        $(".btn-set-blacklist").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定将此账号列入黑名单吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'set_black',
                        member_id: that.data('id')
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

        $(".btn-free-blacklist").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定解除此账号的黑名单吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'unset_black',
                        member_id: that.data('id')
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
