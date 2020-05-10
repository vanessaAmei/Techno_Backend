<?php 
use chriskacerguis\RestServer\RestController;

Class Profile extends RestController {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        parent::__construct();
        $this->load->model('profile_model');
        $this->load->library('form_validation');
    }

    public function index_get()
    {
        
        $query = $this->profile_model->getAll();
        echo json_encode($query);
       
    }

    public function index_post($id = null) //Method Post untuk menyimpan Data namun disini juga disamain untuk update, jadi tidak ada method Put
    {
        $validation = $this->form_validation; //Load Form Validation
        $rule = $this->profile_model->rules(); //Mengambil Rules pada Model

        if($id == null) //Jika ID Null yang berarti jika ingin create Data maka Rule nya ini
        {
            array_push($rule, [
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
                'field' => 'nohp',
                'label' => 'nohp',
                'rules' => ''
            ],
    
            [
                'field' => 'email',
                'label' => 'email',
                'rules' => ''
            ],
    
            [
                'field' => 'alamat',
                'label' => 'alamat',
                'rules' => ''
            ]
            );
        }

        $validation->set_rules($rule); //Form output untuk menunjukkan hasil rule
        if (!$validation->run()) 
        {
            return $this->returnData($this->form_validation->error_array(), true);
        }

        $profile = new profileData(); //Dibuatkan Entity untuk tempat menyimpan Data, Tidak wajib dilakukan 
        $profile->nama = $this->post('nama');
        $profile->nohp = $this->post('nohp');
        $profile->email = $this->post('email');
        $profile->alamat = $this->post('alamat');

        if($id == null)
        {
            $response = $this->profile_model->store($profile); //Mengakses Fugsi Store dari Model, ini dilakukan jika ID null yang berarti create data
        }
        else
        {
            $response = $this->profile_model->update($profile,$id); //Mengakses Fugsi Update dari Model, ini dilakukan jika ID Not null yang berarti update data
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null) //Method Delete untuk menghapus data, namun pada model telah diubah menjadi Update untuk melakukan Soft Delete
    {
        if($id == null)
        {
            return $this->returnData('ID profile Tidak Ditemukan', true); //Error Exception jika ID nya tidak ditemukan
        }

        $response = $this->profile_model->destroy($id); //Mengakses Fugsi Delete dari Model, melakukan Soft Delete
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function returnData($msg,$error) //Fungsi untuk me-return kan Nilai balikkan setelah melakukan Method apapun seperti Error dan Pesan
    {
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class profileData
{
    public $id;
    public $nama;
    public $nohp;
    public $email;
    public $alamat;
}