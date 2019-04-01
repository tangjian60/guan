<h1 class="page-header">资金冻结</h1>
<form class="form-horizontal form-freezing" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:red;">警告！高危操作！该操作直接冻结会员的本金或者佣金，从而影响会员提现。</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">会员ID</label>
                <div class="col-md-8">
                    <input placeholder="会员ID" class="form-control" type="text" name="member_id" value="<?php if (!empty($member_id)) echo $member_id; ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">冻结本金</label>
                <div class="col-md-8">
                    <input placeholder="冻结本金金额" class="form-control" type="number" name="freezing_capital_amount" value="<?php echo $member_data[0]->freezing_capital_amount;?>" onmousewheel="return false;" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">冻结佣金</label>
                <div class="col-md-8">
                    <input placeholder="冻结佣金金额" class="form-control" type="number" name="freezing_commission_amount" value="<?php echo $member_data[0]->freezing_commission_amount;?>" onmousewheel="return false;" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-8">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <button class="btn btn-lg btn-primary btn-freezing-commit" data-url="<?php echo base_url('member_manage/freezing_commit'); ?>">提交</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function () {
        $(".btn-freezing-commit").click(function (e) {
            e.preventDefault();
            var form_data = $('.form-freezing').formToJSON();
            var amount = /(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/;
            var that = $(this);

            hide_error_message();

            if (!amount.test(form_data.freezing_capital_amount) && !amount.test(form_data.freezing_commission_amount)) {
                alert('请填写冻结金额');
                return;
            }

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