<?php

class PdfManager
{

    protected $pdf_folder = ROOT_PATH . '/data/pdf/';
    protected $db;
    protected $request = false;
    protected $pdf_path = false;

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
        $req_exist = $this->db->prepare("SELECT * FROM pdf WHERE ID = ? AND hash = ?");
        $req_exist->execute(array($id, $hash));
        if ($req_exist->rowCount() == 1) {
            return $req_exist->fetch();
        }
        return false;
    }

    //register
    public function register($path, $name)
    {
        if (file_exists($path)) {
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($extension == "pdf") {
                $Npath = $this->pdf_folder . $name;
                $hash = sha1($Npath);
                move_uploaded_file($path, $Npath);
                $req_insert = $this->db->prepare("INSERT INTO pdf(hash, path) VALUES(?,?)");
                $req_insert->execute(array($hash, $Npath));
                /*$req_id = $this->db->prepare("SELECT * FROM pdf WHERE path = ? AND hash = ?");
                $req_id->execute(array($Npath, $hash));
                if ($req_id->rowCount() == 1) {
                    return (int) $req_id->fetch()["ID"];
                }
                return false;*/
                return $this->db->lastInsertId();
            }
            return false;
        }
        return false;
    }

    //GetToken
    public function GetTokenByID($id)
    {
        $req_exist = $this->db->prepare("SELECT * FROM pdf WHERE ID = ?");
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

    //display
    public function GetPdf($req, $d)
    {
        $this->setRequest($req);
        if ($this->IsValidRequest() != false) {
            if ($this->prepareHeaderData()) {
                if ($d == 1) {
                    return $this->download();
                }
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
            return file_exists($this->pdf_path);
        }
        return false;
    }

    private function display()
    {
        if ($this->pdf_path != NULL) {
            $name = pathinfo($this->pdf_path, PATHINFO_BASENAME);
            header('Content-Type: application/pdf');
            //header('Content-Disposition: attachment; filename="'.$name.'"');
            readfile($this->pdf_path);
        }
        return false;
    }

    private function download()
    {
        if ($this->pdf_path != NULL) {
            $name = pathinfo($this->pdf_path, PATHINFO_BASENAME);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            readfile($this->pdf_path);
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
        $this->pdf_path = $path;
    }

    public function setDb(PDO $db)
    {
        $this->db = $db;
    }
}