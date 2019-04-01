<h1 class="page-header">商家店铺管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>会员ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="会员ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>店铺名</label>
        <input type="text" class="form-control filter-control" name="shop_name" placeholder="店铺名" value="<?php if (!empty($shop_name)) echo $shop_name; ?>">
    </div>
    <div class="form-group">
        <label>掌柜ID</label>
        <input type="text" class="form-control filter-control" name="shop_ww" placeholder="掌柜ID" value="<?php if (!empty($shop_ww)) echo $shop_ww; ?>">
    </div>
    <div class="form-group">
        <label>开始时间</label>
        <input type="text" class="form-control filter-control format_date" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
    </div>
    <div class="form-group">
        <label>结束时间</label>
        <input type="text" class="form-control filter-control format_date" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
    </div>
    <div class="form-group">
        <label>状态</label>
        <select name="status" class="form-control">
            <option value="">全部</option>
            <?php
            $task_array = array(
                STATUS_PASSED => '正常',
                STATUS_CHECKING => '审核中',
                STATUS_FAILED => '审核失败',
                STATUS_BAN => '黑名单'
            );

            foreach ($task_array as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($status) && $k == $status) {
                    echo ' selected';
                }
                echo '>' . $v . '</option>';
            }
            ?>
        </select>
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>会员ID</th>
                <th>店铺类型</th>
                <th>店铺名</th>
                <th>掌柜ID</th>
                <th>店铺地区</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->seller_id); ?></td>
                    <td>
                        <?php
                        switch ($v->shop_type) {
                            case SHOP_TYPE_TAOBAO:
                                echo '淘宝店';
                                break;
                            case SHOP_TYPE_TMALL:
                                echo '天猫店';
                                break;
                            case SHOP_TYPE_PINDUODUO:
                                echo '拼多多';
                                break;
                            default:
                                echo '未知';
                                break;
                        }
                        ?>
                    </td>
                    <td><?php echo $v->shop_name; ?></td>
                    <td><?php echo $v->shop_ww; ?></td>
                    <td><?php echo $v->shop_province . $v->shop_city . $v->shop_county . $v->shop_address; ?></td>
                    <td>
                        <?php
                        switch ($v->status) {
                            case STATUS_PASSED:
                                echo '正常';
                                break;
                            case STATUS_CHECKING:
                                echo '审核中';
                                break;
                            case STATUS_FAILED:
                                echo '审核失败';
                                break;
                            case STATUS_BAN:
                                echo '黑名单';
                                break;
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($v->status == STATUS_BAN): ?>
                            <a href="javascript:;" class="btn btn-sm btn-default btn-free-blacklist" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('shop_manage/operation_handle'); ?>">解除黑名单</a>
                        <?php else: ?>
                            <a href="javascript:;" class="btn btn-sm btn-danger btn-set-blacklist" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('shop_manage/operation_handle'); ?>">拉入黑名单</a>
                        <?php endif; ?>
                            <a class="btn btn-sm btn-primary" href="<?php echo base_url('shop_manage/edit?id=' . $v->id); ?>">修改店铺信息</a>
                            <a href="javascript:;"  data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('shop_manage/change_shop'); ?>" data-old_seller_id="<?php echo $v->seller_id; ?>" data-conclusion="HB"  class="btn btn-sm btn-danger btn-check">店铺换绑</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('fragment_pagination'); ?>
<?php endif; ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".btn-set-blacklist").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定将此账号列入黑名单吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'set_black',
                        shop_id: that.data('id')
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });

        $(".btn-free-blacklist").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定解除此账号的黑名单吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'unset_black',
                        shop_id: that.data('id')
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });

        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });

        $(".fancybox").fancybox();
    });
</script>
<script type="text/javascript">
    function check(form) {
        if(form.seller_id.value=='') {
            alert("请输入新的商家ID!");
            form.seller_id.focus();
            return false;
        }
        return true;
    }
</script>
<script type="text/javascript">
    $('.btn-check').click(function (e) {

        e.preventDefault();
        var that = $(this);

        that.addClass('disabled');
        that.attr("disabled", true);
            // 店铺换绑
            if ($(this).data('conclusion') == 'HB') {
                var reject_reason = prompt("请输入要绑定的新商家ID！");
                if (reject_reason == null || reject_reason == '') {
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
                    reject_reason: reject_reason
                },
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        alert('店铺换绑成功！');
                        location.reload();
                    } else {
                        alert('店铺换绑失败！');
                        location.reload();
                    }
                }
            );

    });

    $(".fancybox").fancybox();
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>



