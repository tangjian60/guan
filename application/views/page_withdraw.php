<h1 class="page-header">会员提现管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <input type="hidden" id="excel" name="excel" value="0" />
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
        <label>真实姓名</label>
        <input type="text" class="form-control filter-control" name="name" value="<?php if(!empty($name)) echo $name;?>">
    </div>
    <div class="form-group">
        <label>充值状态</label>
        <select class="form-control filter-control" name="status">
            <?php
            $options = array(
                STATUS_CHECKING => '提现处理中',
                STATUS_REMITING => '打款处理中',
                //STATUS_REMITED => '已打款',
                STATUS_PASSED => '提现成功',
                STATUS_CANCELING => '待退款',
                STATUS_FAILED => '提现失败',

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

<?php if ($status == STATUS_PASSED):?>
<form id="form-import" class="form-inline" action="<?php echo base_url('withdraw/import')?>" method="GET" style="background-color:#fefefe;line-height:65px;">
    <div class="form-group">
        <input type="file" name="file_upload" id="file-upload" />
        <p><a href="javascript:;" class="btn btn-primary btn-import" role="button">上传退票</a>
            (仅支持xls,xlsx格式的excel文件)
        </p>
    </div>
</form>
<?php endif?>

<!-- 模态框（Modal） -->
<?php $this->load->view('reject_modal')?>

<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>提现会员ID</th>
                <th>提现用户名</th>
                <th>提现金额</th>
                <th>真实姓名</th>
                <th>开户行</th>
                <th>卡号</th>
                <th>支行</th>
                <th>申请提现时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo encode_id($v->user_id); ?></td>
                    <td><?php echo $v->user_name; ?></td>
                    <td><?php echo $v->amount; ?></td>
                    <td><?php echo $v->real_name; ?></td>
                    <td><?php echo $v->bank_name; ?></td>
                    <td><?php echo $v->bank_card_num; ?></td>
                    <td><?php echo $v->bank_address . $v->bank_branch; ?></td>
                    <td><?php echo $v->create_time; ?></td>
                    <td>
                        <?php echo $options[$v->status];?>
                    </td>
                    <td>
                        <?php if ($v->status == STATUS_CHECKING): ?>
                            <a href="javascript:;" class="btn btn-sm btn-success btn-accept" data-id="<?php echo $v->id; ?>" data-tixian_type="<?php echo $v->tixian_type;?>" data-url="<?php echo base_url('withdraw/accept_handle'); ?>">通过</a>
                            <button class="btn btn-sm btn-danger reject" data-id="<?php echo $v->id?>" data-url="<?php echo base_url('withdraw/reject_handle');?>" data-toggle="modal" data-target="#myModal">拒绝</button>
                        <?php elseif ($v->status == STATUS_REMITING):?>
                            <a href="javascript:;" class="btn btn-sm btn-success btn-approve" data-id="<?php echo $v->id; ?>" data-tixian_type="<?php echo $v->tixian_type;?>" data-url="<?php echo base_url('withdraw/operation_handle'); ?>">打款</a>
                        <?php elseif ($v->status == STATUS_PASSED):?>
                            <a href="javascript:;" class="btn btn-sm btn-success btn-app" data-id="<?php echo $v->id; ?>" data-tixian_type="<?php echo $v->tixian_type;?>" data-url="<?php echo base_url('withdraw/return_ticket'); ?>">退票</a>
                        <?php elseif ($v->status == STATUS_CANCELING):?>
                            <a href="javascript:;" class="btn btn-sm btn-success btn-approve" data-id="<?php echo $v->id; ?>" data-tixian_type="<?php echo $v->tixian_type;?>" data-url="<?php echo base_url('withdraw/operation_handle'); ?>">打款</a>
                            <button class="btn btn-sm btn-danger reject" data-id="<?php echo $v->id?>" data-url="<?php echo base_url('withdraw/reject_handle');?>" data-toggle="modal" data-target="#myModal">拒绝</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($status == STATUS_REMITING || $status == STATUS_CANCELING):?>
                <tr> <td> <button data-status=<?php echo $status;?> class="btn btn-sm btn-success btn-approve2" >全部打款</button> </td> </tr>
            <?php endif?>
            <?php if ($status == STATUS_CHECKING):?>
                <tr> <td> <button class="btn btn-sm btn-success btn-excel" >导出EXCEL</button> </td> </tr>
            <?php endif?>
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
            $('#excel').val(0);
            $('#form-filter').submit();
        });

        $('.btn-excel').click(function(e){
            var btn_excel_data = $('#form-filter').formToJSON();
            if (!btn_excel_data.start_time || !btn_excel_data.end_time) {
                alert('由于条目较多，请先选择时间段！');
                return;
            }

            var that = $(this);
            e.preventDefault();
            that.addClass('disabled');
            that.attr("disabled", true);

            ajax_request(
                "<?php echo base_url('withdraw/doDumpCheck');?>",
                {
                    act: 'dumpExcel',
                },
                function (e) {
                    that.removeClass('disabled');
                    that.attr("disabled", false);
                    if (e.code == CODE_SUCCESS) {
                        $('#i_page').val(1);
                        $('#excel').val(1);
                        $('#form-filter').submit();
                    } else {
                        alert(e.msg);
                    }
                });
        });

        $(".btn-approve").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确认本提现申请打款吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'withdraw_approve',
                        withdraw_id: that.data('id')
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

        $(".btn-accept").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确认通过提现申请吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        withdraw_id: that.data('id')
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

        $(".btn-app").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确认申请退票吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'withdraw_approve',
                        withdraw_id: that.data('id')
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

        $(".btn-approve2").click(function(e){
            var that = $(this);
            that.addClass('disabled');
            that.attr("disabled", true);
            ajax_request(
                "<?php echo base_url('withdraw/batch_approve');?>",
                {
                    act: 'withdraw_approve',
                    status: that.data('status'),
                }
                ,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        location.reload();
                    } else {
                        alert(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });

        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });

        $('.btn-import').click(function(e){
            var formData = new FormData();
            formData.append("file_upload", document.getElementById("file-upload").files[0]);
            //that.addClass('disabled');
            //that.attr("disabled", true);

            $.ajax({
                url: "<?php echo base_url('withdraw/import')?>",
                type: "POST",
                data: formData,
                dataType: "json",
                cache: false,
                /**
                 *必须false才会自动加上正确的Content-Type
                 */
                contentType: false,
                /**
                 * 必须false才会避开jQuery对 formdata 的默认处理
                 * XMLHttpRequest会对 formdata 进行正确的处理
                 */
                processData: false,
                success: function (res) {
                    if (res.code == CODE_SUCCESS) {
                        location.reload(true);
                    } else {
                        alert(res.msg);
                        //that.removeClass('disabled');
                        //that.attr("disabled", false);
                    }
                },
                error: function () {
                    alert("上传失败！");
                }
            });
        });
    });
</script>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>reject-modal.js?v=1903201439"></script>


