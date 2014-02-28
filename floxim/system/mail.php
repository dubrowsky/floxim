<?php
class fx_system_mail {

    protected $default_from = '';
    protected $default_from_name = '';
    protected $default_reply = '';
    protected $charset = 'UTF-8';
    protected $priority = 3;
    protected $boundary = "--=_NetxPart_F_L_O_X_I_M_sMdAdsf0sGdAsfDAfN";
    protected $body_plain = '';
    protected $body_html = '';
    protected $from, $name, $reply;
    protected $is_html = false;

    public function set_body($plain, $html = "") {
        $this->body_plain = $plain;
        $this->body_html = $html;
        $this->is_html = $html ? true : false;
        return $this;
    }

    function send($to, $subject, $from = '', $reply = '', $from_name = '') {
        $this->from = $from ? $from : $this->default_from;
        $this->reply = $reply ? $reply : $this->default_reply;
        $this->name = $from_name ? $from_name : $this->default_from_name;

        return mail($to, $this->encode_header($subject), $this->makebody(), $this->makeheader());
    }

    protected function encode_header($str) {
        return '=?'.$this->charset.'?B?'.base64_encode($str).'?=';
    }

    protected function makebody() {
        $out = "";

        if ($this->is_html) {
            $out .= "--".$this->boundary."\n";
            $out .= "Content-type: text/plain;charset=\"".$this->charset."\"\n";
            $out .= "Content-Transfer-Encoding: quoted-printable\n\n".$this->quoted_printable_encode($this->body_plain)."\r\n\r\n";
            $out .= "--".$this->boundary."\n";
            $out .= "Content-type: text/html;charset=\"".$this->charset."\"\n";
            $out .= "Content-Transfer-Encoding: quoted-printable\n\n".$this->quoted_printable_encode($this->body_html)."\r\n\r\n";
            $out .= "--".$this->boundary."--\n";
        } else {
            $out .= $this->quoted_printable_encode($this->body_plain)."\n";
        }

        return $out;
    }

    protected function makeheader() {
        $out = "From: ".$this->encode_header($this->name)." <".$this->from.">\n";
        $out .= "Reply-To: <".$this->reply.">\n";
        $out .= "Return-Path: <".$this->reply.">\n";
        $out .= "MIME-Version: 1.0\n";

        if ($this->is_html) {
            $out .= "Content-Type: multipart/alternative;\n boundary=\"".$this->boundary."\"\n";
        } else {
            $out.= "Content-Type: text/plain; charset=".$this->charset."\nContent-Transfer-Encoding: quoted-printable\n";
        }

        $out .= "X-Priority: ".$this->priority."\n";

        return $out;
    }

    protected function quoted_printable_encode($str) {
        $tohex = '"=".strtoupper(dechex(ord("$1")))';

        $str = preg_replace('/([^\x09\x20\x0D\x0A\x21-\x3C\x3E-\x7E])/e', $tohex, $str);
        // encode x20, x09 at the end of lines
        $str = preg_replace("/([\x20\x09])(\r?\n)/e", $tohex.'$2', $str);
        $str = str_replace("\r", "", $str);

        // split into chunks
        // Из-за разбиения строки по RFC (=CRLF) возникают "лишние" переносы строк на некоторых почтовых серверах
        $lines = explode("\n", $str);
        foreach ($lines as $num => $line) {
            if (strlen($line) > 76) {
                preg_match_all('/.{1,73}([^=]{0,2})?/u', $line, $regs);
                $lines[$num] = join("=\n", $regs[0]);
            }
        }
        $str = join("\n", $lines);

        return $str;
    }

}