<h1 class="page-header">公告管理<a href="<?php echo base_url('notice_edit'); ?>" class="btn btn-lg btn-primary" style="margin-left:25px;">发布公告</a></h1>
<form id="form-filter" class="form-inline" action="" method="GET" style="background-color:#fefefe;line-height:65px;">
    <input type="hidden" id="i_page" name="i_page" value="<?php if (!empty($i_page)) echo $i_page; ?>">
    <div class="form-group">
        <label>关键字</label>
        <input type="text" class="form-control filter-control" name="keywords" placeholder="关键字" value="<?php if (!empty($keywords)) echo $keywords; ?>">
    </div>
    <div class="form-group">
        <label>开始时间</label>
        <input type="text" class="form-control filter-control format_date" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
    </div>
    <div class="form-group">
        <label>结束时间</label>
        <input type="text" class="form-control filter-control format_date" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
    </div>
    <a href="javascript:;" class="btn btn-primary filter-btn">提交查询</a>
</form>
<?php if (!empty($data)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>公告对象</th>
                <th>公告标题</th>
                <th>是否置顶</th>
                <th>排序</th>
                <th>推送时间</th>
                <th>过期时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $k=>$v): ?>
                <tr>
                    <td><?php echo ++$k; ?></td>
                    <td>
                        <?php
                        switch ($v->notice_type) {
                            case NOTICE_TYPE_ALL:
                                echo '全部';
                                break;
                            case NOTICE_TYPE_BUYER:
                                echo '买手';
                                break;
                            case NOTICE_TYPE_SELLER:
                                echo '商家';
                                break;
                        }
                        ?>
                    </td>
                    <td><?php echo $v->title; ?></td>
                    <td>
                        <?php if ($v->is_top):?> 是 <?php else:?> 否 <?php endif;?>
                    </td>
                    <td><?php echo $v->sort; ?></td>
                    <td><?php echo $v->gmt_create; ?></td>
                    <td><?php echo $v->expire_time; ?></td>
                    <td>
                        <a href="<?php echo base_url('notice_edit?notice_id='.$v->id); ?>" class="btn btn-sm btn-success">编辑</a>
                        <a href="javascript:;" class="btn btn-sm btn-danger btn-delete-notice" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('notice/operation_handle'); ?>">删除</a>
                        <?php if ($v->is_top):?>
                            <a href="javascript:;" data-act="undo" class="btn btn-sm btn-danger btn-top-notice" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('notice/top_handle'); ?>">取消置顶</a>
                        <?php else:?>
                            <a href="javascript:;" data-act="do" class="btn btn-sm btn-danger btn-top-notice" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('notice/top_handle'); ?>">置顶</a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('fragment_pagination'); ?>
<?php endif; ?>
<script>
    $(function () {
        $('.filter-btn').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $(".btn-delete-notice").click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (window.confirm("确定要删除这条公告吗？")) {

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {
                        act: 'delete_notice',
                        notice_id: that.data('id')
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            }
        });

        $(".btn-top-notice").click(function (e) {
            e.preventDefault();
            var that = $(this);

            that.addClass('disabled');
            that.attr("disabled", true);

            ajax_request(
                that.data('url'),
                {
                    act: that.data('act'),
                    notice_id: that.data('id')
                },
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        location.reload();
                    } else {
                        alert(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });


        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });

        $(".fancybox").fancybox();
    });
</script>