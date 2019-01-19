<?php

namespace FreePBX\modules;

use Exception;
use FreePBX\BMO;
use FreePBX\FreePBX_Helpers;
use PDO;
use PDOException;

class Accountcode extends FreePBX_Helpers implements BMO
{

    /**
     * Accountcode constructor.
     * @param null $freepbx
     * @throws Exception
     */
    public function __construct($freepbx = null)
    {
        if ($freepbx == null) {
            throw new Exception("Not given a FreePBX Object");
        }
        $this->FreePBX = $freepbx;
        $this->db = $freepbx->Database;
    }

    public function install()
    {
        // TODO: Implement install() method.
    }

    /**
     * @throws Exception
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function backup()
    {
        // TODO: Implement backup() method.
    }

    public function restore($backup)
    {
        // TODO: Implement restore() method.
    }

    /**
     * Processes form submission and pre-page actions.
     *
     * @param string $page Display name
     * @return bool
     * @throws Exception
     */
    public function doConfigPageInit($page)
    {
        $action = $this->getReq('action','');
        $id = $this->getReq('id','');

        switch ($page) {
            case 'accountcode':
                switch ($action) {
                    case 'add':
                        return $this->addAccount($_REQUEST);
                        break;
                    case 'delete':
                        return $this->deleteAccount($id);
                        break;
                    case 'edit':
                        $this->updateAccount($_REQUEST);
                        break;
                }
                break;
            case 'accountcode_rules':
                switch ($action) {
                    case 'add':
                        return $this->addRule($_REQUEST);
                        break;
                    case 'delete':
                        return $this->deleteRule($id);
                        break;
                    case 'edit':
                        $this->updateRule($_REQUEST);
                        break;
                }
                break;
        }
    }

    /**
     * @param $post
     * @return bool|void
     * @throws Exception
     */
    public function addAccount($post)
    {
        $pass = password_hash('4567',PASSWORD_DEFAULT);
        $rule = isset($post['rules']) ? $post['rules'] : $rule = array();
        $rules = implode(',', $rule);
        $sql = "INSERT INTO accountcode (name, email, account, pass, rules, active) VALUES (:name, :email, :account, :pass, :rules, :active)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
        $stmt->bindParam(':account', $post['account'], PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':rules', $rules, PDO::PARAM_STR);
        $stmt->bindParam(':active', $post['active'], PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error! Duplicate account.")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode');
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function deleteAccount($id)
    {
        $sql = "DELETE FROM accountcode WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            die_freepbx($stmt->getMessage()."<br><br>".$sql);
        }

        return redirect('config.php?display=accountcode');
    }

    /**
     * @param $post
     * @return bool|void
     * @throws Exception
     */
    public function updateAccount($post)
    {
        $rule = isset($post['rules']) ? $post['rules'] : $rule = array();
        $rules = implode(',', $rule);
        if (isset($post['reset'])) {
            $pass = password_hash('4567',PASSWORD_DEFAULT);
            $sql = 'UPDATE accountcode SET name = :name, email = :email, account = :account, pass = :pass, rules = :rules, active = :active WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        } else {
            $sql = 'UPDATE accountcode SET name = :name, email = :email, account = :account, rules = :rules, active = :active WHERE id = :id';
            $stmt = $this->db->prepare($sql);
        }
        $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
        $stmt->bindParam(':account', $post['account'], PDO::PARAM_STR);
        $stmt->bindParam(':rules', $rules, PDO::PARAM_STR);
        $stmt->bindParam(':active', $post['active'], PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error! Duplicate account.")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode');
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneAccount($id)
    {
        $sql = "SELECT id,name,email,account,rules,active FROM accountcode WHERE id = :id";
        $stmt = $this->Database->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchObject();
        return [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'account' => $row->account,
            'rules' => $row->rules,
            'active' => $row->active,
            'rule' => $this->getListRule(),
        ];
    }

    /**
     * @return null|array
     */
    public function getListAccount()
    {
        $sql = 'SELECT id,name,email,account,active FROM accountcode';
        $data = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            return $data;
        }
        return null;
    }

    /**
     * @param $post
     * @return bool|void
     * @throws Exception
     */
    public function addRule($post)
    {
        $sql = "INSERT INTO accountcode_rules (rule) VALUES (:rule)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':rule', $post['rule'], PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error! Duplicate rule.")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode_rules');
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function deleteRule($id)
    {
        $sql = "DELETE FROM accountcode_rules WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            die_freepbx($stmt->getMessage()."<br><br>".$sql);
        }

        return redirect('config.php?display=accountcode_rules');
    }

    /**
     * @param $post
     * @return bool|void
     * @throws Exception
     */
    public function updateRule($post)
    {
        $sql = 'UPDATE accountcode_rules SET rule = :rule WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
        $stmt->bindParam(':rule', $post['rule'], PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error! Duplicate rule.")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode_rules');
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneRule($id)
    {
        $sql = "SELECT id,rule FROM accountcode_rules WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchObject();
        return [
            'id' => $row->id,
            'rule' => $row->rule,
        ];
    }

    /**
     * @return null|array
     */
    public function getListRule()
    {
        $sql = 'SELECT id,rule FROM accountcode_rules';
        $data = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            return $data;
        }
        return null;
    }

    /**
     * Adds buttons to the bottom of pages per set conditions
     *
     * @param array $request $_REQUEST
     * @return array
     */
    public function getActionBar($request)
    {
        if ('accountcode' == $request['display'] || 'accountcode_rules' == $request['display']) {
            if (!isset($_GET['view'])) {
                return [];
            }
            $buttons = [
                'delete' => ['name' => 'delete', 'id' => 'delete', 'value' => _('Delete'),],
                'reset' => ['name' => 'reset', 'id' => 'reset', 'value' => _("Reset"),],
                'submit' => ['name' => 'submit', 'id' => 'submit', 'value' => _("Submit"),],
            ];
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                unset($buttons['delete']);
            }
            return $buttons;
        }
    }

    /**
     * Returns bool permissions for AJAX commands
     * https://wiki.freepbx.org/x/XoIzAQ
     * @param string $command The ajax command
     * @param array $setting ajax settings for this command typically untouched
     * @return bool
     */
    public function ajaxRequest($command, &$setting)
    {
        //The ajax request
        if ("getJSON" == $command ) {
            return true;
        }
        return false;
    }

    /**
     * Handle Ajax request
     * @url ajax.php?module=accountcode&command=getJSON&jdata=grid&page=account
     * @url ajax.php?module=accountcode&command=getJSON&jdata=grid&page=rules
     *
     * @return array
     */
    public function ajaxHandler()
    {
        if('getJSON' == $_REQUEST['command'] && 'grid' == $_REQUEST['jdata']){
            $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '';
            switch ($page) {
                case 'account':
                    return $this->getListAccount();
                    break;
                case 'rules':
                    return $this->getListRule();
                    break;
            }
        }
        return json_encode(['status' => false, 'message' => _("Invalid Request")]);
    }

    /**
     * This returns html to the main page
     *
     * @param $page
     * @return string html
     */
    public function showPage($page)
    {
        switch ($page) {
            case 'rules':
                $content = load_view(__DIR__ . '/views/rules/grid.php');
                if('form' == $_REQUEST['view']){
                    $content = load_view(__DIR__ . '/views/rules/form.php');
                    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $content = load_view(__DIR__.'/views/rules/form.php', $this->getOneRule($_REQUEST['id']));
                    }
                }
                return load_view(__DIR__.'/views/rules/default.php', ['content' => $content]);
                break;
            case 'account':
                $content = load_view(__DIR__ . '/views/account/grid.php');
                if('form' == $_REQUEST['view']){
                    $rule = $this->getListRule();
                    $content = load_view(__DIR__ . '/views/account/form.php', ['rule' => $rule, 'active' => '1']);
                    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $content = load_view(__DIR__.'/views/account/form.php', $this->getOneAccount($_REQUEST['id']));
                    }
                }
                return load_view(__DIR__.'/views/account/default.php', ['content' => $content]);
                break;
        }
    }

    /**
     * @param $request
     * @return string
     */
    public function getRightNav($request)
    {
        return load_view(__DIR__."/views/account/rnav.php",array());
    }

    /**
     * @return bool
     */
    public function myDialplanHooks()
    {
        return true;
    }

    /**
     * Dialplan generation
     *
     * @param object $ext The dialplan object we add to
     * @param string $engine This will always be asterisk
     * @param int $priority 500?
     * @return void
     */
    public function doDialplanHook(&$ext, $engine, $priority)
    {
        $fcc = new \featurecode('accountcode', 'updatepass');
        $fcc->setDescription('Update Account Code password');
        $fcc->setDefault('*11');
        $fcc->update();
        $hw_fc = $fcc->getCodeActive();
        unset($fcc);
        $id = 'app-accountcode';
        $ext->addInclude('from-internal-additional', $id);
        $ext->add($id, $hw_fc, '', new \ext_goto('1', 's', 'app-accountcode-updatepass'));
        $id = 'app-accountcode-updatepass';
        $c = 's';
        $ext->add($id, $c, 'label', new \ext_answer());
        $ext->add($id, $c, '', new \ext_wait(1));
        $ext->add($id, $c, '', new \ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'Read','ACCOUNT,enter_account&followed_pound,10,,2,4'));
        $ext->add($id, $c, '', new \ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'Read','PASSWORD,enter-password,10,,2,4'));
        $ext->add($id, $c, '', new \ext_execif('$["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]', 'AGI','accountcode-updatepass.php,${ACCOUNT},${PASSWORD}'));
        $ext->add($id, $c, 'hangup', new \ext_hangup());
    }
}
