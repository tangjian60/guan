<div class="col-sm-3 col-md-2 sidebar">
    <ul class="nav nav-sidebar">
        <?php
        foreach ($menus as $menu_item) {
            echo '<li';
            if ($menu_item['menu_path'] == uri_string()) {
                echo ' class="active"';
            }
            echo '><a href="' . base_url($menu_item['menu_path']) . '">' . $menu_item['menu_name'] . '</a></li>';
        }
        ?>
    </ul>
</div>