<h1 class="page-header">买手银行卡管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
    <div class="form-group">
        <label>买手ID: </label>
        <input type="text" class="form-control filter-control" name="user_id" value="<?php echo isset($user_id) ? $user_id:'' ?>" placeholder="买手ID"/>
    </div>
    <div class="form-group">
        <label>买手姓名: </label>
        <input type="text" class="form-control filter-control" name="true_name" value="<?php echo isset($true_name) ? $true_name:'' ?>" placeholder="买手姓名"/>
    </div>
    <div class="form-group">
        <label>银行卡号: </label>
        <input type="text" class="form-control filter-control" name="bank_card_num" value="<?php echo isset($bank_card_num) ? $bank_card_num: ''?>" placeholder="银行卡号"/>
    </div>
    <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
    <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary btn-clear">清理</a>
</form>


<div class="form-group">
    <label class="col-md-9 control-label"></label>
    <div class="col-md-9">
        <div id="error_display" class="alert"></div>
    </div>
</div>

<div class="bootstrap-admin-panel-content">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>序号</th>
            <th>买手ID</th>
            <th>买手姓名</th>
            <th>身份证号</th>
            <th>银行卡号</th>
            <th>开户行银行</th>
            <th>开户支行</th>
            <th>提交认证时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($data)): ?>
            <tr>
                <td colspan="3">温馨提示：请输入搜索条件！</td>
            </tr>
        <?php else: ?>
        <?php foreach ($data as $k=>$v): ?>
            <tr>
                <td><?php echo ++$k; ?></td>
                <td><?php echo encode_id($v->user_id); ?></td>
                <td><?php echo $v->true_name; ?></td>
                <td><?php echo $v->id_card_num; ?></td>
                <td><?php echo $v->bank_card_num?></td>
                <td><?php echo $v->bank_name?></td>
                <td><?php echo $v->bank_branch?></td>
                <td><?php echo $v->gmt_create?></td>
                <td>
                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('assemble/edit?id=' . $v->id); ?>">修改银行卡</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        $('#btn-commit-filter').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".btn-clear").click(function(){
            $(":input","#form-filter")
                .not(":button",":reset","hidden","submit")
                .val("")
                .removeAttr("selected");
        });
    });
</script>
