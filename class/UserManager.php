<?php

class UserManager
{

    private $db;
    protected $ID;
    protected $login;
    protected $name;
    protected $firstname;
    protected $pass;
    protected $email;
    protected $description;
    protected $date_register;
    protected $avatar_path;
    protected $rank;

    const TIMEOUT = 3600;
    const TIMEOUTCOOKIE = 31536000;

    public function __construct(PDO $db, $data = NULL)
    {
        $this->setDb($db);
        if ($data != NULL) {
            $this->hydrate($data);
        }
    }

    public function __destruct()
    {
        $this->db = NULL;
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function connect($mail, $pass)
    {
        $req_connect = $this->db->prepare('SELECT * FROM user WHERE email = ? AND pass = ?');
        $req_connect->execute(array($mail, $pass));
        if ($req_connect->rowCount() == 1) {
            $this->hydrate($req_connect->fetch());
            $token = $this->GetToken('sess');
            if ($this->exist($this->ID(), 'authsess') == 0) {
                $_SESSION['_authsess'] = $token;
                setcookie('_authsess', $token, time() + self::TIMEOUT, '/', null, false, true);
                $req = $this->db->prepare('INSERT INTO authsess(ID_user, token, IP, PHPSESSID, expire) VALUES(?,?,?,?,?)');
                $b = $req->execute(array($this->ID(), $token, $_SERVER['REMOTE_ADDR'], session_id(), time() + self::TIMEOUT));
            } else {
                $_SESSION['_authsess'] = $token;
                setcookie('_authsess', $token, time() + self::TIMEOUT, '/', null, false, true);
                $req = $this->db->prepare('UPDATE authsess SET token= ?, IP = ?, PHPSESSID = ?, expire = ? WHERE ID_user = ?');
                $b = $req->execute(array($token, $_SERVER['REMOTE_ADDR'], session_id(), time() + self::TIMEOUT, $this->ID()));
            }
            return $b;
        }
        return false;
    }

    public function remember()
    {
        $token = $this->GetToken('cookie');
        $this->gc('authcookieremember');
        if ($this->exist($this->ID(), 'authcookieremember') == 0) {
            setcookie('_authcookie-remember', $token, time() + self::TIMEOUTCOOKIE, '/', null, false, true);
            $req_remember = $this->db->prepare('INSERT INTO authcookieremember(ID_user, token, expire) VALUES(?,?,?)');
            $b = $req_remember->execute(array($this->ID(), $token, time() + self::TIMEOUTCOOKIE));
            return $b;
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id(true);
        $req_disco = $this->db->prepare('DELETE FROM authsess WHERE ID_user = ?');
        $a = $req_disco->execute(array($this->ID()));
        setcookie('_authsess', '', time() - 10, '/', null, false, true);
        $req_disco_cookie = $this->db->prepare('DELETE FROM authcookieremember WHERE ID_user = ?');
        $b = $req_disco_cookie->execute(array($this->ID()));
        setcookie('_authcookie-remember', '', time() - 10, '/', null, false, true);
        $this->gc('authcookieremember');
        $this->gc('authsess');
        return $a and $b;
    }

    public function cookie_connect($token)
    {
        $token_arr = explode('=-=-', $token);
        $ID_user = (int)$token_arr[0];
        if ($ID_user > 0) {
            $this->gc('authcookieremember');
            $req_verify_sess = $this->db->prepare('SELECT * FROM authcookieremember WHERE ID_user = ? AND token = ? AND expire > ?');
            $req_verify_sess->execute(array($ID_user, $token, time()));
            if ($req_verify_sess->rowCount() == 1) {
                $time_expire = $req_verify_sess->fetch()['expire'];
                $req_connect = $this->db->prepare('SELECT * FROM user WHERE ID = ?');
                $req_connect->execute(array($ID_user));
                if ($req_connect->rowCount() == 1) {
                    $data = $req_connect->fetch();
                    $id = $data['ID'];
                    $name = $data['name'];
                    $email = $data["email"];
                    if ($this->verifToken($id, $name, $email, $time_expire - self::TIMEOUTCOOKIE, "cookie", $token)) {
                        return $this->connect($email, $data["pass"]);
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;

    }

    public function register($name, $firstname, $pass, $mail, $login)
    {
        $req = $this->db->prepare('SELECT * FROM user WHERE email = ?');
        $req->execute(array($mail));
        if ($req->rowCount() == 0) {
            $req = $this->db->prepare('INSERT INTO user(name, firstname, email, pass, login, date_register) VALUES(?, ?, ?, ?, ?, NOW())');
            $req->execute(array($name, $firstname, $mail, $pass, $login));
            $req->closeCursor();
            $req = $this->db->prepare('SELECT * FROM user WHERE email = ? AND pass = ?');
            $req->execute(array($mail, $pass));
            $data = $req->fetch();
            $this->hydrate($data);
            return $this->connect($mail, $pass);
        }
        return false;
    }

    public function update($name, $firstname, $pass, $mail, $description, $avatar_path, $login)
    {
        if ($pass == NULL) {
            $pass = $this->pass;
        }
        $req = $this->db->prepare('SELECT * FROM user WHERE email = ? AND ID <> ?');
        $req->execute(array($mail, $this->ID()));
        if ($req->rowCount() == 0) {
            $req_update = $this->db->prepare("UPDATE user SET name = ?, firstname = ?, pass = ?, login = ?,email = ?, description = ?, avatar_path = ?  WHERE ID = ?");
            $req_update->execute(array($name, $firstname, $pass, $login, $mail, $description, $avatar_path, $this->ID()));
            return true;
        }
        return false;
    }

    public function delete_user($pass)
    {
        $req_user_verify = $this->db->prepare('SELECT * FROM user WHERE ID = ? AND pass = ?');
        $req_user_verify->execute(array($this->ID(), $pass));
        if ($req_user_verify->rowCount() == 1) {
            $req_delete = $this->db->prepare('DELETE FROM user WHERE ID = ?');
            $req_delete->execute(array($this->ID()));
            return $this->logout();
        }
        return false;
    }

    public function verifSess()
    {
        if (isset($_SESSION['_authsess']) AND !empty($_SESSION['_authsess']) AND isset($_COOKIE['_authsess']) AND !empty($_COOKIE['_authsess']) AND $_SESSION['_authsess'] == $_COOKIE['_authsess']) {
            $token = explode('=-=-', $_SESSION['_authsess']);
            $ID_user = (int)$token[0];
            $req_verify_sess = $this->db->prepare('SELECT * FROM authsess WHERE ID_user = ? AND token = ? AND IP = ? AND PHPSESSID = ? AND expire > ?');
            $req_verify_sess->execute(array($ID_user, $_SESSION['_authsess'], $_SERVER['REMOTE_ADDR'], session_id(), time()));
            $time_expire = $req_verify_sess->fetch()['expire'];
            if ($req_verify_sess->rowCount() == 1) {
                $req_user_sess = $this->db->prepare('SELECT * FROM user WHERE ID = ?');
                $req_user_sess->execute(array($ID_user));
                if ($req_user_sess->rowCount() == 1) {
                    $data = $req_user_sess->fetch(PDO::FETCH_ASSOC);
                    $id = $data['ID'];
                    $name = $data['name'];
                    $email = $data["email"];
                    if ($this->verifToken($id, $name, $email, $time_expire - self::TIMEOUT, "sess", $_SESSION['_authsess'])) {
                        $this->hydrate($data);
                        $token = $this->GetToken('sess');
                        $_SESSION['_authsess'] = $token;
                        $req_update = $this->db->prepare('UPDATE authsess SET expire = ? , token = ? WHERE ID_user = ?');
                        $req_update->execute(array(time() + self::TIMEOUT, $token, $this->ID()));
                        return setcookie('_authsess', $token, time() + self::TIMEOUT, '/', null, false, true);;
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function refresh()
    {
        if ((int)$this->ID > 0) {
            $req_refresh = $this->db->prepare("SELECT * FROM user WHERE ID = ?");
            $req_refresh->execute(array($this->ID()));
            $data = $req_refresh->fetch();
            if (is_array($data)) {
                $this->hydrate($data);
                return true;
            }
            return false;
        }
        return false;
    }

    public function exist($data, $table)
    {
        if (is_int($data) AND is_string($table)) {
            if ($table == 'user') {
                $req_exist = $this->db->prepare('SELECT * FROM user WHERE ID = ?');
            } else {
                $req_exist = $this->db->prepare('SELECT * FROM ' . $table . ' WHERE ID_user = ?');
            }
            $req_exist->execute(array((int)$data));
            return $req_exist->rowCount();
        }
    }

    public function getbyid($id)
    {
        if ((int)$id > 0) {
            $req_get = $this->db->prepare('SELECT * FROM user WHERE ID = ?');
            $req_get->execute(array($id));
            if ($req_get->rowCount() == 1) {
                $this->hydrate($req_get->fetch());
                return true;
            }
            return false;
        }
        return false;
    }

    public static function GetUserList()
    {
        global $db;
        $req = $db->query('SELECT * FROM user');
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'UserManager', array($db));
        return $req;
    }

    public function GetToken($service = "")
    {
        return $this->ID() . "=-=-" . hash("sha256", $this->name . $this->email . time() . $service) . sha1($this->ID());
    }

    public function verifToken($id, $name, $email, $time, $service, $old_token)
    {
        $token = $id . "=-=-" . hash("sha256", $name . $email . $time . $service) . sha1($id);
        return $token == $old_token;
    }

    public function gc($table)
    {
        $req_gc = $this->db->prepare('DELETE FROM ' . $table . ' WHERE expire < ?');
        $req_gc->execute(array(time()));
        $req_gc->execute(array(time()));
        $req_optimize = $this->db->query('OPTIMIZE TABLE ' . $table);
        return $req_gc AND $req_optimize;
    }

    //admin
    public function promote($id)
    {
        if ($this->rank == "admin" || $this->rank == "webmaster") {
            $req_exist = $this->db->prepare("SELECT * FROM user WHERE ID = ? && rank = ?");
            $req_exist->execute(array($id, "user"));
            if ($req_exist->rowCount() == 1) {
                $req_update = $this->db->prepare("UPDATE user SET rank = ? WHERE ID = ?");
                $b = $req_update->execute(array("admin", $id));
                return $b;
            }
            return false;
        }
        return false;
    }

    public function degrade($id)
    {
        if ($this->rank == "webmaster") {
            $req_exist = $this->db->prepare("SELECT * FROM user WHERE ID = ? && rank = ?");
            $req_exist->execute(array($id, "admin"));
            if ($req_exist->rowCount() == 1) {
                $data = $req_exist->fetch();
                $rank = $data["rank"];
                if ($rank == "user") {
                    return false;
                }
                if ($rank == "admin") {
                    $Newrank = "user";
                }
                if ($rank == "webmaster") {
                    $Newrank = "admin";
                }
                $req_update = $this->db->prepare("UPDATE user SET rank = ? WHERE ID = ?");
                $b = $req_update->execute(array($Newrank, $id));
                return $b;
            }
            return false;
        }
        return false;
    }

    //Getters and setters

    public function setDb(PDO $db)
    {
        $this->db = $db;
    }

    public function setId($id)
    {
        $id = (int)$id;
        if ($id > 0) {
            $this->ID = $id;
        }
    }

    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;
        }
    }

    public function setLogin($name)
    {
        if (is_string($name)) {
            $this->login = $name;
        }
    }

    public function setFirstname($name)
    {
        if (is_string($name)) {
            $this->firstname = $name;
        }
    }

    public function setPass($pass)
    {
        if (is_string($pass)) {
            $this->pass = $pass;
        }
    }

    public function setEmail($mail)
    {
        if (is_string($mail)) {
            $this->email = $mail;
        }
    }

    public function setAvatar_path($path)
    {
        $this->avatar_path = $path;
    }

    public function setDescription($data)
    {
        if (is_string($data)) {
            $this->description = str_replace("<br />", "/n", $data);
        }
    }

    public function setDate_register($date)
    {
        if (is_string($date)) {
            $this->date_register = $date;
        }
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    public function ID()
    {
        return (int)$this->ID;
    }

    public function name($service = NULL)
    {
        if ($service == 'input') {
            return str_replace(["<", "script", "/>", ">"], "", $this->name);
        }
        if ($service == 'nochange') {
            return ($this->name);
        }
        return htmlspecialchars($this->name);
    }

    public function login($service = NULL)
    {
        if ($service == 'input') {
            return str_replace(["<", "script", "/>", ">"], "", $this->login);
        }
        if ($service == 'nochange') {
            return ($this->login);
        }
        return htmlspecialchars($this->login);
    }

    public function firstname($service = NULL)
    {
        if ($service == 'input') {
            return str_replace(["<", "script", "/>", ">"], "", $this->firstname);
        }
        if ($service == 'nochange') {
            return ($this->firstname);
        }
        return htmlspecialchars($this->firstname);
    }

    public function pass()
    {
        return $this->pass;
    }

    public function email($service = NULL)
    {
        if ($service == 'input') {
            return str_replace(["<", "script", "/>", ">"], "", $this->email);
        }
        if ($service == 'nochange') {
            return ($this->email);
        }
        return htmlspecialchars($this->email);
    }

    public function avatar_path()
    {
        return ($this->avatar_path);
    }

    public function description($service = NULL)
    {
        if ($service == 'input') {
            return (str_replace(["<", "script", "/>", ">"], "", $this->description));
        }
        if ($service == 'nochange') {
            return ($this->description);
        }
        return nl2br(htmlspecialchars($this->description));
    }

    public function date_register()
    {
        return strftime($this->date_register);
    }

    public function rank()
    {
        return $this->rank;
    }
}