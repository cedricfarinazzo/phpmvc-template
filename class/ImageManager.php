<?php

class ImageManager
{

    protected $img_folder = ROOT_PATH . '/data/image/';
    protected $db;
    protected $request = false;
    protected $img_path = false;
    protected $img = NULL;
    protected $imgredim = NULL;
    protected $extension = false;

    public function __construct(PDO $db)
    {
        $this->setDb($db);
    }

    public function __destruct()
    {
        $this->db = NULL;
    }

    public function exist($id, $hash)
    {

        $req_exist = $this->db->prepare("SELECT * FROM image WHERE ID = ? AND hash = ?");
        $req_exist->execute(array($id, $hash));
        if ($req_exist->rowCount() == 1) {
            return $req_exist->fetch();
        }
        return false;
    }

    //init table
    public function IsEmpty()
    {
        $req_count = $this->db->query("SELECT * FROM image");
        return $req_count->rowCount() == 0;
    }

    public function AddDefault()
    {
        $req_trunc = $this->db->query("TRUNCATE TABLE image");
        $req_ai = $this->db->query("ALTER TABLE image AUTO_INCREMENT = 1");
        $files = array_diff(scandir($this->img_folder), array('..', '.'));
        foreach ($files as $f) {
            $path = $this->img_folder . $f;
            $hash = sha1($path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $req_insert = $this->db->prepare("INSERT INTO image(hash, path, extension) VALUES(?,?,?)");
            $b = $req_insert->execute(array($hash, $path, $extension));
        }
        return true;
    }

    //register
    public function register($path, $name = "")
    {
        if (file_exists($path)) {
            $legalExtensions = array("jpg", "png", "gif", "jpeg");
            $legalSize = "10000000";
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $actualSize = filesize($path);
            if ($actualSize > 0 AND $actualSize < $legalSize) {
                if (in_array($extension, $legalExtensions)) {
                    $newName = bin2hex(random_bytes(64));
                    while (file_exists($this->img_folder . '/' . $newName . '.' . $extension)) {
                        $newName = bin2hex(random_bytes(64));
                    }
                    $Npath = $this->img_folder . '/' . $newName . '.' . $extension;
                    $hash = sha1($Npath);
                    move_uploaded_file($path, $Npath);
                    $req_insert = $this->db->prepare("INSERT INTO image(hash, path, extension) VALUES(?,?,?)");
                    $req_insert->execute(array($hash, $Npath, $extension));
                    $req_id = $this->db->prepare("SELECT * FROM image WHERE path = ? AND hash = ?");
                    $req_id->execute(array($Npath, $hash));
                    if ($req_id->rowCount() == 1) {
                        return (int)$req_id->fetch()["ID"];
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    //GetToken
    public function GetTokenByID($id)
    {
        $req_exist = $this->db->prepare("SELECT * FROM image WHERE ID = ?");
        $a = $req_exist->execute(array((int)$id));
        if ($req_exist->rowCount() == 1) {
            $data = $req_exist->fetch();
            return $this->GenToken($data["ID"], $data["hash"]);
        }
    }

    //GenToken
    private function GenToken($id, $hash)
    {
        return $id . "=-=" . $hash;
    }

    //redimmension
    public function redimUp($NouvelleLargeur = 200)
    {
        switch ($this->extension) {
            case "gif":
                $this->img = imagecreatefromgif($this->img_path);
                break;
            case "png":
                $this->img = imagecreatefrompng($this->img_path);
                break;
            case "jpeg":
                $this->img = imagecreatefromjpeg($this->img_path);
                break;
            case "jpg":
                $this->img = imagecreatefromjpeg($this->img_path);
                break;
            default:
                return false;
        }
        $taille = getimagesize($this->img_path);
        $NouvelleHauteur = (($taille[1] * (($NouvelleLargeur) / $taille[0])));
        $NouvelleImage = imagecreatetruecolor($NouvelleLargeur, $NouvelleHauteur) or die ("Erreur");
        imagecopyresampled($NouvelleImage, $this->img, 0, 0, 0, 0, $NouvelleLargeur, $NouvelleHauteur, $taille[0], $taille[1]);
        /*var_dump($taille);
        var_dump(imagesx($NouvelleImage));
        var_dump(imagesy($NouvelleImage));*/
        $this->imgredim = $NouvelleImage;
        return true;
    }

    //display
    public function GetImg($req, $larg = 200)
    {
        $this->setRequest($req);
        if ($this->IsValidRequest() != false) {
            if ($this->prepareHeaderData()) {
                $taille = getimagesize($this->img_path);
                $this->redimUp($larg);
                return $this->display();
            }
            return false;
        }
        return false;
    }

    private function IsValidRequest()
    {
        $req = explode("=-=", $this->request);
        $id = (int)$req[0];
        $hash = $req[1];
        return $this->exist($id, $hash);
    }

    private function prepareHeaderData()
    {
        if (($data = $this->IsValidRequest()) != false) {
            $this->setPath($data["path"]);
            $this->setExtension($data["extension"]);
            return file_exists($this->img_path);
        }
        return false;
    }

    private function display()
    {
        switch ($this->extension) {
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
                $ctype = "image/jpeg";
                break;
            case "jpg":
                $ctype = "image/jpeg";
                break;
            default:
        }
        if ($this->img_path != NULL && $this->img != NULL) {
            header('Content-type: ' . $ctype);
            switch ($this->extension) {
                case "gif":
                    return imagegif($this->imgredim);
                    break;
                case "png":
                    return imagepng($this->imgredim);
                    break;
                case "jpeg":
                    return imagejpeg($this->imgredim);
                    break;
                case "jpg":
                    return imagejpeg($this->imgredim);
                    break;
                default:
            }
        }
        return false;
    }

    //getters and setters
    public function setRequest($req)
    {
        $this->request = $req;
    }

    public function setPath($path)
    {
        $this->img_path = $path;
    }

    public function setExtension($ext)
    {
        $this->extension = $ext;
    }

    public function setDb(PDO $db)
    {
        $this->db = $db;
    }
}