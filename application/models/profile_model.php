<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class profile_model extends CI_Model
{
    private $table = 'user'; 

    public $id;
    public $nama;
    public $nohp;
    public $alamat;
    public $email;
    
    
    public $rule = [
        [
            'field' => 'nama',
            'label' => 'nama',
            'rules' => 'required'
        ],

        [
            'field' => 'nohp',
            'label' => 'nohp',
            'rules' => 'required'
        ],

        [
            'field' => 'email',
            'label' => 'email',
            'rules' => 'required'
        ],

        [
            'field' => 'alamat',
            'label' => 'alamat',
            'rules' => 'required'
        ]
    ];
    public function Rules() { return $this->rule; } //Fungsi untuk return nilai rule dimana untuk di cek

    public function getAll() {
        $this->db->select('*');
        $this->db->from('user');
        return $this->db->get()->result_array();
    }

    public function store($request) {   //Fungsi untuk menyimpan data
        $this->nama = $request->nama;   //Gunakan $Request untuk mengambil data yang diinputkan oleh user
        $this->nohp = $request->nohp;
        $this->alamat = $request->alamat;
        $this->email = $request->email;
        if($this->db->insert($this->table, $this))
        {
            return ['msg'=>'Berhasil Input Produk','error'=>false];
        }
            return ['msg'=>'Gagal Input Produk','error'=>true];
    }

    public function update($request,$id) { //Fungsi untuk update data
        $updateData = ['nama' => $request->nama, 
                        'nohp' => $request->nohp, 
                        'alamat' => $request->alamat, 
                        'email' => $request->email]; 
        
        if($this->db->where('id',$id)->update($this->table, $updateData)) 
        {
            return ['msg'=>'Berhasil Update Produk','error'=>false];
        }
            return ['msg'=>'Gagal Update Produk','error'=>true];
    }
       
    public function destroy($id) { 
        
        if(empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) 
            return ['msg' => 'Id tidak ditemukan', 'error'=>true];

        if($this->db->delete($this->table, array('id'=> $id))){
            return ['msg' => 'Berhasil', 'error'=>false];
        }
        return ['msg' => 'Gagal', 'error'=>true];
    }
}
?>