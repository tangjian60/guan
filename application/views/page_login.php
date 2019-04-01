<div class="container">
    <form class="form-signin">
        <h2 class="form-signin-heading"><?php echo HILTON_NAME; ?>管理后台</h2>
        <input id="input-user-account" type="text" class="form-control" name="user_account" placeholder="用户名" required autofocus>
        <input id="input-user-passwd" type="password" class="form-control" name="user_passwd" placeholder="密码" required>
        <div id="error_display" class="alert"></div>
        <a id="btn-user-login" class="btn btn-lg btn-primary btn-block" data-url="<?php echo base_url('login/login_handler'); ?>" data-target="<?php echo $redirect_page ? $redirect_page : base_url(); ?>">登录</a>
    </form>
</div>
<script type="text/javascript">
    $(function () {
        $('#btn-user-login').click(function (e) {
            e.preventDefault();

            var form_data = $('.form-signin').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data)) {
                show_error_message('请填写用户名和密码');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);
            show_success_message('正在登录，请稍等...');

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        show_success_message('登录成功');
                        goto_url(that.data('target'), 1000);
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });

        $("#input-user-account").focus();
    });
</script>