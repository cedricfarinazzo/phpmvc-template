<?php

class ChatManager
{

    protected $db;

    public function __construct(PDO $db)
    {
        $this->setDb($db);
    }

    //message
    public function post_message($ID_user, $content)
    {
        $req_insert = $this->db->prepare("INSERT INTO chat(ID_user, content, date_post) VALUES(?,?, NOW())");
        $b = $req_insert->execute(array($ID_user, $content));
        return $b;
    }


    //display

    public function lastid()
    {
        $req = $this->db->query("SELECT ID FROM chat ORDER BY ID DESC LIMIT 0,1");
        return $req->rowCount() == 0 ? 0 : $req->fetch()["ID"];
    }

    public function GetLastMessage($id)
    {
        $id = (int)$id;
        /*
        if ($this->lastid() == 0) {
            return json_encode(array(0, ''.PHP_EOL));
        }
        */
        if (0 <= $id AND $id < $this->lastid()) {
            $content = '';
            $req_chat = $this->db->prepare("SELECT * FROM chat WHERE ID > ? AND ID <= ? ORDER BY ID DESC");

            $req_chat->execute(array($id, $this->lastid()));
            while ($data = $req_chat->fetch()) {
                $id = $data["ID"];
                $author_id = $data["ID_user"];
                $message = htmlspecialchars($data["content"]);
                $date = $data["date_post"];
                $author = new UserManager($this->db);
                $author_img = new ImageManager($this->db);
                if ($author->getbyid($author_id)) {
                    $name = $author->name();
                    $firstname = $author->firstname();
                    $email = $author->email();
                    $avatar_id = $author->avatar_path();
                } else {
                    $name = 'Anonyme';
                    $firstname = '';
                    $email = '#" disabled';
                    $avatar_id = 1;
                }
                $token_img = $author_img->GetTokenByID($avatar_id);
                $content .=
                    '<li class="collection-item avatar" id="message-' . $id . '">
					<img src="' . URL_PATH . '/image.php?img=' . urlencode($token_img) . '&larg=200" alt="author-image-profil" class="circle">
					<span class="title col s12 center"><a href="' . URL_PATH . '/?p=user&id=' . urldecode($author->ID()) . '">' . $name . ' ' . $firstname . '</a> le ' . strftime($date) . '</span>
					<p>
						' . $message . '
					</p>
					<a href="mailto: ">Contactez</a>
				</li>' . PHP_EOL;
            }
            return json_encode(array($this->lastid(), base64_encode($content)));
        }
        return json_encode(array($this->lastid(), ''));
    }

    public function display_chat()
    {
        echo '<ul class="collection" id="message-container">' . PHP_EOL;
        $req_chat = $this->db->query("SELECT * FROM chat ORDER BY ID DESC LIMIT 0,50");
        if ($req_chat->rowCount() == 0) {
            echo '
				<li class="collection-item avatar">
					<i class="material-icons">error</i>
					<span class="title col s12 center">Oups !</span>
					<p class="center">
						Pas encore de message ...
					</p>
				</li>  ' . PHP_EOL;
        } else {
            while ($data = $req_chat->fetch()) {
                $id = $data["ID"];
                $author_id = $data["ID_user"];
                $message = htmlspecialchars($data["content"]);
                $date = $data["date_post"];
                $author = new UserManager($this->db);
                $author_img = new ImageManager($this->db);
                if ($author->getbyid($author_id)) {
                    $name = $author->name();
                    $firstname = $author->firstname();
                    $email = $author->email();
                    $avatar_id = $author->avatar_path();
                } else {
                    $name = 'Anonyme';
                    $firstname = '';
                    $email = '#" disabled';
                    $avatar_id = 1;
                }
                $token_img = $author_img->GetTokenByID($avatar_id);
                echo
                    '<li class="collection-item avatar" id="message-' . $id . '">
					<img src="' . URL_PATH . '/image.php?img=' . urlencode($token_img) . '&larg=200" alt="author-image-profil" class="circle">
					<span class="title col s12 center"><a href="' . URL_PATH . '/?p=user&id=' . urldecode($author->ID()) . '">' . $name . ' ' . $firstname . '</a> le ' . strftime($date) . '</span>
					<p>
						' . $message . '
					</p>
					<a href="mailto: ">Contactez</a>
				</li>' . PHP_EOL;
            }
        }
        echo '</ul>' . PHP_EOL;
    }

    public function RemoveAll()
    {
        $req_delete = $this->db->query("TRUNCATE chat");
        $req_ai = $this->db->query("ALTER TABLE chat AUTO_INCREMENT = 1");
        return true;
    }


    private function setDb($db)
    {
        $this->db = $db;
    }
}