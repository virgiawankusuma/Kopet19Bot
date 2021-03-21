<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //Load Dependencies

    }

    // List all your items
    public function index($offset = 0)
    {
        $data = [
            'title' => 'Daftar menu/fitur',
            'fitur' => $this->db->order_by('id', 'ASC')->get('tbl_fitur')->result(),
            'menu' => $this->db->order_by('id', 'ASC')->get('tbl_menu')->result()
        ];
        $this->load->view('index', $data);
    }

    // Add a new item
    public function addMenu()
    {
        $this->db->insert('tbl_menu', ['menu' => $this->input->post('menu')]);
        redirect('/admin', 'refresh');
    }

    public function addFitur()
    {
        $data = [
            'fitur' => $this->input->post('fitur'),
            'callback_data' => $this->input->post('fitur'),
            'description' => $this->input->post('description'),
            'source' => $this->input->post('source'),
            'menu' => $this->input->post('menu'),
            'url' => $this->input->post('url')
        ];
        $this->db->insert('tbl_fitur', $data);
        redirect('/admin', 'refresh');
    }

    //Update one item
    public function updateMenu($id)
    {
        switch ($id) {
            case null:
                redirect('/admin', 'refresh');
                break;

            default:
                $data = [
                    'title' => 'Ubah menu',
                    'menu' => $this->db->get_where('tbl_menu', ['id' => $id])->row(),
                ];
                $this->load->view('updateMenu', $data);
                break;
        }
    }
    public function updateFitur($id = null)
    {
        switch ($id) {
            case null:
                redirect('/admin', 'refresh');
                break;

            default:
                $data = [
                    'title' => 'Ubah fitur',
                    'fitur' => $this->db->get_where('tbl_fitur', ['id' => $id])->row(),
                    'menu' => $this->db->order_by('id', 'ASC')->get('tbl_menu')->result()
                ];
                $this->load->view('updateFitur', $data);
                break;
        }
    }

    //Delete one item
    public function deleteMenu($id)
    {
        $this->db->where(['id' => $id])->delete('tbl_menu');
        redirect('/admin', 'refresh');
    }

    public function deleteFitur($id)
    {
        $this->db->where(['id' => $id])->delete('tbl_fitur');
        redirect('/admin', 'refresh');
    }
}

/* End of file Admin.php */
