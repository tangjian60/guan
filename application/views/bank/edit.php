<h1 class="page-header">代理商管理--编辑</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">商家账号</label>
                <div class="col-md-8">
                    <input readonly class="form-control" type="text" name="seller_name" value="<?php echo $bankinfo->true_name?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">银行卡号</label>
                <div class="col-md-8">
                    <input placeholder="银行卡号" class="form-control" type="text" value="<?php echo $bankinfo->bank_card_num?>" name="bank_card_num">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label"></label>
                <div class="col-md-9">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <input type="hidden" value="<?php echo $bankinfo->id?>" name="id" />
                <input type="hidden" value="update" name="act" />
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('bank/save?act=update'); ?>">提交</button>
                <a class="btn btn-lg btn-primary btn-back" href="<?php echo base_url('bank'); ?>">返回</a>
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