<?php

class WebsocketManager
{

    protected $host;
    protected $port;

    protected $socket;

    protected $client;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function init()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
    }

    public function listen()
    {
        socket_bind($this->socket, 0, $this->port);
        socket_listen($this->socket);
        $this->clients = array($this->socket);
    }

    public function action($req)
    {
        $res = "Votai Test.";
        return $res;
    }

    public function start()
    {
        do {

            $socket_new = socket_accept($this->socket);
            $header = socket_read($socket_new, 1024);
            perform_handshaking($header, $socket_new, $this->host, $this->port);
            socket_getpeername($socket_new, $ip);
            echo "Nouvelle connection avec IP : $ip \n";

            while (socket_recv($socket_new, $buf, 1024, 0) >= 1) {
                $msg = $this->unmask($buf);
                $received_msg = "Message received from $ip : $msg \n";
                echo $received_msg;

                $msg = $this->mask($this->action($msg));
                socket_write($socket_new, $msg, strlen($msg));
            }

            $buf = @socket_read($socket_new, 1024, PHP_NORMAL_READ);
            if ($buf === false) {
                echo "Connexion coupée avec IP : $ip \n";
            }
        } while (true);
        socket_close($socket_new);
    }


    /**
     * Get text from socket
     * @param string $text
     */
    function unmask($text)
    {
        $length = ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        return $text;
    }

    /**
     * Encoding message
     * @param unknown $text
     * @return string
     */
    function mask($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);

        if ($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif ($length > 125 && $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif ($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header . $text;
    }

    /**
     * Set header websocket
     * @param unknown $receved_header
     * @param unknown $client_conn
     * @param unknown $host
     * @param unknown $port
     */
    function perform_handshaking($receved_header, $client_conn, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $receved_header);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }

        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n" .
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        @socket_write($client_conn, $upgrade, strlen($upgrade));
    }
}