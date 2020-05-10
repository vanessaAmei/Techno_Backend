<?php
defined('BASEPATH') or exit('No direct script access allowed');
class login_model extends CI_Model
{
    private $table = 'admin';
    public $id;

    public $username;
    public $password;
    public $rule = [
        [
            'field' => 'username',
            'label' => 'username',
            'rules' => 'required',
        ],
    ];

    public function Rules()
    {
        return $this->rule;
    }

    public function getAll()
    {
        return
            $this->db->get('pegawai')->result();
    }

    public function store($request)
    {
        $this->username = $request->username;
        $this->password = $request->password;
        if ($this->db->insert($this->table, $this)) {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

    public function update($request, $id)
    {
        $updateData = ['username' => $request->username];
        if ($this->db->where('id', $id)->update($this->table, $updateData)) {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

    public function destroy($id)
    {
        if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) return ['msg' => 'Id tidak ditemukan', 'error' => true];

        if ($this->db->delete($this->table, array('id' => $id))) {
            return ['msg' => 'Berhasil', 'error' => false];
        }
        return ['msg' => 'Gagal', 'error' => true];
    }

    public function verifyUser($request)
    {
        $pegawai = $this->db->select('*')->where(array('username' => $request->username, 'password' => $request->password))->get($this->table)->row_array();
        if(!empty($pegawai) ){
            return $pegawai;          
        }else{
            return false;
        }
    }
}
?>