<!DOCTYPE html>
<html>
<head>
    <?php $this->load->view('header'); ?>
</head>
<body>
<?php $this->load->view('nav'); ?>
<div class="container-fluid">
    <div class="row">
        <?php $this->load->view('menu'); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" style="padding-top:100px;">
            <?php $this->load->view($TargetPage); ?>
        </div>
    </div>
</div>
<?php $this->load->view('footer'); ?>
</body>
</html>