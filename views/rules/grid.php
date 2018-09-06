<div id="toolbar-all">
    <a href="?display=accountcode_rules&view=form" class="btn btn-default"><i class="fa fa-plus"></i>&nbsp;<?php echo _("Add Rule")?></a>
    <a href="?display=accountcode&view=form" class="btn btn-default"><i class="fa fa-plus"></i>&nbsp;<?php echo _("Add Account")?></a>
</div>
<table id="accountcode"
       data-url="ajax.php?module=accountcode&page=rules&command=getJSON&jdata=grid&page=rules"
       data-cache="false"
       data-state-save="true"
       data-state-save-id-table="accountcode_rules_grid"
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
            <th data-field="rule" class="col-md-9"><?php echo _("Rule")?></th>
            <th data-field="id" data-formatter="linkFormat" class="col-md-1 text-center"><?php echo _("Actions")?></th>
		</tr>
	</thead>
</table>
<script type="text/javascript">
    function linkFormat(value, row, index){
        var html = '<a href="?display=accountcode_rules&view=form&id='+value+'"><i class="fa fa-pencil-square-o"></i></a>&nbsp;';
        html += '<a class="delAction" href="?display=accountcode_rules&action=delete&id='+value+'"><i class="fa fa-trash"></i></a>';
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