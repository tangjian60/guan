<h1 class="page-header">花呗审核--拒绝</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <input  type="hidden" name="huabei_id" value="<?php echo $userbind_info->id?>">
    <div class="panel panel-default">
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">淘宝昵称</label>
                <div class="col-md-8">
                    <?php echo $userbind_info->tb_nick; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">账号等级</label>
                <div class="col-md-8">
                    <?php echo Tbrateenum::_get_name($userbind_info->tb_rate); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">花呗截图</label>
                <div class="col-md-8">
                    <a href="<?php echo CDN_DOMAIN . $userbind_info->tb_huabei_pic; ?>" class="fancybox"><img class="item-pic-box" src="<?php echo CDN_DOMAIN . $userbind_info->tb_huabei_pic; ?>"></a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">拒绝理由</label>
                <div class="col-md-8">
                    <input class="form-control" type="text" name="memo" value="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label"></label>
                <div class="col-md-9">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <input type="hidden" value="update" name="act" />
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('huabei_review/reject_handle'); ?>">提交</button>
                <a class="btn btn-lg btn-primary btn-back" href="<?php echo base_url('huabei_review'); ?>">返回</a>
            </div>
        </div>
    </div>
</form>

<script>
    $(function () {
        $(".btn-transaction-commit").click(function (e) {
            e.preventDefault();
            var form_data = $('.form-transaction').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data)) {
                show_error_message('请填写所有字段');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        show_success_message(e.msg);
                        that.attr("disabled", true);
                        that.addClass('disabled');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });

    $(".fancybox").fancybox();
</script>