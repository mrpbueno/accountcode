<?php


namespace FreePBX\modules;


use DB;
use Exception;
use FreePBX\BMO;
use FreePBX\FreePBX_Helpers;

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
        return true;
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
}