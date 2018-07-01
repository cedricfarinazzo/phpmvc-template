<?php

class MessageToast
{
    protected $id_message;
    private $data;

    public function __construct($id_message, $datalang)
    {
        $this->id_message = $id_message;
        $this->data = $datalang;
    }

    public function GetMessage()
    {
        if (0 <= $this->id_message && $this->id_message < count($this->data)) {
            $style = $this->data["toast"][$this->id_message]["style"];
            $message = $this->data["toast"][$this->id_message]["text"];
            $text = '<span class="message-toast ' . $style . '" >' . $message . '</span><button class="btn-flat toast-action" onclick="Materialize.Toast.removeAll();" >' . $this->data["toast_hide"] . '</button>';

            $all =
                "<script>
                (function($){
                    var toastMessageContent = '.$text.';
                    Materialize.toast(
                        {
                            html: toastMessageContent,
                            classes: 'rounded'
                        }
                    );
                })(jQuery);
            </script>";
            return $all;
        }
        return false;
    }
}