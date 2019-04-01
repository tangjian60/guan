<h1 class="page-header">店铺信息--编辑</h1>
<form class="form-horizontal form-transaction" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">商家ID</label>
                <div class="col-md-8">
                    <input  readonly class="form-control" type="text" name="seller_id" value="<?php echo encode_id($data->seller_id);?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">店铺名称</label>
                <div class="col-md-8">
                    <input  class="form-control" type="text" name="shop_name" value="<?php echo $data->shop_name?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">店主旺旺</label>
                <div class="col-md-8">
                    <input readonly placeholder="店主旺旺" class="form-control" type="text" value="<?php echo $data->shop_ww?>" name="shop_ww">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">店铺省份</label>
                <div class="col-md-8">
                    <input readonly placeholder="店铺省份" class="form-control" type="text" value="<?php echo $data->shop_province?>" name="shop_province">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">店铺城市</label>
                <div class="col-md-8">
                    <input readonly placeholder="店铺城市" class="form-control" type="text" value="<?php echo $data->shop_city?>" name="shop_city">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">店铺区县</label>
                <div class="col-md-8">
                    <input readonly placeholder="店铺区县" class="form-control" type="text" value="<?php echo $data->shop_county?>" name="shop_county">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label"></label>
                <div class="col-md-9">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <input type="hidden" value="<?php echo $data->id?>" name="id" />
                <input type="hidden" value="update" name="act" />
                <button class="btn btn-lg btn-primary btn-transaction-commit" data-url="<?php echo base_url('shop_manage/edit_do?act=update'); ?>">提交</button>
                <?php $id = decode_id($data->id); ?>
                <a class="btn btn-lg btn-primary btn-back" href="<?php echo base_url('shop_manage?shop_ww='.$data->shop_ww); ?>">返回</a>
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