<h3><?php echo ($itemid ? _("Edit Account Code") : _("New Account Code")) ?></h3>
<form autocomplete="off" action="" method="post" class="fpbx-submit" id="hwform" name="hwform" data-fpbx-delete="config.php?display=accountcode&action=delete&id=<?php echo $id?>">
    <input type="hidden" name="view" value="form">
    <input type="hidden" name='action' value="<?php echo $id?'edit':'add' ?>">
    <input type="hidden" name="account" value="<?php echo $itemid; ?>">
    <!--Name-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="body"><?php echo _("Name") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="name"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="name-help" class="help-block fpbx-help-block"><?php echo _("Enter the contents of your note")?></span>
            </div>
        </div>
    </div>
    <!--END Name-->
    <!--Email-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="body"><?php echo _("Email") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="email"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="email-help" class="help-block fpbx-help-block"><?php echo _("Enter the contents of your note")?></span>
            </div>
        </div>
    </div>
    <!--END Email-->
    <!--CODE-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="subject"><?php echo _("Code") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="code"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo $code?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="code-help" class="help-block fpbx-help-block"><?php echo _("Enter a subject for your note")?></span>
            </div>
        </div>
    </div>
    <!--END CODE-->
    <!--Active-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="active"><?php echo _("Active") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="active"></i>
                        </div>
                        <div class="col-md-9 radioset">
                            <input type="radio" class="form-control" id="activeyes" name="active" value="1" <?php echo ($active == '1' ? 'CHECKED' : ''); ?>>
                            <label for="activeyes"><?php echo _("Yes")?></label>
                            <input type="radio" class="form-control" id="activeno" name="active" value="0" <?php echo ($active == '1' ? '' : 'CHECKED'); ?>>
                            <label for="activeno"><?php echo _("No")?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="active-help" class="help-block fpbx-help-block"><?php echo _("Select Yes, if you would like to record the PIN in the call detail records when used")?></span>
            </div>
        </div>
    </div>
    <!--END Active-->
    <!--Reset Pass-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="reset"><?php echo _("Reset Password") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="reset"></i>
                        </div>
                        <div class="col-md-9 radioset">
                            <input type="radio" class="form-control" id="resetyes" name="reset" value="1" <?php echo ($reset == '1' ? 'CHECKED' : ''); ?>>
                            <label for="resetyes"><?php echo _("Yes")?></label>
                            <input type="radio" class="form-control" id="resetno" name="reset" value="0" <?php echo ($reset == '0' ? '' : 'CHECKED'); ?>>
                            <label for="resetno"><?php echo _("No")?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="reset-help" class="help-block fpbx-help-block"><?php echo _("Select Yes, if you would like to record the PIN in the call detail records when used")?></span>
            </div>
        </div>
    </div>
    <!--END Reset Pass-->
</form>