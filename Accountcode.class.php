<?php


namespace FreePBX\modules;


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
        global $db;
        global $amp_conf;

        $autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";

        $sql[] = "CREATE TABLE IF NOT EXISTS accountcode ( 
	              id INTEGER NOT NULL PRIMARY KEY $autoincrement, 
	              name VARCHAR( 50 ) COLLATE utf8mb4_unicode_ci,
	              email VARCHAR( 50 ) COLLATE utf8mb4_unicode_ci,
	              code VARCHAR( 20 ) COLLATE utf8mb4_unicode_ci UNIQUE KEY,
	              pass VARCHAR( 100 ) COLLATE utf8mb4_unicode_ci,
	              rules VARCHAR( 200 ) COLLATE utf8mb4_unicode_ci,
	              active TINYINT ( 1 )
                  ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $sql[] = "CREATE TABLE IF NOT EXISTS accountcode_usage ( 	              
	              rule_id INTEGER( 11 ) COLLATE utf8mb4_unicode_ci,
	              dispname VARCHAR( 30 ) COLLATE utf8mb4_unicode_ci,
	              foreign_id VARCHAR( 30 ) COLLATE utf8mb4_unicode_ci,
                  PRIMARY KEY (`dispname`, `foreign_id`)
                  ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $sql[] = "CREATE TABLE IF NOT EXISTS accountcode_rules ( 	              
	              id INTEGER NOT NULL PRIMARY KEY $autoincrement,
	              rule VARCHAR( 30 ) COLLATE utf8mb4_unicode_ci,	                                
                  ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        echo "creating tables..";
        foreach ($sql as $q) {
            $check = $db->query($q);
            if(DB::IsError($check)) {
                die_freepbx("Can not create pinset tables\n".$check->getDebugInfo());
            } else {
                echo "done<br>\n";
            }
        }
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
     * @return void
     * @throws Exception
     */
    public function doConfigPageInit($page)
    {
        $action = $this->getReq('action','');
        $id = $this->getReq('id','');
        $name = $this->getReq('name');
        $email = $this->getReq('email');
        $code = $this->getReq('code','');
        $reset = $this->getReq('reset', '');
        $rules = $this->getReq('rules', '');
        $active = $this->getReq('active', '');

        switch ($action) {
            case 'add':
                return $this->addItem($name, $email, $code, $rules, $active);
                break;
            case 'delete':
                return $this->deleteItem($id);
                break;
            case 'edit':
                $this->updateItem($id, $name, $email, $code, $rules, $active);
                break;
        }
    }

    public function addItem($name, $email, $code, $rules, $active)
    {
        //
    }

    public function deleteItem($id)
    {
        //
    }

    public function updateItem($id, $name, $email, $code, $rules, $active)
    {
        //
    }
}