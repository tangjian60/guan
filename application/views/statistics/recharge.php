<h1 class="page-header">充值统计</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <div class="form-group">
        <label>查询日期</label>
        <input type="text" class="form-control filter-control format_date" name="checkDate" id="checkDate" value="<?php echo !empty($checkDate) ? $checkDate : date('Y-m-d', strtotime("-1 day")); ?>"> -
        <input type="text" class="form-control filter-control format_date" name="checkDate2" id="checkDate2" value="<?php echo !empty($checkDate2) ? $checkDate2 : date('Y-m-d', strtotime("-1 day")); ?>">
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
    <a class="btn btn-success btn-today" href="<?php echo base_url('promote_statistics/recharge?today=1&checkDate='.$checkDate.'&checkDate2='.$checkDate2.''); ?>">今日实时</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>日期</th>
                <th>客服充值</th>
                <th>校正充值</th>
                <th>总充值</th>
                <th>总提现</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td><?php echo $v['date']; ?></td>
                    <td>
                        <a target="_blank" href="<?php echo base_url('promote_statistics/recharge_detail?type=1&date=' . $v['date'] . '&today=' . $today); ?>" data-type="1" data-date="<?php echo $v['date']?>" data-url="<?php echo base_url('promote_statistics/ajaxGetRechargeDetailDB'); ?>" class="check-detai3l"><?php echo $v['sumRecharge']; ?></a>
                    </td>

                    <?php if ($v['sumRechargeCorrect'] > 0):; ?>
                        <td style="color: #FF0000"><a target="_blank" href="<?php echo base_url('promote_statistics/recharge_detail?type=2&date=' . $v['date'] . '&today=' . $today); ?>" data-type="2" data-date="<?php echo $v['date']?>" data-url="<?php echo base_url('promote_statistics/ajaxGetRechargeDetailDB'); ?>" class="check-deta3il"><?php echo $v['sumRechargeCorrect']; ?></a></td>
                    <?php else:?>
                        <td style="color: #008000"><a target="_blank" href="<?php echo base_url('promote_statistics/recharge_detail?type=2&date=' . $v['date'] . '&today=' . $today); ?>" data-type="2" data-date="<?php echo $v['date']?>" data-url="<?php echo base_url('promote_statistics/ajaxGetRechargeDetailDB'); ?>" class="check-deta3il"><?php echo $v['sumRechargeCorrect']; ?></a></td>
                    <?php endif; ?>

                    <td><?php echo $v['sumRechargeTotal']; ?></td>
                    <td><a target="_blank" href="<?php echo base_url('promote_statistics/recharge_detail?type=3&date=' . $v['date'] . '&today=' . $today); ?>" data-type="3" data-date="<?php echo $v['date']?>" data-url="<?php echo base_url('promote_statistics/ajaxGetRechargeDetailDB'); ?>" class="check-deta3il"><?php echo $v['sumWithdrawTotal']; ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ui-dialog -->
    <div id="dialog_simple" title="详情"></div>

<?php endif; ?>
<script>
    $(function () {
        // 初始化简单对话框
        $('#dialog_simple').dialog({
            autoOpen: false,
            modal: true,
            width: 800,
            height: 800,
            buttons: {
                "关 闭": function () {
                    $(this).dialog("close");
                    return;
                }
            }
        });

        $('.check-detail').click(function (e) {
            e.preventDefault();

            var today = '<?php echo $today?>' ? '1' : '2';
            ajax_request(
                $(this).data('url'),
                {
                    type: $(this).data('type'),
                    date: $(this).data('date'),
                    today: today,
                },
                function (res) {
                    if (res.code == CODE_SUCCESS) {
                        //console.log(res.data);
                        if (res.msg == '1' || res.msg == '2') {
                            var thead_html = '<th>序号</th><th>手机账号</th><th>充值金额</th><th>操作时间</th><th>操作人</th><th>账单备注</th>';
                        } else {
                            var thead_html = '<th>序号</th><th>手机账号</th><th>提现金额</th><th>操作时间</th><th>操作人</th><th>账单备注</th>';
                        }

                        var html = '<table class="table table-striped">';
                        html += '<thead><tr>';
                        html += thead_html;
                        html += '</tr></thead><tbody>';

                        $.each(res.data, function(i,val){
                            html += '<tr>';
                            html += '<td>' + (i+1) + '</td>';
                            html += '<td>' + val.user_name + '</td>';
                            html += '<td>' + val.amount + '</td>';
                            html += '<td>' + val.op_time + '</td>';
                            html += '<td>' + val.op_man + '</td>';
                            html += '<td>' + val.memo + '</td>';
                            html += '</tr>';

                        });
                        html += '</tbody></table>';

                        $('#dialog_simple').html(html);
                        $('#dialog_simple').dialog('open');
                    } else {
                        return;
                    }
                });
        });

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
            endDate: '<?php echo date('Y-m-d', strtotime("-1 day"))?>',
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