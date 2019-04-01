<h1 class="page-header">会员提现管理审核--拒绝</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <input  type="hidden" name="withdraw_id" value="<?php echo $withdraw_info->id?>">
    <div class="panel panel-default">
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">提现用户名</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->user_name; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">提现金额</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->amount; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">真实姓名</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->real_name; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">开户行</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->bank_name; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">卡号</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->bank_card_num; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">支行</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->bank_address . $withdraw_info->bank_branch; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">申请提现时间</label>
                <div class="col-md-8">
                    <?php echo $withdraw_info->create_time; ?>
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
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('withdraw/reject_handle'); ?>">提交</button>
                <a class="btn btn-lg btn-primary btn-back" href="<?php echo base_url('withdraw'); ?>">返回</a>
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
</script>