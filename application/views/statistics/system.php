<h1 class="page-header">系统余额统计</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <div class="form-group">
        <label>查询日期</label>
        <input type="text" class="form-control filter-control format_date" name="checkDate" id="checkDate" value="<?php echo !empty($checkDate) ? $checkDate : date('Y-m-d'); ?>"> -
        <input type="text" class="form-control filter-control format_date" name="checkDate2" id="checkDate2" value="<?php echo !empty($checkDate2) ? $checkDate2 : date('Y-m-d'); ?>">
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>日期</th>
                <th>买手佣金</th>
                <th>买手本金</th>
                <th>商家余额</th>
                <th>代理金币</th>
                <th>补单余额</th>
                <th>佣金余额</th>
                <th>流量余额</th>
                <th>提现余额</th>
                <th>总额</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo $v['date']; ?></td>
                    <td><?php echo $v['sumbalanceCommission']; ?></td>
                    <td><?php echo $v['sumbalanceCapital']; ?></td>
                    <td><?php echo $v['sumbalance']; ?></td>
                    <td><?php echo $v['agent']; ?></td>
                    <td><?php echo $v['sumauditing']; ?></td>
                    <td><?php echo $v['sumfactCommission']; ?></td>
                    <td><?php echo $v['sumCommission']; ?></td>
                    <td><?php echo $v['sumwithdrawBalance']; ?></td>
                    <td><?php echo $v['total']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();

            var checkDate = $('#checkDate').val();
            var checkDate2 = $('#checkDate2').val();
            if (checkDate > checkDate2) alert('查询日期有误，请检查！');
            if (getDateDiff(checkDate, checkDate2) > 29) {
                alert('查询日期间隔不能超过30天！');return;
            }

            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".format_date").datetimepicker({
            minView: 'month',
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            endDate: '<?php echo date('Y-m-d')?>',
            autoclose: true
        });
    });


    function getDateDiff(startDate, endDate)
    {
        var startTime = new Date(Date.parse(startDate.replace(/-/g,   "/"))).getTime();
        var endTime = new Date(Date.parse(endDate.replace(/-/g,   "/"))).getTime();
        var dates = Math.abs((startTime - endTime))/(1000*60*60*24);
        return  dates;
    }

</script>