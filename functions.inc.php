<?php
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

/**
 * @param $engine
 * @throws Exception
 */
function accountcode_get_config($engine)
{
    global $ext;

    $ext->add('macro-accountcode', 's', '', new ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'Read','CODPASS,agent-pass,10,,2,4'));
    $ext->add('macro-accountcode', 's', '', new ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'AGI','accountcode.php,${CODPASS},${ARG1},${ARG2}'));
    $ext->add('macro-accountcode', 's', '', new ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'ResetCDR','v'));

    $usage_list = accountcode_list_usage('routing');
    if (is_array($usage_list) && count($usage_list)) {
        foreach ($usage_list as $thisroute) {
            $context = 'outrt-'.$thisroute['foreign_id'];
            $patterns = core_routing_getroutepatternsbyid($thisroute['foreign_id']);
            foreach ($patterns as $pattern) {
                $fpattern = core_routing_formatpattern($pattern);
                $exten = $fpattern['dial_pattern'];
                $ext->splice($context, $exten, 1, new ext_macro('accountcode',$thisroute['foreign_id'].','.$thisroute['rule_id']),'accountcode');
            }
        }
    }
}

/**
 * @param bool $dispname
 * @return bool|null
 * @throws Exception
 */
function accountcode_list_usage($dispname=true)
{
    $sql = 'SELECT * FROM `accountcode_rules_usage`';
    if ($dispname !== true) {
        $sql .= " WHERE `dispname` = '$dispname'";
    }
    return sql($sql,'getAll',DB_FETCHMODE_ASSOC);
}

function accountcode_rules()
{
    return \FreePBX::Accountcode()->getListRule();
}

/**
 * @param $route_id
 * @param $action
 * @param string $rule_id
 * @throws Exception
 */
function accountcode_adjustroute($route_id, $action, $rule_id='')
{
    global $db;
    $dispname = 'routing';
    $route_id = $db->escapeSimple($route_id);
    $rule_id = $db->escapeSimple($rule_id);
    switch ($action) {
        case 'delroute':
            sql('DELETE FROM accountcode_rules_usage WHERE foreign_id ='.q($route_id)." AND dispname = '$dispname'");
            break;
        case 'addroute';
            if ($rule_id != '') {
                $_SESSION['accountcodeAddRoute'] = $rule_id;
            }
            break;
        case 'delayed_insert_route';
            if ($rule_id != '') {
                sql("INSERT INTO accountcode_rules_usage (dispname, foreign_id, rule_id) VALUES ('$dispname', '$route_id', $rule_id)");
            }
            break;
        case 'editroute';
            if ($rule_id != '') {
                sql("REPLACE INTO accountcode_rules_usage (dispname, foreign_id, rule_id) VALUES ('$dispname', '$route_id', '$rule_id')");
            } else {
                sql('DELETE FROM accountcode_rules_usage WHERE foreign_id ='.q($route_id)." AND dispname = '$dispname'");
            }
            break;
    }
}

/**
 * @param $viewing_itemid
 * @param $target_menuid
 * @return bool|string
 * @throws Exception
 */
function accountcode_hook_core($viewing_itemid, $target_menuid)
{
    global $db;
    switch ($target_menuid) {
        case 'routing':
            $rules = accountcode_rules();
            if ($viewing_itemid == '') {
                $selected_rule = '';
            } else {
                if (isset($_SESSION['accountcodeAddRoute']) && $_SESSION['accountcodeAddRoute'] != '') {
                    $selected_rule = $_SESSION['accountcodeAddRoute'];
                } else {
                    $selected_rule = $db->getOne("SELECT rule_id FROM accountcode_rules_usage WHERE dispname='routing' AND foreign_id='".$db->escapeSimple($viewing_itemid)."'");
                    if(DB::IsError($selected_rule)) {
                        die_freepbx($selected_rule->getMessage());
                    }
                }
            }
            $hookhtml = '
			<!--Accountcode HOOK-->
			<div class="element-container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="form-group">
								<div class="col-md-3">
									<label class="control-label" for="accountcode">'. _("Account Code Rule").'</label>
									<i class="fa fa-question-circle fpbx-help-icon" data-for="accountcode"></i>
								</div>
								<div class="col-md-9">
									<select name="accountcode" class="form-control">
										<option value="">'._('None').'</option>';
            if (is_array($rules)) {
                foreach($rules as $rule) {
                    $selected = $selected_rule == $rule['id'] ? 'selected' : '';
                    $hookhtml .= "<option value={$rule['id']} ".$selected.">{$rule['rule']}</option>";
                }
            }

            $hookhtml .= '				</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="accountcode-help" class="help-block fpbx-help-block">'._('Optional: Select a rule to use. If using this option, leave the Route Password field blank.').'</span>
					</div>
				</div>
			</div>
			<!--END Accountcode HOOK-->
			';
            return $hookhtml;
            break;

        default:
            return false;
            break;
    }
}

/**
 * @param $viewing_itemid
 * @param $request
 */
function accountcode_hookProcess_core($viewing_itemid, $request)
{
    switch ($request['display']) {
        case 'routing':
            $action = (isset($request['action']))?$request['action']:null;
            $route_id = $viewing_itemid;
            if (isset($request['Submit']) ) {
                $action = (isset($action))?$action:'editroute';
            }
            if (!$action && isset($_SESSION['accountcodeAddRoute']) && $_SESSION['accountcodeAddRoute'] != '') {
                accountcode_adjustroute($route_id,'delayed_insert_route',$_SESSION['accountcodeAddRoute']);
                unset($_SESSION['accountcodeAddRoute']);
            } elseif ($action) {
                accountcode_adjustroute($route_id,$action,$request['accountcode']);
            }
            break;
    }
}