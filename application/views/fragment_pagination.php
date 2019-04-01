<nav style="text-align:center;">
    <ul class="pagination pagination-lg">
        <li>
            <a id="btn-previous-page" href="javascript:;">
                <span>&laquo;</span>
            </a>
        </li>
        <li class="active"><a id="label-current-page" href="javascript:;">1</a></li>
        <li>
            <a id="btn-next-page" href="javascript:;">
                <span>&raquo;</span>
            </a>
        </li>
        <li>
            <a href="javascript:;">
                <input type="text" id="txt-jump-page" style="width:32px;height:25px;">
            </a>
        </li>
        <li>
            <a id="btn-jump-page" href="javascript:;">
                <span>跳转</span>
            </a>
        </li>
    </ul>
</nav>
<script type="text/javascript">
    $(function () {

        var i_page = $('#i_page').val();

        if (i_page == "" || i_page < 1) {
            i_page = 1;
        }

        $('#label-current-page').html(i_page);

        if (i_page == 1) {
            $('#btn-previous-page').parent('li').addClass('disabled');
        }

        $('#btn-previous-page').click(function (e) {
            e.preventDefault();

            var n_page = i_page - 1;

            if (n_page > 0) {
                $('#i_page').val(n_page);
                $('#form-filter').submit();
            }
        });

        $('#btn-next-page').click(function (e) {
            e.preventDefault();
            $('#i_page').val(++i_page);
            $('#form-filter').submit();
        });

        $('#btn-jump-page').click(function (e) {
            e.preventDefault();
            if ($('#txt-jump-page').val() == '') return;
            $('#i_page').val($('#txt-jump-page').val());
            $('#form-filter').submit();
        });

    });
</script>