<div class="row row-p0m0">
    <div class="col-md-11 col-md-red">
        <h3>修改密码</h3>
    </div>
</div>
<div class="col-lg-12">
    <div class="bootstrap-admin-back-to-parent panel panel-default" style="padding:50px 0 50px 60px;">
        <form class="form-inline form-change-passwd" style="line-height: 35px">
            <div class="row">
                <div class="col-md-10">
                    <label>原密码</label>
                    <input type="password" class="form-control" name="old_passwd" placeholder="原密码">
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <label>新密码</label>
                    <input type="password" class="form-control" name="new_passwd" placeholder="新密码">
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <label>新密码</label>
                    <input type="password" class="form-control" name="confirm_passwd" placeholder="再输入一次新密码">
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <button id="btn-change-passwd" type="button" class="btn btn-primary" data-url="<?php echo base_url('changepwd/change_handler'); ?>">确定</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#btn-change-passwd').click(function (e) {
            e.preventDefault();

            var form_data = $('.form-change-passwd').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data)) {
                show_error_message('请填写所有字段');
                return;
            }

            if (form_data.new_passwd == form_data.old_passwd) {
                show_error_message('新密码不能和旧密码一样');
                return;
            }

            if (form_data.new_passwd != form_data.confirm_passwd) {
                show_error_message('两次新密码输入不一致');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);
            show_success_message('提交中...');

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        show_success_message('修改成功');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });
</script>