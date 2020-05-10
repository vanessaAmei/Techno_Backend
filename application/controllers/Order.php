<?php 
use chriskacerguis\RestServer\RestController;

Class Order extends RestController {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        parent::__construct();
        $this->load->model('order_model');
        $this->load->library('form_validation');
    }

    public function index_get()
    {
        
        $id = $this->get('id_customer');

        if ($id == '') {
            $query = $this->order_model->getAll();
            echo json_encode($query);
        }else{
            $query = $this->order_model->getById($id);
            echo json_encode($query);
        } 
       
    }

    public function orderStatus_get()
    {
        $id = $this->get('id_customer');

        $query = $this->order_model->getByStatus($id);
        echo json_encode($query);  
    }

    public function penjualan_get(){
        $query = $this->order_model->penjualan();
        echo json_encode($query);
    }

    public function customer_get(){
        $query = $this->order_model->customer();
        $query = count($query);
        echo json_encode($query);
    }

    public function changeJumlah_post()
    {
        $id = $this->post('id_order');
        $jumlah = $this->post('jumlah');
        $harga = $this->post('harga');

        $change = $this->order_model->change_jumlah($id, $jumlah, $harga);
        if($change) {
             $this->response($change, 200);
        }
    }

    public function changeStatus_post()
    {
        $id = $this->post('id_order');
        $status = $this->post('status');

        $change = $this->order_model->change_status($id, $status);
        if($change) {
             $this->response($change, 200);
        }
    }

    public function gambar_post(){
            $order = new orderData();
            $order->id_order = $this->post('id_order');
            $order->gambar = $this->post('gambar');
            $response = $this->order_model->storeImage($order); //Mengakses Fugsi Update dari Model, ini dilakukan jika ID Not null yang berarti update data
            if($response) {
                $this->response($response, 200);
           }
    }

    public function index_post($id = null) //Method Post untuk menyimpan Data namun disini juga disamain untuk update, jadi tidak ada method Put
    {
        $validation = $this->form_validation; //Load Form Validation
        $rule = $this->order_model->rules(); //Mengambil Rules pada Model

        if($id == null) //Jika ID Null yang berarti jika ingin create Data maka Rule nya ini
        {
            array_push($rule, [
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
            ]
            );
        }

        else //Jika ID Not Null yang berarti jika ingin update Data maka Rule nya ini
        {
            array_push($rule, [
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
            ]
            );
        }

        $validation->set_rules($rule); //Form output untuk menunjukkan hasil rule
        if (!$validation->run()) 
        {
            return $this->returnData($this->form_validation->error_array(), true);
        }

        $order = new orderData(); //Dibuatkan Entity untuk tempat menyimpan Data, Tidak wajib dilakukan 
        $order->id_customer = $this->post('id_customer');
        $order->id_barang = $this->post('id_barang');
        $order->jumlah = $this->post('jumlah');
        $order->total = $this->post('total');
        $order->status = $this->post('status');
        $order->alamat = $this->post('alamat');
        $order->no_hp = $this->post('no_hp');
        $order->tanggal = $this->post('tanggal');
        // $order->gambar = NULL;

        if($id == null)
        {
            $response = $this->order_model->store($order); //Mengakses Fugsi Store dari Model, ini dilakukan jika ID null yang berarti create data
        }
        else
        {
            $response = $this->order_model->update($order,$id); //Mengakses Fugsi Update dari Model, ini dilakukan jika ID Not null yang berarti update data
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null) //Method Delete untuk menghapus data, namun pada model telah diubah menjadi Update untuk melakukan Soft Delete
    {
        if($id == null)
        {
            return $this->returnData('ID produk Tidak Ditemukan', true); //Error Exception jika ID nya tidak ditemukan
        }

        $response = $this->order_model->destroy($id); //Mengakses Fugsi Delete dari Model, melakukan Soft Delete
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function returnData($msg,$error) //Fungsi untuk me-return kan Nilai balikkan setelah melakukan Method apapun seperti Error dan Pesan
    {
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class orderData
{
    public $id_customer;
    public $id_barang;
    public $jumlah;
    public $total;
    public $status;
    public $alamat;
    public $no_hp;
    public $tanggal;
    public $gambar;
}