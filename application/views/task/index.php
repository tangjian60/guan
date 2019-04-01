<h1 class="page-header">派单管理</h1>
<form id="form-filter" class="form-inline " method="get">
    <div class="row" style="margin:10px 20px;">
        <div class="col-md-5">
            <div class="form-group">
                <label>任务类型: </label>
                <select class="form-control" name="task_type">
                    <?php
                    $task_array = array(
                        TASK_TYPE_LL => '流量单',
                        TASK_TYPE_DF => '垫付单',
                        TASK_TYPE_PDD => '拼多多'
                    );

                    $task_type = isset($task_type) ? $task_type : '';
                    foreach ($task_array as $k => $v) {
                        echo '<option value="' . $k . '"';
                        if ($k == $task_type) {
                            echo ' selected';
                        }
                        echo '>' . $v . '</option>';
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

<div class="bootstrap-admin-panel-content">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>编号</th>
            <th>任务编号</th>
            <th>店铺名称</th>
            <th>放单模式</th>
            <th>花呗设置</th>
            <th>性别限制</th>
            <th>年龄限制</th>
            <th>等级限制</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $k=>$v) :$v = json_decode($v, true); ?>
            <tr>
                <td><?php echo ++$k; ?></td>
                <td><?php echo encode_id($v['id']); ?></td>
                <td><?php echo $v['shop_name']; ?></td>
                <td><?php echo $v['is_preferred'] == 1 ? '优先模式，优先派送给会员接单' : '普通模式'; ?></td>
                <td><?php echo isset($v['is_huabei']) ? ($v['is_huabei'] == 1 ? '只允许开通花呗的会员接单' : '不限制') : 'N/A'; ?></td>
                <td><?php echo isset($v['sex_limit']) ? ($v['sex_limit'] == 'na' ? '不限' : $v['sex_limit']) : 'N/A'; ?></td>
                <td><?php
                    if (isset($v['age_limit'])) {
                        switch ($v['age_limit']) {
                            case '15':
                                echo '15-25岁';
                                break;
                            case '26':
                                echo '26-35岁';
                                break;
                            case '36':
                                echo '36-45岁';
                                break;
                            case '46':
                                echo '46-55岁';
                                break;
                            case '56':
                                echo '56岁以上';
                                break;
                            default:
                                echo '不限制';
                                break;
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>  </td>
                <td>
                    <?php
                    if (isset($v['tb_rate_limit'])) {
                        switch ($v['tb_rate_limit']) {
                            case 3:
                                echo '3心';
                                break;
                            case 4:
                                echo '4心';
                                break;
                            case 5:
                                echo '5心';
                                break;
                            case 6:
                                echo '1钻';
                                break;
                            case 7:
                                echo '2钻';
                                break;
                            case 8:
                                echo '3钻';
                                break;
                            case 9:
                                echo '4钻';
                                break;
                            case 10:
                                echo '5钻';
                                break;
                            case 11:
                                echo '1皇冠';
                                break;
                            case 12:
                                echo '2皇冠';
                                break;
                            case 13:
                                echo '3皇冠';
                                break;
                            case 14:
                                echo '4皇冠';
                                break;
                            case 15:
                                echo '5皇冠 ';
                                break;
                            default:
                                echo '不限制';
                                break;
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td>派单中</td>
                <td>
                    <a href="<?php echo base_url('task_details?task_id=' . encode_id($v['id']) . '&task_type=' . $task_type); ?>" class="btn btn-sm btn-primary">任务详情</a>
                </td>
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
