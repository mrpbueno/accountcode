<h3><?php echo ($itemid ? _("Edit account") : _("New account")) ?></h3>
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
                            <input type="text" maxlength="50" class="form-control maxlen" id="name" name="name" value="<?php echo $name?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="name-help" class="help-block fpbx-help-block"><?php echo _("Enter username")?></span>
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
                            <label class="control-label" for="body"><?php echo _("E-mail") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="email"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="email" maxlength="50" class="form-control maxlen" id="email" name="email" value="<?php echo $email?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="email-help" class="help-block fpbx-help-block"><?php echo _("Enter the e-mail")?></span>
            </div>
        </div>
    </div>
    <!--END Email-->
    <!--account-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="subject"><?php echo _("Account") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="account"></i>
                        </div>
                        <div class="col-md-9">
                            <input type="number" maxlength="20" class="form-control maxlen" id="account" name="account" value="<?php echo $account?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="account-help" class="help-block fpbx-help-block"><?php echo _("Enter the account. The account will be added to the CDR record's 'accountcode' field.")?></span>
            </div>
        </div>
    </div>
    <!--END account-->
    <!--Rules-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="subject"><?php echo _("Rules") ?></label>
                            <i class="fa fa-question-circle fpbx-help-icon" data-for="rules"></i>
                        </div>
                        <div class="checkbox col-sm-6">
                            <?php foreach ($rule as $r): ;?>
                                <label class="col-sm-12">
                                    <input type="checkbox" name="rules[]" value="<?php echo $r['id']; ?>"
                                        <?php
                                            $rs = explode(',',$rules);
                                            if (in_array($r['id'],$rs)) {
                                                echo 'checked';
                                            }
                                        ?>
                                    >
                                    <?php echo $r['rule']; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span id="rules-help" class="help-block fpbx-help-block"><?php echo _("Select the access rules.")?></span>
            </div>
        </div>
    </div>
    <!--END Rules-->
    <!--Active-->
    <div class="element-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label" for="active"><?php echo _("Enable") ?></label>
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
                <span id="active-help" class="help-block fpbx-help-block"><?php echo _("Enable or disable Account Code.")?></span>
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
                <span id="reset-help" class="help-block fpbx-help-block"><?php echo _("The default password is 4567")?></span>
            </div>
        </div>
    </div>
    <!--END Reset Pass-->
</form>