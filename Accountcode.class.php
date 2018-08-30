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

    public function uninstall()
    {
        echo "dropping table accountcode..";
        sql('DROP TABLE IF EXISTS `accountcode`');
        echo "done<br>\n";
        echo "dropping table accountcode_usage..";
        sql('DROP TABLE IF EXISTS `accountcode_usage`');
        echo "done<br>\n";
        echo "dropping table accountcode_rules..";
        sql('DROP TABLE IF EXISTS `accountcode_rules`');
        echo "done<br>\n";
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
                        return $this->addCode($_REQUEST);
                        break;
                    case 'delete':
                        return $this->deleteCode($id);
                        break;
                    case 'edit':
                        $this->updateCode($_REQUEST);
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

    public function addCode($post)
    {
        $pass = password_hash('4567',PASSWORD_DEFAULT);
        $rules = implode(',', $post['rules']);
        $sql = "INSERT INTO accountcode (name, email, code, pass, rules, active) VALUES (:name, :email, :code, :pass, :rules, :active)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
        $stmt->bindParam(':code', $post['code'], PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':rules', $rules, PDO::PARAM_STR);
        $stmt->bindParam(':active', $post['active'], PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error duplicate entry")."')</script>";
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
    public function deleteCode($id)
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

    public function updateCode($post)
    {
        $rules = implode(',', $post['rules']);
        if (isset($post['reset'])) {
            $pass = password_hash('4567',PASSWORD_DEFAULT);
            $sql = 'UPDATE accountcode SET name = :name, email = :email, code = :code, pass = :pass, rules = :rules, active = :active WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        } else {
            $sql = 'UPDATE accountcode SET name = :name, email = :email, code = :code, rules = :rules, active = :active WHERE id = :id';
            $stmt = $this->db->prepare($sql);
        }
        $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $post['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $post['email'], PDO::PARAM_STR);
        $stmt->bindParam(':code', $post['code'], PDO::PARAM_STR);
        $stmt->bindParam(':rules', $rules, PDO::PARAM_STR);
        $stmt->bindParam(':active', $post['active'], PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error duplicate entry")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode');
    }

    public function getOneCode($id)
    {
        $sql = "SELECT id,name,email,code,rules,active FROM accountcode WHERE id = :id";
        $stmt = $this->Database->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchObject();
        return [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'code' => $row->code,
            'rules' => $row->rules,
            'active' => $row->active,
            'rule' => $this->getListRule(),
        ];
    }

    public function getListCode()
    {
        $sql = 'SELECT id,name,email,code,active FROM accountcode';
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
                echo "<script>javascript:alert('"._("Error duplicate entry")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode_rules');
    }

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

    public function updateRule($post)
    {
        $sql = 'UPDATE accountcode_rules SET rule = :rule WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $post['id'], PDO::PARAM_INT);
        $stmt->bindParam(':rule', $post['rule'], PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                echo "<script>javascript:alert('"._("Error duplicate entry")."')</script>";
                return false;
            } else {
                die_freepbx($stmt->getMessage()."<br><br>".$sql);
            }
        }

        return redirect('config.php?display=accountcode_rules');
    }

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
     * @url ajax.php?module=accountcode&command=getJSON&jdata=grid&page=code
     * @url ajax.php?module=accountcode&command=getJSON&jdata=grid&page=rules
     *
     * @return array
     */
    public function ajaxHandler()
    {
        if('getJSON' == $_REQUEST['command'] && 'grid' == $_REQUEST['jdata']){
            $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '';
            switch ($page) {
                case 'code':
                    return $this->getListCode();
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
            case 'code':
                $content = load_view(__DIR__ . '/views/code/grid.php');
                if('form' == $_REQUEST['view']){
                    $rule = $this->getListRule();
                    $content = load_view(__DIR__ . '/views/code/form.php', ['rule' => $rule, 'active' => '1']);
                    if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                        $content = load_view(__DIR__.'/views/code/form.php', $this->getOneCode($_REQUEST['id']));
                    }
                }
                return load_view(__DIR__.'/views/code/default.php', ['content' => $content]);
                break;
        }
    }

    /**
     * @param $request
     * @return string
     */
    public function getRightNav($request)
    {
        return load_view(__DIR__."/views/code/rnav.php",array());
    }
}