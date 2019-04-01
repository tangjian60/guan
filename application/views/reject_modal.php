<!-- 模态框（Modal） -->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<form id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times</button>
                <h4 class="modal-title" id="myModalLabel">拒绝原因</h4>
            </div>
            <div class="panel-body" >
                <label class="col-md-3 control-label" style="padding-top: 8px;">请选择</label>
                <div class="col-md-8">
                    <select class="form-control filter-control memo1" >
                        <?php foreach($reject as $k => $v):?>
                        <option value="<?php echo $k;?>"> <?php echo $v;?></option>
                        <?php endforeach;?>
                    </select>
                </div>

                <label class="col-md-3 control-label" style="padding-top: 8px;">备注</label>
                <div class="col-md-8">
                    <input placeholder="备注" type="text" value="" class="form-inline memo2 form-control filter-control" />
                    <input id="id" name="id" value="" type="hidden"/>
                    <input id="url" value="" type="hidden"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-default btn-reject">提交审核</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</form>
</div>


<!-- form-control filter-control format_date -->