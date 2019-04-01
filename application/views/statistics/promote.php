<h1 class="page-header">推广统计</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>会员ID</label>
        <input type="text" class="form-control filter-control" name="member_id" placeholder="会员ID" value="<?php if (!empty($member_id)) echo $member_id; ?>">
    </div>
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
<!--                <th>会员ID</th>-->
                <th>会员名</th>
                <th>下线注册量</th>
                <th>实名审核通过量</th>
                <th>淘宝账号审核通过量</th>
                <th>首单完成总量</th>
                <th>下线任务完成总量</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo $v['date']; ?></td>
                    <td><?php echo $v['userName']; ?></td>
                    <td><?php echo $v['promoteCnt']; ?></td>
                    <td><?php echo $v['certificationPassCnt']; ?></td>
                    <td><?php echo $v['tbPassCnt']; ?></td>
                    <td><?php echo $v['firstRewardCntTotal']; ?></td>
                    <td><?php echo $v['tasksDone']; ?></td>
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