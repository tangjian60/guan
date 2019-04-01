<h1 class="page-header">银行卡管理</h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
    <div class="form-group">
        <label>商家ID: </label>
        <input type="text" class="form-control filter-control" name="seller_id" value="<?php echo isset($seller_id) ? $seller_id:'' ?>" placeholder="商家ID"/>
    </div>
    <div class="form-group">
        <label>商家姓名: </label>
        <input type="text" class="form-control filter-control" name="true_name" value="<?php echo isset($true_name) ? $true_name:'' ?>" placeholder="商家姓名"/>
    </div>
    <div class="form-group">
        <label>银行卡号: </label>
        <input type="text" class="form-control filter-control" name="bank_card_num" value="<?php isset($bank_card_num) ? $bank_card_num: ""?>" placeholder="银行卡号"/>
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
            <th>商家ID</th>
            <th>商家姓名</th>
            <th>银行卡号</th>
            <th>开户行银行</th>
            <th>开户支行</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $k=>$v): ?>
            <tr>
                <td><?php echo ++$k; ?></td>
                <td><?php echo encode_id($v->seller_id); ?></td>
                <td><?php echo $v->true_name; ?></td>
                <td><?php echo $v->bank_card_num?></td>
                <td><?php echo $v->bank_name?></td>
                <td><?php echo $v->bank_branch?></td>
                <td><?php echo $v->gmt_create?></td>
                <td>
                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('bank/edit?id=' . $v->id); ?>">修改银行卡</a>
                </td>
            </tr>
        <?php endforeach; ?>
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
