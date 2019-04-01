<h1 class="page-header">代理商管理</h1>
<form id="form-filter" class="form-inline " method="get">
    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
    <div class="row" style="margin:10px 20px;">
        <div class="col-md-5">
            <div class="form-group">
                <label>代理商账号: </label>
                <input type="text" name="seller_name" value="<?php echo $seller_name?>" placeholder="代理商账号"/>
            </div>
            <div class="form-group">
                <label>授权状态: </label>
                <select name="status" style="width:160px;margin-left:15px;" class="form-control">
                    <option value="">全部</option>
                    <?php
                    foreach ( $status_arr as $key => $value) {
                        echo '<option value="' . $key . '"';
                        if (isset($status) && $status != '' && $key == $status) {
                            echo ' selected';
                        }
                        echo '>' . $value . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary btn-clear">清理</a>
        </div>
    </div>
</form>

<div class="form-group">
    <label class="col-md-9 control-label"></label>
    <div class="col-md-9">
        <div id="error_display" class="alert"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title"><a href="<?php echo base_url('agent/add')?>">添加授权代理商</a></div>
            </div>
        </div>
    </div>
</div>

<div class="bootstrap-admin-panel-content">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>序号</th>
            <th>代理商账号</th>
            <th>淘宝垫付单提成(单)</th>
            <th>淘宝浏览单提成(单)</th>
            <th>拼多多垫付提成(单)</th>
            <th>授权状态</th>
            <th>授权时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $k=>$v): ?>
            <tr>
                <td><?php echo ++$k; ?></td>
                <td><?php echo $v->id; ?></td>
                <td><?php echo $v->seller_name; ?></td>
                <td><?php echo $v->tb_prepaid?></td>
                <td><?php echo $v->tb_flow?></td>
                <td><?php echo $v->pdd_prepaid?></td>
                <td><?php echo $status_arr["$v->status"]?></td>
                <td><?php echo date("y-m-d H:i:s",$v->mtime)?></td>
                <td>
                    <a class="btn btn-sm btn-primary" href="<?php echo base_url('agent/edit?id=' . $v->id); ?>">编辑</a>
                    <?php if ($v->status == \CONSTANT\Agent::STATUS_NORMAL){?>
                        <a class="btn-frozen btn btn-sm btn-primary" data-user-name="<?php echo $v->seller_name?>" data-act="frozen" data-id="<?php echo $v->id?>" data-url="<?php echo base_url('agent/frozen')?>" href="javascript:;">暂停授权</a>
                    <?php }else{?>
                        <a class="btn-unfrozen btn btn-sm btn-primary" data-user-name="<?php echo $v->seller_name?>" data-act="unfrozen" data-id="<?php echo $v->id?>" data-url="<?php echo base_url('agent/unfrozen')?>" href="javascript:;">解冻授权 </a>
                    <?php }?>
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

        $(".btn-frozen").click(function (e) {
            e.preventDefault();
            agent($(this));
        });

        function agent(that)
        {
            $(function(that){
                var data = {
                    'act': that.data('act'),
                    'user_name': that.data('user-name'),
                    'id':that.data('id')
                };

                var url = that.data('url');
                console.log(url, data);
                ajax_request(url, data, function(e){
                    if (e.code == CODE_SUCCESS) {
                        show_success_message(e.msg)
                        goto_url(location.href, 800);
                    } else {
                        show_error_message(e.msg);
                        hide_error_message(5000);
                    }
                    that.removeClass('disabled');
                    that.attr("disabled", false);
                })
            }(that));
        }

        $(".btn-unfrozen").click(function (e) {
            e.preventDefault();
            agent($(this));
        });

        $(".btn-clear").click(function(){
            $(":input","#form-filter")
                .not(":button",":reset","hidden","submit")
                .val("")
                .removeAttr("selected");
        });
    });
</script>
