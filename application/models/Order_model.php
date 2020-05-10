<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class order_model extends CI_Model
{
    private $table = 'pembelian'; 

    public $id_order;
    public $id_customer;
    public $id_barang;
    public $jumlah;
    public $total;
    public $status;
    public $alamat;
    public $no_hp;
    public $tanggal;
    public $gambar;
    
    
    public $rule = [
        [
            'field' => 'id_customer',
            'label' => 'id_customer',
            'rules' => 'required'
        ],

        [
            'field' => 'id_barang',
            'label' => 'id_barang',
            'rules' => 'required'
        ],

        [
            'field' => 'jumlah',
            'label' => 'jumlah',
            'rules' => 'required'
        ],

        [
            'field' => 'total',
            'label' => 'total',
            'rules' => 'required'
        ],
        [
            'field' => 'status',
            'label' => 'status',
            'rules' => 'required'
        ],
        [
            'field' => 'alamat',
            'label' => 'alamat',
            'rules' => 'required'
        ],
        [
            'field' => 'no_hp',
            'label' => 'no_hp',
            'rules' => 'required'
        ],
        [
            'field' => 'tanggal',
            'label' => 'tanggal',
            'rules' => 'required'
        ],
        [
            'field' => 'gambar',
            'label' => 'gambar',
            'rules' => ''
        ],
    ];
    public function Rules() { return $this->rule; } //Fungsi untuk return nilai rule dimana untuk di cek

    public function getAll() {
        $this->db->select('a.id_order as "id_order", c.nama as "customer", b.nama as "nama", b.harga as "harga", a.jumlah as "jumlah", 
        a.total as "total", a.status as "status", a.alamat as "alamat", a.no_hp as "no_hp", a.tanggal as "tanggal", a.gambar as "gambar"');
        $this->db->from('pembelian a');
        $this->db->join('barang b', 'id_barang');
        $this->db->join('user c', 'a.id_customer=c.id');
        return $this->db->get()->result_array();
    }

    public function getById($id)
    {
        $this->db->select('a.id_order as "id_order", b.nama as "nama", b.harga as "harga", a.jumlah as "jumlah", 
        a.total as "total", a.status as "status", a.alamat as "alamat", a.no_hp as "no_hp", a.tanggal as "tanggal", a.gambar as "gambar"');
        $this->db->from('pembelian a'); 
        $this->db->join('barang b', 'id_barang');
        $this->db->where('id_customer', $id);
        return $this->db->get()->result_array();
    }

    public function getByStatus($id)
    {
        $this->db->select('a.id_order as "id_order", b.nama as "nama", b.harga as "harga", a.jumlah as "jumlah", 
        a.total as "total", a.status as "status", a.alamat as "alamat", a.no_hp as "no_hp", a.tanggal as "tanggal", a.gambar as "gambar"');
        $this->db->from('pembelian a'); 
        $this->db->join('barang b', 'id_barang');
        $this->db->where('id_customer', $id);
        $this->db->where('status=', 'Waiting For Confirmation');
        return $this->db->get()->result_array();
    }

    public function change_jumlah($id, $new_jumlah, $harga)
    {
        $update=$this->db->query("UPDATE pembelian SET jumlah='$new_jumlah', total=($harga*$new_jumlah)  where id_order='$id'");
        if($update){
            return ['msg'=>'Berhasil Update Jumlah','error'=>false];
        }
    }

    public function penjualan(){
        $query = "SELECT b.nama as 'Produk', a.jumlah as 'Quantity', (SELECT SUM(a.total) FROM pembelian a WHERE a.status='Done') as 'total' FROM pembelian a JOIN barang b ON a.id_barang=b.id_barang WHERE a.status='Done' GROUP BY b.nama LIMIT 1";
        $result = $this->db->query($query);
        return $result->result();  
    }

    public function customer(){
        $query="SELECT id_customer FROM pembelian GROUP BY id_customer";
        $result = $this->db->query($query);
        return $result->result();
    }

    public function change_status($id,$new_status)
    {
        $update=$this->db->query("UPDATE pembelian SET status='$new_status' where id_order='$id'");
        if($update){
            return ['msg'=>'Berhasil Update Status','error'=>false];
        }
    }

    public function store($request) {   //Fungsi untuk menyimpan data
        $this->id_customer = $request->id_customer;   //Gunakan $Request untuk mengambil data yang diinputkan oleh user
        $this->id_barang = $request->id_barang;
        $this->jumlah = $request->jumlah;
        $this->total = $request->total;
        $this->status = $request->status;
        $this->alamat = $request->alamat;
        $this->no_hp = $request->no_hp;
        $this->tanggal = $request->tanggal;
        $this->gambar = "default.jpg";
        if($this->db->insert($this->table, $this))
        {
            return ['msg'=>'Berhasil Input Pembelian','error'=>false];
        }
            return ['msg'=>'Gagal Input Pembelian','error'=>true];
    }

    public function update($request,$id) { //Fungsi untuk update data
        $updateData = ['id_customer' => $this->id_customer, 
                        'id_barang' => $request->id_barang, 
                        'jumlah' => $request->jumlah, 
                        'total' => $request->total,
                        'status' => $request->status,
                        'alamat' => $request->alamat,
                        'no_hp' => $request->no_hp,
                        'tanggal' => $request->tanggal]; 
        
        if($this->db->where('id_order',$id)->update($this->table, $updateData)) 
        {
            return ['msg'=>'Berhasil Update Pembelian','error'=>false];
        }
            return ['msg'=>'Gagal Update Pembelian','error'=>true];
    }
       
    public function destroy($id) { 
        
        if(empty($this->db->select('*')->where(array('id_order' => $id))->get($this->table)->row())) 
            return ['msg' => 'Id tidak ditemukan', 'error'=>true];

        if($this->db->delete($this->table, array('id_order'=> $id))){
            return ['msg' => 'Berhasil', 'error'=>false];
        }
        return ['msg' => 'Gagal', 'error'=>true];
    }

    public function storeImage($request) {   //Fungsi untuk menyimpan data
        $this->id_order = $request->id_order;
        $this->gambar = $this->_uploadImage();
        $updateData = ['gambar' => $this->gambar]; 
        
        if($this->db->where('id_order', $this->id_order)->update($this->table, $updateData)) 
        {
            // print_r($this->_uploadImage());
            return ['msg'=>'Berhasil Tambah Gambar','error'=>false];
            
        }
            return ['msg'=>'Gagal Tambah Gambar','error'=>true];
    }

    private function _uploadImage()
    {
        
        $config['upload_path']          = './upload/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['file_name']            = $this->id_order;
        $config['overwrite']			= true;
        $config['max_size']             = 1024; // 1MB
    //     $config['max_width']            = 500;
    // $config['max_height']               = 500;

        $this->load->library('upload', $config);

        if ( !$this->upload->do_upload('gambar'))
        {
                $error = array('error' => $this->upload->display_errors());
                print_r($config['upload_path']);
                return "default.jpg";
        }
        else
        {
                return $this->upload->data("file_name");
        }
        
    }
}
?>