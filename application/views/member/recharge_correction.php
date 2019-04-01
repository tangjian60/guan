<h1 class="page-header">资金操作 - 充值校正</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:red;">警告！高危操作！该操作直接以额外的方式扣除或增加用户余额</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">会员ID</label>
                <div class="col-md-8">
                    <input placeholder="会员ID" class="form-control" type="text" name="member_id" value="<?php if (!empty($member_id)) echo $member_id; ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">金额</label>
                <div class="col-md-8">
                    <input placeholder="转账金额" class="form-control" type="number" name="amount" onmousewheel="return false;">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">备注</label>
                <div class="col-md-8">
                    <textarea class="form-control" rows="8" name="memo"></textarea>
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('transaction/do_recharge_correction'); ?>">提交</button>
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
                        that.attr("disabled",true);
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });
</script>