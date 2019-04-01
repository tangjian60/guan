<h1 class="page-header">员工管理</h1>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>工号</th>
            <th>账号</th>
            <th>姓名</th>
            <th>角色</th>
            <th>是否渠道</th>
            <th>注册时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $admin_item): ?>
            <tr>
                <td><?php echo $admin_item->id; ?></td>
                <td><?php echo $admin_item->user_name; ?></td>
                <td><?php echo $admin_item->real_name; ?></td>
                <td>
                    <?php
                    switch ($admin_item->account_role) {
                        case EMPLOYEE_ROLE_BOSS:
                            echo '负责人';
                            break;
                        case EMPLOYEE_ROLE_STAFF:
                            echo '普通员工';
                            break;
                    }
                    ?>
                </td>
                <td>否</td>
                <td><?php echo $admin_item->gmt_create; ?></td>
                <td>
                    <?php
                    switch ($admin_item->status) {
                        case STATUS_ENABLE:
                            echo '正常';
                            break;
                        default:
                            echo '禁用';
                            break;
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($admin_item->status == STATUS_ENABLE) {
                        echo '<a href="javascript:;" class="btn-disable-account right_space20" data-id="' . $admin_item->id . '" data-url="' . base_url('staff_manage/operation_handle') . '">禁用</a>';
                        echo '<a href="javascript:;" class="btn-reset-passwd right_space20" data-id="' . $admin_item->id . '" data-url="' . base_url('staff_manage/operation_handle') . '">重置密码</a>';
                        if ($admin_item->account_role == EMPLOYEE_ROLE_STAFF) {
                            echo '<a href="' . base_url('authority_manage?admin_id=' . $admin_item->id) . '">设置权限</a>';
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <form class="form-inline form-staff-manage" style="line-height: 35px">
                <td><span class="important">*<span></td>
                <td><input class="form-control" type="text" name="user_name" style="width:180px;"></td>
                <td><input class="form-control" type="text" name="real_name" style="width:180px;"></td>
                <td>
                    <select class="form-control" name="account_role" style="width:180px;">
                        <option value="<?php echo EMPLOYEE_ROLE_BOSS; ?>">负责人</option>
                        <option value="<?php echo EMPLOYEE_ROLE_STAFF; ?>" selected="selected">普通员工</option>
                    </select>
                </td>
                <td colspan="1"><a href="javascript:;" class="btn btn-sm btn-primary" id="btn-add-staff" data-url="<?php echo base_url('staff_manage/operation_handle'); ?>">添加</a></td>
                <!--
                <td colspan="1"><a href="<?php echo base_url('staff_manage/channel');?>" class="btn btn-sm btn-primary" >添加渠道商</a></td>
                -->
            </form>
        </tr>
        </tbody>
    </table>
</div>
<script>
    $(function () {
        $(".btn-disable-account").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("员工账号只能禁用，不能恢复，确认吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'disable_account',
                        staff_id: that.data('id')
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

        $(".btn-reset-passwd").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确认要重置密码吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'reset_account_passwd',
                        staff_id: that.data('id')
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            alert('重置密码成功');
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });

        $('#btn-add-staff').click(function (e) {
            e.preventDefault();

            var form_data = $('.form-staff-manage').formToJSON();
            var that = $(this);

            if (invalid_parameter(form_data)) {
                alert('账号和姓名不能为空');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);
            form_data.act = 'add_new_staff';

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        alert('添加员工成功');
                        location.reload();
                    } else {
                        alert(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });
</script>