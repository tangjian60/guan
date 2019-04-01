<h1 class="page-header">推广关系管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>上线会员ID</label>
        <input type="text" class="form-control filter-control" name="owner_id" placeholder="会员ID" value="<?php if (!empty($owner_id)) echo $owner_id; ?>">
    </div>
    <div class="form-group">
        <label>下线会员ID</label>
        <input type="text" class="form-control filter-control" name="promote_id" placeholder="会员ID" value="<?php if (!empty($promote_id)) echo $promote_id; ?>">
    </div>
    <div class="form-group">
        <label>首单奖励</label>
        <select class="form-control age_limit" name="first_reward">
            <option value="">全部</option>
            <?php
            $options = array(
                STATUS_ENABLE => '已完成',
                2 => '未完成'
            );

            foreach ($options as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($first_reward) && $first_reward != '' && $k == $first_reward) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
        <p id="tips-age-limit" class="color-red"></p>
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
                <th>上线会员ID</th>
                <th>下线会员ID</th>
                <th>首单奖励</th>
                <th>推广时间</th>
                <th>返利有效期</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->owner_id); ?></td>
                    <td><?php echo encode_id($v->promote_id); ?></td>
                    <td><?php if ($v->first_reward == STATUS_ENABLE) echo '已完成'; else echo '未完成'; ?></td>
                    <td><?php echo $v->promote_time; ?></td>
                    <td><?php echo $v->validity_time; ?></td>
                    <td><?php if ($v->status == STATUS_ENABLE) echo '有效'; else echo '失效'; ?></td>
                    <td>
                        <?php if ($v->status == STATUS_ENABLE): ?>
                            <a href="javascript:;" class="btn btn-sm btn-danger btn-delete-promotion" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('spread_relation/operation_handle'); ?>">删除推广关系</a>
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

        $(".btn-delete-promotion").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定要删除此推广关系吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'delete_promotion_relation',
                        promotion_id: that.data('id')
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