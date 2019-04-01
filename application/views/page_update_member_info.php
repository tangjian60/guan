
<script type="text/javascript">
$(function(){
    window.s = ["province", "city", "county"];
    window.def_select_name = [
        "<?php echo $member_data[0]->bank_province ?>","<?php echo $member_data[0]->bank_city ?>",
        "<?php echo $member_data[0]->bank_county ?>"
    ];
    _init_area();
});

</script>
<h1 class="page-header">会员信息修改</h1>
<form class="form-horizontal form-update-member" action="" method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" style="color:red;">警告！高危操作！该操作直接修改会员已经审核通过的信息</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <?php if(empty($member_data)): ?>
                <div class="panel-heading">
                    <h3 class="panel-title" style="color:red;">暂无该会员审核通过的实名认证信息</h3>
                </div>
            <?php else: ?>
                <input type="hidden" name="member_id" value="<?php echo $member_data[0]->id; ?>">
                <div class="form-group">
                    <label class="col-md-3 control-label">真实姓名</label>
                    <div class="col-md-8">
                        <?php $array = array('2','3','6'); $readonly = ''; ?>
                        <?php if (!in_array($admin_id,$array)) {$readonly = 'readonly';} ?>
                        <input class="form-control" type="text" name="true_name" value="<?php echo $member_data[0]->true_name; ?>" <?php echo $readonly?>>
                        <input class="form-control" type="hidden" name="user_id" value="<?php echo $member_data[0]->user_id; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">会员身份证</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="id_card_num" value="<?php echo $member_data[0]->id_card_num; ?>" <?php echo $readonly?>>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">会员QQ号码</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="qq_num" value="<?php echo $member_data[0]->qq_num; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">会员银行卡</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="bank_card_num" value="<?php echo $member_data[0]->bank_card_num; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">开户行-名称</label>
                    <div class="col-md-8">
                        <!-- <input class="form-control" type="text" name="bank_name" value="<?php echo $member_data[0]->bank_name; ?>"> -->
                        <?php
                            $banks_list = [
                                '招商银行',
                                '中国工商银行',
                                '中国农业银行',
                                '中国银行',
                                '中国建设银行',
                                '交通银行',
                                '中信银行',
                                '光大银行',
                                '华夏银行',
                                '民生银行',
                                '广发银行',
                                '平安银行',
                                '兴业银行',
                                '上海浦东发展银行',
                                '北京银行',
                                '南京银行',
                                '江苏银行',
                                '宁波银行',
                                '上海银行',
                                '杭州银行',
                                '农村商业银行',
                            ];

                            if (in_array($member_data[0]->bank_name, $banks_list)) {
                                echo '<select name="bank_name" class="form-control">';
                                foreach ($banks_list as $bank) {
                                    echo sprintf("<option value='%s' %s>%s</option>", $bank, ($member_data[0]->bank_name == $bank)?'selected':'', $bank);
                                }
                                echo '</select>';
                            }else{
                                echo $member_data[0]->bank_name;
                            }
                        ?>
                    </div>
                </div>

                <div class="form-group page" data-page="bind-user-update">
                    <label class="col-md-3 control-label">开户行-地区</label>
                    <div class="col-md-2 col-sm-2">
                        <select class="form-control" name="bank_province" id="province">
                            <?php if($member_data[0]->bank_province) { ?>
                                <option value="<?php echo $member_data[0]->bank_province ?>"><?php echo $member_data[0]->bank_province ?></option>
                                <?php
                                    } else {
                                ?>
                            <option value="">请选择省份</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-2">
                        <select class="form-control" name="bank_city" id="city">
                            <?php if($member_data[0]->bank_city) { ?>
                                <option value="<?php echo $member_data[0]->bank_city ?>"><?php echo $member_data[0]->bank_city ?></option>
                                <?php
                            } else {
                            ?>
                            <option value="">请选择城市</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-2">
                        <select class="form-control" name="bank_county" id="county">
                            <?php if($member_data[0]->bank_county) { ?>
                                <option value="<?php echo $member_data[0]->bank_county ?>"><?php echo $member_data[0]->bank_county ?></option>
                                <?php
                            } else {
                            ?>
                            <option value="">请选择地区</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label">开户行-支行</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="bank_branch" value="<?php echo $member_data[0]->bank_branch; ?>">
                    </div>
                </div>
                <div id="error_display" class="alert"></div>
                <div style="text-align:center;margin-top:50px;">
                    <button class="btn btn-lg btn-primary btn-update-member-commit" data-url="<?php echo base_url('member_manage/update_info'); ?>"  data-target="<?php echo base_url('member_manage/index'); ?>">提交</button>
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