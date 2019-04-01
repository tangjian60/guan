<h1 class="page-header">运营统计</h1>
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
                <th>发布任务数</th>
                <th>接单任务数</th>
                <th>未被接单数</th>
                <th>接单买手数</th>
                <th>接单买号数</th>
                <th>发布商家数</th>
                <th>发布店铺数</th>
                <th>新注册买手数</th>
                <th>当天注册并接单的买手数</th>
                <th>新注册商家数</th>
                <th>当天注册并放单的商家数</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo $v['date']; ?></td>
                    <td><?php echo $v['sumSubtask']; ?></td>
                    <td><?php echo $v['sumOrdertaking']; ?></td>
                    <td><?php echo $v['sumUnanswered']; ?></td>
                    <td><?php echo $v['sumBuyer']; ?></td>
                    <td><?php echo $v['sumFlapper']; ?></td>
                    <td><?php echo $v['sumSeller']; ?></td>
                    <td><?php echo $v['sumShop']; ?></td>
                    <td><?php echo $v['sumNewbuyer']; ?></td>
                    <td><?php echo $v['sumNewbuyers']; ?></td>
                    <td><?php echo $v['sumNewseller']; ?></td>
                    <td><?php echo $v['sumNewsellers']; ?></td>
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