<?php

class CustomSessionHandler //implements SessionHandlerInterface
{
    private $db;
    protected $session = array();
    protected $session_time = 3600;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function __destruct()
    {
        $this->db = NULL;
        session_write_close();
    }

    public function install()
    {
        $req = $this->db->query("SHOW TABLES LIKE sess_table");
        if ($req->rowCount() == 0) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sess_table` (
  `sess_id` char(40) NOT NULL,
  `sess_datas` text NOT NULL,
  `sess_expire` bigint(20) NOT NULL,
  UNIQUE KEY `sess_id` (`sess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        }
    }

    public function open()
    {
        $this->gc();
        return ($this->db instanceof PDO);
    }

    public function close()
    {
        $this->db = NULL;
        return (!($this->db instanceof PDO));
    }

    public function destroy($sid)
    {
        $this->gc();
        $req_destroy = $this->db->prepare('DELETE FROM sess_table WHERE sess_id = ?');
        $bool = $req_destroy->execute(array($sid));
        $req_destroy->closeCursor();
        return $bool;
    }

    public function gc()
    {
        $req_gc = $this->db->query('DELETE FROM sess_table WHERE sess_expire < ' . time());
        $req_optimize = $this->db->query('OPTIMIZE TABLE sess_table');
        return $req_gc AND $req_optimize;
        $req_optimize->closeCursor();
        $req_gc->closeCursor();
    }

    public function create_sid()
    {
        function random_uniq_sud()
        {
            $val = rand(10, 500);
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'$+×÷=%₩;?-\<>{}[]},-";
            srand((double)microtime() * 1000000);
            $i = 0;
            $pass = '';
            while ($i <= $val) {
                $num = rand() % 33;
                $tmp = substr($chars, $num, 1);
                $pass = sha1($num) . $pass . $tmp;
                $i++;
            }
            $num = rand() % 43;
            $sid = substr($pass, $num, 40);
            return $sid;
        }

        $bool = true;
        while ($bool) {
            $sid = random_uniq_sud();
            $req_exist = $this->db->prepare('SELECT sess_id FROM sess_table WHERE sess_id = ?');
            $req_exist->execute(array($sid));
            if ($req_exist->rowCount() == 0) {
                $bool = false;
                return $sid;
            }
        }

    }

    public function read($sid)
    {
        $req_read = $this->db->prepare('SELECT sess_datas FROM sess_table WHERE sess_id = ?');
        $req_read->execute(array($sid));
        if ($req_read->rowCount() == 1) {
            $data = $req_read->fetch();
            return !empty($data['sess_datas']) ? $data['sess_datas'] : false;
        } else {
            return false;
        }
        $req_read->closeCursor();
    }

    public function write($sid, $data)
    {
        $expire = (int)(time() + $this->session_time);
        $req_exist = $this->db->prepare('SELECT sess_id FROM sess_table WHERE sess_id = ?');
        $req_exist->execute(array($sid));
        if ($req_exist->rowCount() == 1) {
            $req_write = $this->db->prepare('UPDATE sess_table SET sess_datas = ?, sess_expire = ? WHERE sess_id = ?');
            $bool = $req_write->execute(array($data, $expire, $sid));
        } else {
            $req_write = $this->db->prepare('INSERT INTO sess_table(sess_id, sess_datas, sess_expire) VALUES(?,?,?)');
            $bool = $req_write->execute(array($sid, $data, $expire));
        }
        $req_exist->closeCursor();
        $req_write->closeCursor();
        return $bool;
    }
}
/*
CREATE TABLE IF NOT EXISTS `sess_table` (
  `sess_id` char(40) NOT NULL,
  `sess_datas` text NOT NULL,
  `sess_expire` bigint(20) NOT NULL,
  UNIQUE KEY `sess_id` (`sess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/




