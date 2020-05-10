<?php 
use chriskacerguis\RestServer\RestController;

Class Produk extends RestController {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        parent::__construct();
        $this->load->model('produk_model');
        $this->load->library('form_validation');
    }

    public function index_get()
    {
        
        $query = $this->produk_model->getAll();
        echo json_encode($query);
       
    }

    public function count_get(){
        $query = $this->produk_model->getCountId();
        echo json_encode($query);
    }

    public function index_post($id = null) //Method Post untuk menyimpan Data namun disini juga disamain untuk update, jadi tidak ada method Put
    {
        $validation = $this->form_validation; //Load Form Validation
        $rule = $this->produk_model->rules(); //Mengambil Rules pada Model

        if($id == null) //Jika ID Null yang berarti jika ingin create Data maka Rule nya ini
        {
            array_push($rule, [
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
            );
        }

        else //Jika ID Not Null yang berarti jika ingin update Data maka Rule nya ini
        {
            array_push($rule, [
                'field' => 'nama',
                'label' => 'nama',
                'rules' => ''
            ],
    
            [
                'field' => 'harga',
                'label' => 'harga',
                'rules' => ''
            ],
    
            [
                'field' => 'kode',
                'label' => 'kode',
                'rules' => ''
            ],
    
            [
                'field' => 'stok',
                'label' => 'stok',
                'rules' => ''
            ]
            );
        }

        $validation->set_rules($rule); //Form output untuk menunjukkan hasil rule
        if (!$validation->run()) 
        {
            return $this->returnData($this->form_validation->error_array(), true);
        }

        $produk = new produkData(); //Dibuatkan Entity untuk tempat menyimpan Data, Tidak wajib dilakukan 
        $produk->nama = $this->post('nama');
        $produk->harga = $this->post('harga');
        $produk->kode = $this->post('kode');
        $produk->stok = $this->post('stok');

        if($id == null)
        {
            $response = $this->produk_model->store($produk); //Mengakses Fugsi Store dari Model, ini dilakukan jika ID null yang berarti create data
        }
        else
        {
            $response = $this->produk_model->update($produk,$id); //Mengakses Fugsi Update dari Model, ini dilakukan jika ID Not null yang berarti update data
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null) //Method Delete untuk menghapus data, namun pada model telah diubah menjadi Update untuk melakukan Soft Delete
    {
        if($id == null)
        {
            return $this->returnData('ID produk Tidak Ditemukan', true); //Error Exception jika ID nya tidak ditemukan
        }

        $response = $this->produk_model->destroy($id); //Mengakses Fugsi Delete dari Model, melakukan Soft Delete
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function returnData($msg,$error) //Fungsi untuk me-return kan Nilai balikkan setelah melakukan Method apapun seperti Error dan Pesan
    {
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class produkData
{
    public $id_barang;
    public $nama;
    public $harga;
    public $kode;
    public $stok;
}