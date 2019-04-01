<h3 class="page-header">权限管理</h3>
<div style="border: 1px solid #ddd;padding:30px;font-size:18px;">
    <form class="form-horizontal bootstrap-admin-login-form" action="" method="post">
        <div class="panel panel-default">
            <div class="panel-heading">用户权限设置</div>
            <div class="panel-body">
                <div class="row">
                    <div class="checkbox">
                        <?php
                        foreach (Authorityenum::get_authority_list() as $permission_item) {
                            echo '<label class="col-md-2" style="height:40px;margin-right:60px;">';
                            echo '<input type="checkbox" name="p_value[]" value="' . $permission_item['auth_code'] . '"';
                            if (in_array($permission_item['auth_code'], $data)) {
                                echo ' checked="checked"';
                            }
                            echo '>' . $permission_item['menu_name'] . '</label>';
                        }
                        ?>
                    </div>
                </div>
                <div class="row col-md-3" style="margin-top:50px;">
                    <button id="change_permission_btn" type="submit" class="btn btn-lg btn-primary">提交修改</button>
                </div>
            </div>
        </div>
    </form>
</div>