<h1 class="page-header">公告编辑</h1>
<form class="form-horizontal form-notice-edit" action="" method="POST">
    <input type="hidden" name="notice_id" value="<?php if (!empty($notice_id)) echo $notice_id; ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">公告详情</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <div class="form-group">
                <label class="col-md-3 control-label">公告标题</label>
                <div class="col-md-8">
                    <input placeholder="公告标题" class="form-control" type="text" name="title" value="<?php if (!empty($notice_info)) echo $notice_info->title; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">公告对象</label>
                <div class="col-md-8">
                    <select class="form-control" name="notice_type">
                        <?php
                        $notice_type_array = array(
                            NOTICE_TYPE_ALL => '全部',
                            NOTICE_TYPE_BUYER => '买手',
                            NOTICE_TYPE_SELLER => '商家'
                        );

                        foreach ($notice_type_array as $k => $v) {
                            echo '<option value="' . $k . '"';
                            if (!empty($notice_info) && $notice_info->notice_type == $k) {
                                echo ' selected';
                            }
                            echo '>' . $v . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">公告内容</label>
                <div class="col-md-8">
                    <textarea class="form-control" rows="8" name="content"><?php if (!empty($notice_info)) echo $notice_info->content; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">排序</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="sort" value="<?php if (!empty($notice_info)) echo $notice_info->sort; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">过期时间</label>
                <div class="col-md-8">
                    <input type="text" class="form-control format_date" name="expire_time" value="<?php if (!empty($notice_info)) echo $notice_info->expire_time; ?>">
                </div>
            </div>
            <div style="text-align:center;margin-top:50px;">
                <button class="btn btn-lg btn-primary btn-notice-commit">提交</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function () {
        $(".btn-notice-commit").click(function (e) {
            e.preventDefault();
            var form_data = $('.form-notice-edit').formToJSON();
            var that = $(this);

            if (form_data.notice_id == "") {
                form_data.notice_id = '0';
            }

            if (invalid_parameter(form_data)) {
                alert('请填写所有字段');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);

            $('.form-notice-edit').submit();
        });

        $(".format_date").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });
    });
</script>