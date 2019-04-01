<h1 class="page-header">常见功能修改管理</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!--<a class="btn btn-lg btn-success" href="<?php /*echo base_url('assemble/buyer_bank'); */?>">买手银行卡管理</a>-->
                <a  style="margin-left:300px;" class="btn btn-lg btn-success" href="<?php echo base_url('assemble/bind_info'); ?>">买手帐号信息管理</a>
                <a  style="margin-left:300px;"  data-url="<?php echo base_url('assemble/new_relation'); ?>" data-conclusion="TG" class="btn btn-lg btn-success btn-check" href="javascript:;">新增推广关系</a>
                <!--<a  href="javascript:;"  data-id="<?php /*; */?>" data-url="<?php /*echo base_url('shop_manage/change_shop'); */?>" data-old_seller_id="<?php /*; */?>" data-conclusion="HB"  class="btn btn-sm btn-danger btn-check">新增推广关系</a>-->
               <!-- <a  style="margin-left:300px;" class="btn btn-lg btn-success" href="#">新建管理模块</a>-->
            </div>
        </div>

      <!--  <div class="panel panel-default">
            <div class="panel-heading">
                <a  style="margin-left:300px;" class="btn btn-lg btn-success" href="#">新建管理模块</a>
                <a  style="margin-left:300px;" class="btn btn-lg btn-success" href="#">新建管理模块</a>
                <a  style="margin-left:300px;" class="btn btn-lg btn-success" href="#">新建管理模块</a>
            </div>
        </div>-->

    </div>
</div>

<script type="text/javascript">
    $('.btn-check').click(function (e) {

        e.preventDefault();
        var that = $(this);

        that.addClass('disabled');
        that.attr("disabled", true);
        // 店铺换绑
        if ($(this).data('conclusion') == 'TG') {
            var owner_id = prompt("请输入推荐人ID！");
            var promote_id = prompt("请输入被推荐人ID！");
            if (owner_id == '' || promote_id == '') {
                that.removeClass('disabled');
                that.attr("disabled", false);
                return;
            }
        }
        ajax_request(
            $(this).data('url'),
            {
                old_seller_id: $(this).data('old_seller_id'),
                id: $(this).data('id'),
                conclusion: $(this).data('conclusion'),
                owner_id : owner_id,
                promote_id : promote_id



            },
            function (e) {
                if (e.code == CODE_SUCCESS) {
                    alert('新增推广关系成功！');
                    location.reload();
                } else {
                    alert('新增推广关系失败！');
                    location.reload();

                   /* show_error_message(e.msg);
                    that.removeClass('disabled');
                    that.attr("disabled", false);*/
                }
            }
        );

    });

    $(".fancybox").fancybox();
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
