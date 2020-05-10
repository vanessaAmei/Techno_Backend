<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class produk_model extends CI_Model
{
    private $table = 'barang'; 

    public $id_barang;
    public $kode;
    public $nama;
    public $harga;
    public $stok;
    
    
    public $rule = [
        [
            'field' => 'nama',
            'label' => 'nama',
            'rules' => 'required'
        ],

        [
            'field' => 'harga',
            'label' => 'harga',
            'rules' => 'required'
        ],

        [
            'field' => 'kode',
            'label' => 'kode',
            'rules' => 'required'
        ],

        [
            'field' => 'stok',
            'label' => 'stok',
            'rules' => 'required'
        ]
    ];
    public function Rules() { return $this->rule; } //Fungsi untuk return nilai rule dimana untuk di cek

    public function getAll() {
        $this->db->select('*');
        $this->db->from('barang');
        return $this->db->get()->result_array();
    }

    public function getCountId() {
        $this->db->select_max('id_barang');
        $this->db->from('barang');
        return $this->db->get()->result_array();
    }

    public function store($request) {   //Fungsi untuk menyimpan data
        $this->nama = $request->nama;   //Gunakan $Request untuk mengambil data yang diinputkan oleh user
        $this->harga = $request->harga;
        $this->stok = $request->stok;
        $this->kode = $request->kode;
        if($this->db->insert($this->table, $this))
        {
            return ['msg'=>'Berhasil Input Produk','error'=>false];
        }
            return ['msg'=>'Gagal Input Produk','error'=>true];
    }

    public function update($request,$id) { //Fungsi untuk update data
        $updateData = ['nama' => $request->nama, 
                        'harga' => $request->harga, 
                        'stok' => $request->stok, 
                        'kode' => $request->kode]; 
        
        if($this->db->where('id_barang',$id)->update($this->table, $updateData)) 
        {
            return ['msg'=>'Berhasil Update Produk','error'=>false];
        }
            return ['msg'=>'Gagal Update Produk','error'=>true];
    }
       
    public function destroy($id) { 
        
        if(empty($this->db->select('*')->where(array('id_barang' => $id))->get($this->table)->row())) 
            return ['msg' => 'Id tidak ditemukan', 'error'=>true];

        if($this->db->delete($this->table, array('id_barang'=> $id))){
            return ['msg' => 'Berhasil', 'error'=>false];
        }
        return ['msg' => 'Gagal', 'error'=>true];
    }
}
?>