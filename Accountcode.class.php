<?php


namespace FreePBX\modules;


use DB;
use Exception;
use FreePBX\BMO;
use FreePBX\FreePBX_Helpers;
use PDO;

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

    /**
     * Install tables
     * https://wiki.freepbx.org/display/FOP/Database+13
     * @throws Exception
     */
    public function install()
    {
        $table = $this->db->migrate("accountcode");
        $cols = [
            'id' => [
                'type' => 'integer',
                'primarykey' => true,
                'autoincrement' => true,
            ],
            'name' => [
                'type' => 'string',
                'length' => 50,
            ],
            'email' => [
                'type' => 'string',
                'length' => 50,
                ],
            'code' => [
                'type' => 'string',
                'length' => 20,
                ],
            'pass' => [
                'type' => 'string',
                'length' => 100,
                ],
            'rules' => [
                'type' => 'string',
                'length' => 200,
                ],
            'active' => [
                'type' => 'boolean',
                'length' => 1,
                ],
        ];
        $indexes = [
            'code' => [
                'type' => 'unique',
                'cols' => ['code'],
            ],
        ];
        $table->modify($cols, $indexes);
        unset($table);

        $table = $this->db->migrate("accountcode_usage");
        $cols = [
            'rule_id' => [
                'type' => 'integer',
                'length' => 11,
            ],
            'dispname' => [
                'type' => 'string',
                'length' => 30,
                'primarykey' => true,
            ],
            'foreign_id' => [
                'type' => 'integer',
                'length' => 11,
                'primarykey' => true,
            ],
        ];
        $table->modify($cols);
        unset($table);

        $table = $this->db->migrate("accountcode_rules");
        $cols = [
            'id' => [
                'type' => 'integer',
                'primarykey' => true,
                'autoincrement' => true,
            ],
            'rule' => [
                'type' => 'string',
                'length' => 50,
            ],
        ];
        $table->modify($cols);
        unset($table);
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
        $action = q($this->getReq('action',''));
        $id = q($this->getReq('id',''));
        $name = q($this->getReq('name'));
        $email = q($this->getReq('email'));
        $code = q($this->getReq('code',''));
        $reset = q($this->getReq('reset', ''));
        $rules = q($this->getReq('rules', ''));
        $active = q($this->getReq('active', ''));

        switch ($action) {
            case 'add':
                return $this->addItem($name, $email, $code, $rules, $active);
                break;
            case 'delete':
                return $this->deleteItem($id);
                break;
            case 'edit':
                $this->updateItem($id, $name, $email, $code, $rules, $active, $reset);
                break;
        }
    }

    /**
     * @param $name
     * @param $email
     * @param $code
     * @param $rules
     * @param $active
     * @return bool
     * @throws Exception
     */
    public function addItem($name, $email, $code, $rules, $active)
    {
        $pass = password_hash('4567',PASSWORD_DEFAULT);
        $sql = "INSERT INTO accountcode (name, email, code, pass, rules, active) VALUES ($name, $email, $code, $pass, $rules, $active)";
        $results = $this->db->query($sql);
        if (DB::IsError($results)) {
            if ($results->getCode() == DB_ERROR_ALREADY_EXISTS) {
                echo "<script>javascript:alert('"._("Error Duplicate Code Entry")."')</script>";
                return false;
            } else {
                die_freepbx($results->getMessage()."<br><br>".$sql);
            }
        }
        return redirect('config.php?display=accountcode');
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function deleteItem($id)
    {
        $sql = "DELETE FROM accountcode WHERE id = $id";
        $results = $this->db->query($sql);
        if (DB::IsError($results)) {
            die_freepbx($results->getMessage()."<br><br>".$sql);
        }
        return true;
    }

    public function updateItem($id, $name, $email, $code, $rules, $active, $reset)
    {
        //
    }

    /**
     * getOne Gets an individual item by ID
     * @param  int $id Item ID
     * @return array Returns an associative array.
     */
    public function getOne($id)
    {
        $sql = "SELECT id,name,email,code,rules,active FROM accountcode WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchObject();
        return [
            'id' => $row->id,
            'name' => $row->name,
            'code' => $row->code,
            'active' => $row->active
        ];
    }

    /**
     * getList gets a list od pins and their respective id.
     * @return array
     */
    public function getList()
    {
        $sql = 'SELECT id,name,code,active FROM accountcode';
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
        if ('accountcode' == $request['display']) {
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
     * @url ajax.php?module=pinpass&command=getJSON&jdata=grid
     *
     * @return array
     */
    public function ajaxHandler()
    {
        if('getJSON' == $_REQUEST['command'] && 'grid' == $_REQUEST['jdata']){
            return $this->getList();
        }
        return json_encode(['status' => false, 'message' => _("Invalid Request")]);
    }

    /**
     * This returns html to the main page
     *
     * @return string html
     */
    public function showPage()
    {
        $subhead = _('Item List');
        $content = load_view(__DIR__ . '/views/grid.php');

        if('form' == $_REQUEST['view']){
            $subhead = _('Add Item');
            $content = load_view(__DIR__ . '/views/form.php', ['active' => '1']);
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                $subhead = _('Edit Item');
                $content = load_view(__DIR__.'/views/form.php', $this->getOne($_REQUEST['id']));
            }
        }
        echo load_view(__DIR__.'/views/default.php', ['subhead' => $subhead, 'content' => $content]);
    }
}