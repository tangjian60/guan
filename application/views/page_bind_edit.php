<h1 class="page-header">买手帐号信息--编辑</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">买手ID</label>
                <div class="col-md-8">
                    <input  readonly class="form-control" type="text" name="user_id" value="<?php echo encode_id($bankinfo->user_id);?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">账号名称</label>
                <div class="col-md-8">
                    <input  class="form-control" type="text" name="tb_nick" value="<?php echo $bankinfo->tb_nick?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人姓名</label>
                <div class="col-md-8">
                    <input placeholder="收货人姓名" class="form-control" type="text" value="<?php echo $bankinfo->tb_receiver_name?>" name="tb_receiver_name">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人电话</label>
                <div class="col-md-8">
                    <input placeholder="收货人电话" class="form-control" type="text" value="<?php echo $bankinfo->tb_receiver_tel?>" name="tb_receiver_tel">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人的省份</label>
                <div class="col-md-8">
                    <input placeholder="收货人的省份" class="form-control" type="text" value="<?php echo $bankinfo->receiver_province?>" name="receiver_province">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人的城市</label>
                <div class="col-md-8">
                    <input placeholder="收货人的城市" class="form-control" type="text" value="<?php echo $bankinfo->receiver_city?>" name="receiver_city">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人的区县</label>
                <div class="col-md-8">
                    <input placeholder="收货人的区县" class="form-control" type="text" value="<?php echo $bankinfo->receiver_county?>" name="receiver_county">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">收货人街道地址</label>
                <div class="col-md-8">
                    <input placeholder="收货人街道地址" class="form-control" type="text" value="<?php echo $bankinfo->tb_receiver_addr?>" name="tb_receiver_addr">
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
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('assemble/save_bind?act=update'); ?>">提交</button>
                <?php $user_id = decode_id($bankinfo->user_id); ?>
                <?php $user_id = encode_id($bankinfo->user_id);?>
                <a class="btn btn-lg btn-primary btn-back" href="<?php echo base_url('assemble/bind_info?user_id='.$user_id); ?>">返回</a>
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