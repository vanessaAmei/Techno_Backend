<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Send_email extends CI_Controller
{
    public function index()
    {
        $config = [
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_user' => 'tinovaneffendi9867@gmail.com',
            'smtp_pass' => 'al8g8t7y',
            'smtp_port' => 465,
            'crlf'      => "\r\n",
            'newline'   => "\r\n"
        ];
        $this->load->library('email', $config);
        $this->email->from('no-reply@xBanana.com', 'Vychomena Team');
        $this->email->to('nikokevin29@gmail.com');
        $this->email->subject('Verify Your email for access');
        $this->email->message("Hi new Customer, please verify your account to grant all access as a user");
        if ($this->email->send()) {
            echo 'Sukses! email berhasil dikirim.';
        } else {
            echo 'Error! email tidak dapat dikirim.';
        }
    }
}
