<h1 class="page-header"><?php echo $PageTitle; echo ' (',$date,')'?></h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <input type="hidden" id="date" name="date" value="<?php if (!empty($date)) echo $date; ?>">
    <input type="hidden" id="type" name="type" value="<?php if (!empty($type)) echo $type; ?>">
    <input type="hidden" id="today" name="today" value="<?php if (!empty($today)) echo $today; ?>">
<!--    <div class="form-group">-->
<!--        <label>关键字</label>-->
<!--        <input type="text" class="form-control filter-control" name="keywords" placeholder="关键字" value="--><?php //if (!empty($keywords)) echo $keywords; ?><!--">-->
<!--    </div>-->
<!--    <div class="form-group">-->
<!--        <label>开始时间</label>-->
<!--        <input type="text" class="form-control filter-control format_date" name="start_time" value="--><?php //if (!empty($start_time)) echo $start_time; ?><!--">-->
<!--    </div>-->
<!--    <div class="form-group">-->
<!--        <label>结束时间</label>-->
<!--        <input type="text" class="form-control filter-control format_date" name="end_time" value="--><?php //if (!empty($end_time)) echo $end_time; ?><!--">-->
<!--    </div>-->
<!--    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>-->
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>手机账号</th>
                <th><?php echo $title?>金额</th>
                <th>操作时间</th>
                <th>操作人</th>
                <th>账单备注</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo isset($v['id']) ? $v['id'] : ++$k; ?></td>
                    <td><?php echo $v['user_name']; ?></td>
                    <td><?php echo $v['amount']; ?></td>
                    <td><?php echo $v['op_time']; ?></td>
                    <td><?php echo $v['op_man']; ?></td>
                    <td><?php echo $v['memo']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $this->load->view('fragment_pagination'); ?>

<?php endif; ?>