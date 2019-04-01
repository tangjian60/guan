<h1 class="page-header">买家淘宝/拼多多账号管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>会员ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="会员ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>会员名</label>
        <input type="text" class="form-control filter-control" name="user_name" placeholder="会员名" value="<?php if (!empty($user_name)) echo $user_name; ?>">
    </div>
    <div class="form-group">
        <label>nick</label>
        <input type="text" class="form-control filter-control" name="tb_nick" placeholder="淘宝nick" value="<?php if (!empty($tb_nick)) echo $tb_nick; ?>">
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
        <label>状态</label>
        <select name="status" class="form-control">
            <option value="">全部</option>
            <?php
            $task_array = array(
                STATUS_PASSED => '正常',
                STATUS_CHECKING => '审核中',
                STATUS_FAILED => '审核失败',
                STATUS_BAN => '黑名单'
            );

            foreach ($task_array as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($status) && $k == $status) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label>账号类型</label>
        <select name="account_type" class="form-control">
            <option value="<?php echo PLATFORM_TYPE_TAOBAO ;?>">淘宝</option>
            <option value="<?php echo PLATFORM_TYPE_PINDUODUO ;?>">拼多多</option>
            <option value="">全部</option>
        </select>
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>会员ID</th>
                <th>会员名</th>
                <th>昵称</th>
                <th>账号类型</th>
                <th>账号等级</th>
                <th>性别</th>
                <th>年龄</th>
                <th>收货人姓名</th>
                <th>收货人电话</th>
                <th>收货人地址</th>
                <th>花呗状态</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->user_id); ?></td>
                    <td><?php echo $v->user_name; ?></td>
                    <td style="font-family: monospace;"><?php echo $v->tb_nick; ?></td>
                    <td><?php echo $v->account_type == PLATFORM_TYPE_TAOBAO ? '淘宝' : '拼多多'; ?></td>
                    <td><?php echo Tbrateenum::_get_name($v->tb_rate); ?></td>
                    <td><?php echo $v->sex; ?></td>
                    <td><?php echo $v->age . '-' . ($v->age + 9); ?></td>
                    <td><?php echo $v->tb_receiver_name; ?></td>
                    <td><?php echo $v->tb_receiver_tel; ?></td>
                    <td><?php echo $v->receiver_province . $v->receiver_city . $v->receiver_county . $v->tb_receiver_addr; ?></td>
                    <td>
                        <?php if ($v->account_type == PLATFORM_TYPE_TAOBAO): ?>
                            <?php if($v->huabei_status == STATUS_PASSED) echo '已认证'; else echo '未认证'; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        switch ($v->status){
                            case STATUS_PASSED:
                                echo '正常';
                                break;
                            case STATUS_CHECKING:
                                echo '审核中';
                                break;
                            case STATUS_FAILED:
                                echo '审核失败';
                                break;
                            case STATUS_BAN:
                                echo '黑名单';
                                break;
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($v->status == STATUS_BAN): ?>
                            <a href="javascript:;" class="btn btn-sm btn-default btn-free-blacklist" data-id="<?php echo $v->id; ?>" data-user-id="<?php echo $v->user_id; ?>" data-url="<?php echo base_url('taobao_manage/operation_handle'); ?>">解除黑名单</a>
                        <?php else: ?>
                            <a href="javascript:;" class="btn btn-sm btn-danger btn-set-blacklist" data-id="<?php echo $v->id; ?>" data-user-id="<?php echo $v->user_id; ?>" data-url="<?php echo base_url('taobao_manage/operation_handle'); ?>">拉入黑名单</a>
                        <?php endif; ?>
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
                        taobao_id: that.data('id'),
                        user_id: that.data('user-id'),
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
                        taobao_id: that.data('id'),
                        user_id: that.data('user-id'),
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

        $(".fancybox").fancybox();
    });
</script>