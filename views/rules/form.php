<h3><?php echo ($itemid ? _("Edit Account Code Rule") : _("New Account Code Rule")) ?></h3>
<form autocomplete="off" action="" method="post" class="fpbx-submit" id="hwform" name="hwform" data-fpbx-delete="config.php?display=accountcode&action=delete&id=<?php echo $id?>">
    <input type="hidden" name="view" value="form">
    <input type="hidden" name='action' value="<?php echo $id?'edit':'add' ?>">
    <input type="hidden" name="account" value="<?php echo $itemid; ?>">
    <!--Rule-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="body"><?php echo _("Rule") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="rule"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="rule" name="rule" value="<?php echo $rule?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="rule-help" class="help-block fpbx-help-block"><?php echo _("Help")?></span>
            </div>
        </div>
    </div>
    <!--END Rule-->
</form>