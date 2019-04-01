<h1 class="page-header">做单信息修改</h1>
<form class="form-horizontal form-update-member" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:red;">警告！高危操作！该操作直接修改会员已经审核通过的信息</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <?php if(empty($task_detail)): ?>
                <div class="panel-heading">
                    <h3 class="panel-title" style="color:red;">暂无该订单信息</h3>
                </div>
            <?php else: ?>
                <input type="hidden" name="apply_id" value="<?php echo $apply_id; ?>">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                <div class="form-group">
                    <label class="col-md-3 control-label">订单号</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="order_number" value="<?php echo $task_detail->order_number; ?>" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">垫付金额</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="single_task_capital" value="<?php echo $task_detail->single_task_capital; ?>" >
                    </div>
                </div>

                <?php $dft_pic = CDN_BINARY_URL . 'cross.png';?>
                <div class="form-group">
                    <label class="col-md-3 control-label">货比三家截图</label>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $zhusou_prove_pic = $dft_pic;
                                !empty($task_detail->zhusou_prove_pic) && $zhusou_prove_pic = CDN_DOMAIN . $task_detail->zhusou_prove_pic;
                            ?>
                            <img data-input-name="zhusou_prove_pic" class="image-upload" src="<?php echo $zhusou_prove_pic; ?>">
                            <!--<div class="image-uppload-tips">主搜索<br>页面</div>-->
                            <input type="hidden" name="zhusou_prove_pic" value="<?php echo $task_detail->zhusou_prove_pic; ?>">
                        </div>
                        <p class="help-block">主搜索页面关键词+主宝贝</p>
                        <p class="help-block">(浏览≥1分钟)</p>
                    </div>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $huobi_1st_prove_pic = $dft_pic;
                                !empty($task_detail->huobi_1st_prove_pic) && $huobi_1st_prove_pic = CDN_DOMAIN . $task_detail->huobi_1st_prove_pic;
                            ?>
                            <img data-input-name="huobi_1st_prove_pic" class="image-upload" src="<?php echo $huobi_1st_prove_pic; ?>">
                            <input type="hidden" name="huobi_1st_prove_pic" value="<?php echo $task_detail->huobi_1st_prove_pic; ?>">
                        </div>
                        <p class="help-block">货比第一家</p>
                        <p class="help-block">(浏览≥1分钟)</p>
                        <p class="help-block"><a href="<?php echo $huobi_1st_prove_pic?>" target="_blank" class="btn btn-primary">查看大图</a></p>
                    </div>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $huobi_2nd_prove_pic = $dft_pic;
                                !empty($task_detail->huobi_2nd_prove_pic) && $huobi_2nd_prove_pic = CDN_DOMAIN . $task_detail->huobi_2nd_prove_pic;
                            ?>
                            <img data-input-name="huobi_2nd_prove_pic" class="image-upload" src="<?php echo $huobi_2nd_prove_pic; ?>">
                            <input type="hidden" name="huobi_2nd_prove_pic" value="<?php echo $task_detail->huobi_2nd_prove_pic; ?>">
                        </div>
                        <p class="help-block">货比第二家</p>
                        <p class="help-block">(浏览≥1分钟)</p>
                    </div>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $huobi_3rd_prove_pic = $dft_pic;
                                !empty($task_detail->huobi_3rd_prove_pic) && $huobi_3rd_prove_pic = CDN_DOMAIN . $task_detail->huobi_3rd_prove_pic;
                            ?>
                            <img data-input-name="huobi_3rd_prove_pic" class="image-upload" src="<?php echo $huobi_3rd_prove_pic; ?>">
                            <input type="hidden" name="huobi_3rd_prove_pic" value="<?php echo $task_detail->huobi_3rd_prove_pic; ?>">
                        </div>
                        <p class="help-block">货比第三家</p>
                        <p class="help-block">(浏览≥1分钟)</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">店铺浏览截图</label>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $zhubaobei_prove_pic = $dft_pic;
                                !empty($task_detail->zhubaobei_prove_pic) && $zhubaobei_prove_pic = CDN_DOMAIN . $task_detail->zhubaobei_prove_pic;
                            ?>
                            <img data-input-name="zhubaobei_prove_pic" class="image-upload" src="<?php echo $zhubaobei_prove_pic; ?>">
                            <input type="hidden" name="zhubaobei_prove_pic" value="<?php echo $task_detail->zhubaobei_prove_pic; ?>">
                        </div>
                        <p class="help-block">主宝贝截图</p>
                        <p class="help-block">(浏览≥5分钟)</p>
                    </div>
                    <div class="col-md-2">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $fubaobei_prove_pic = $dft_pic;
                                !empty($task_detail->fubaobei_prove_pic) && $fubaobei_prove_pic = CDN_DOMAIN . $task_detail->fubaobei_prove_pic;
                            ?>
                            <img data-input-name="fubaobei_prove_pic" class="image-upload" src="<?php echo $fubaobei_prove_pic; ?>">
                            <input type="hidden" name="fubaobei_prove_pic" value="<?php echo $task_detail->fubaobei_prove_pic; ?>">
                        </div>
                        <p class="help-block">副宝贝截图</p>
                        <p class="help-block">(浏览≥2分钟)</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">支付宝订单付款截图</label>
                    <div class="col-md-4">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $fukuan_prove_pic = $dft_pic;
                                !empty($task_detail->fukuan_prove_pic) && $fukuan_prove_pic = CDN_DOMAIN . $task_detail->fukuan_prove_pic;
                            ?>
                            <img data-input-name="fukuan_prove_pic" class="image-upload" src="<?php echo $fukuan_prove_pic; ?>">
                            <input type="hidden" name="fukuan_prove_pic" value="<?php echo $task_detail->fukuan_prove_pic; ?>">
                        </div>
                        <p class="help-block">付款截图（付款成功后订单详情）</p>
                        <p class="help-block">(浏览≥5分钟)</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">好评截图</label>
                    <div class="col-md-4">
                        <div class="tieyu-icon image-upload-container">
                            <?php
                                $haoping_prove_pic = $dft_pic;
                                !empty($task_detail->haoping_prove_pic) && $haoping_prove_pic = CDN_DOMAIN . $task_detail->haoping_prove_pic;
                            ?>
                            <img data-input-name="haoping_prove_pic" class="image-upload" src="<?php echo $haoping_prove_pic; ?>">
                            <input type="hidden" name="haoping_prove_pic" value="<?php echo $task_detail->haoping_prove_pic; ?>">
                        </div>
                        <p class="help-block3 font-red">好评截图（收货以及好评必须在快递签收以后）</p>
                        <p class="help-bloc3k font-green">(浏览≥5分钟)</p>
                    </div>
                </div>

                <div id="error_display" class="alert"></div>
                <div style="text-align:center;margin-top:50px;">
                    <button class="btn btn-lg btn-primary btn-update-member-commit" data-url="<?php echo base_url('seller_reject_manage/update_order_info_handle'); ?>"  data-target="<?php echo base_url('seller_reject_manage/index'); ?>">提交修改</button>
                </div>
            <?php endif;?>
        </div>
    </div>
</form>
<script>
    $(function () {
        $(".btn-update-member-commit").click(function (e) {
            e.preventDefault();
            var form_data = $('.form-update-member').formToJSON();
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
                        goto_url(that.data('target') + "?<?php echo $params_get?>");
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });
</script>