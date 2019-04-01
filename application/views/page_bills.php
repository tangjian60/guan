<h1 class="page-header">账单</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>会员ID</label>
        <input id="member_id" type="text" class="form-control filter-control" name="member_id" placeholder="会员ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
    <div class="form-group">
        <label>订单号</label>
        <input type="text" class="form-control filter-control" name="order_id" placeholder="订单号" value="<?php if (!empty($order_id)) echo $order_id; ?>">
    </div>
    <div class="form-group">
        <label>金额范围</label>
        <input type="text" class="form-control filter-control" name="start_amount" placeholder="金额大于" value="<?php if (isset($start_amount)) echo $start_amount; ?>">
        ——
        <input type="text" class="form-control filter-control" name="end_amount" placeholder="金额小于" value="<?php if (isset($end_amount)) echo $end_amount; ?>">
    </div>
    <div class="form-group">
        <label>时间范围</label>
        <input type="text" class="form-control filter-control format_date" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
        ——
        <input type="text" class="form-control filter-control format_date" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
    </div>
    <div class="form-group">
        <label>账单类型</label>
        <select name="bill_type" class="form-control filter-control">
            <option value="">全部</option>
            <?php foreach (Paycore::get_bill_type() as $key => $value) {
                echo '<option value="' . $key . '"';
                if (isset($bill_type) && $key == $bill_type) {
                    echo ' selected';
                }
                echo '>' . $value . '</option>';
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
                <th>交易时间</th>
                <th>会员ID</th>
                <th>账单类型</th>
                <th>备注</th>
                <th>金额</th>
                <th>余额</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo $v->gmt_pay; ?></td>
                    <td><?php echo encode_id($v->user_id); ?></td>
                    <td><?php echo Paycore::get_bill_type_name($v->bill_type); ?></td>
                    <td><?php echo $v->memo; ?></td>
                    <td><?php echo $v->amount; ?></td>
                    <td><?php echo $v->balance; ?></td>
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

            if ($('#member_id').val() == "") {
                alert('会员编号必须填写');
                return;
            }

            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });
    });
</script>