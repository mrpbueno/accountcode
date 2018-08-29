<div id="toolbar-all">
    <a href="?display=accountcode&view=form" class="btn btn-default"><i class="fa fa-plus"></i>&nbsp;<?php echo _("Add Account Code")?></a>
</div>
<table id="pinpass"
       data-url="ajax.php?module=accountcode&command=getJSON&jdata=grid&page=code"
       data-cache="false"
       data-state-save="true"
       data-state-save-id-table="accountcode_grid"
       data-toolbar="#toolbar-all"
       data-maintain-selected="true"
       data-show-columns="true"
       data-show-toggle="true"
       data-toggle="table"
       data-pagination="true"
       data-search="true"
       class="table table-striped">
	<thead>
		<tr>
            <th data-field="name" class="col-md-5"><?php echo _("Name")?></th>
            <th data-field="email" class="col-md-4"><?php echo _("E-mail")?></th>
            <th data-field="code" class="col-md-1 text-center"><?php echo _("Code")?></th>
            <th data-field="active" data-formatter="activeFormat" class="col-md-1 text-center"><?php echo _("Active")?></th>
            <th data-field="id" data-formatter="linkFormat" class="col-md-1 text-center"><?php echo _("Actions")?></th>
		</tr>
	</thead>
</table>
<script type="text/javascript">
    function linkFormat(value, row, index){
        var html = '<a href="?display=accountcode&view=form&id='+value+'"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
        html += '<a class="delAction" href="?display=accountcode&action=delete&id='+value+'"><i class="fa fa-trash"></i></a>';
        return html;
    }
    function activeFormat(value) {
        if(value == 1) {
            var html = '<i class="text-success fa fa-check-circle-o"></i>';
        } else  {
            var html = '<i class="text-danger fa fa-ban"></i>';
        }
        return html;
    }
</script>