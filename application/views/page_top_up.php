<h1 class="page-header">商家充值管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>会员ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="会员ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>会员名</label>
        <input type="text" class="form-control filter-control" name="member_name" placeholder="会员名" value="<?php if (!empty($member_name)) echo $member_name; ?>">
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
        <label>充值状态</label>
        <select class="form-control filter-control" name="status">
            <option value="">全部</option>
            <?php
            $options = array(
                STATUS_PASSED => '充值成功',
                STATUS_CHECKING => '充值处理中',
                STATUS_FAILED => '充值失败'
            );

            foreach ($options as $k => $v) {
                echo '<option value="' . $k . '"';
                if (isset($status) && $status != '' && $k == $status) {
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
                <th>充值商家ID</th>
                <th>充值商家用户名</th>
                <th>汇款银行卡</th>
                <th>商家汇款截图</th>
                <th>充值人姓名</th>
                <th>充值金额</th>
                <th>充值时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->seller_id); ?></td>
                    <td><?php echo $v->seller_name; ?></td>
                    <td><?php echo $v->huikuan_bank_name; ?></td>
                    <td>
                        <?php
                        if ($v->chongzhi_img == "") {
                            echo $v->zhuanru_bank_name;
                        } else if ($v->chongzhi_img != ""){
                            echo '<a href="'.CDN_DOMAIN . $v->chongzhi_img.'" class="fancybox"><img class="item-pic-box" src="'.CDN_DOMAIN . $v->chongzhi_img.'"></a>';
                        }
                        ?>
                    </td>
                    <td><?php echo $v->transfer_person; ?></td>
                    <td><?php echo $v->transfer_amount; ?></td>
                    <td><?php echo $v->create_time; ?></td>
                    <td>
                        <?php
                        switch ($v->status) {
                            case STATUS_PASSED:
                                echo '充值成功';
                                break;
                            case STATUS_CHECKING:
                                echo '充值处理中';
                                break;
                            case STATUS_FAILED:
                                echo '充值失败';
                                break;
                            default:
                                echo '未知';
                                break;
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($v->status == STATUS_CHECKING): ?>
                            <a href="javascript:;" class="btn btn-sm btn-success btn-approve" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('top_up/operation_handle'); ?>">通过</a>
                            <a href="javascript:;" class="btn btn-sm btn-danger btn-reject" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('top_up/operation_handle'); ?>">拒绝</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php $this->load->view('fragment_pagination'); ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".btn-approve").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定已经收到充值？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'top_up_approve',
                        top_up_id: that.data('id')
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

        $(".btn-reject").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定拒绝此充值申请？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'top_up_reject',
                        top_up_id: that.data('id')
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
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>