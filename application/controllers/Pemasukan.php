<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemasukan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Pemasukan";
        $data['pemasukan'] = $this->admin->getPemasukan();
        $this->template->load('templates/dashboard', 'pemasukan/data', $data);
    }

    private function _validasi()
    {
        $this->form_validation->set_rules('tanggal_pemasukan', 'Tanggal Pemasukan', 'required|trim');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|trim');
        $this->form_validation->set_rules('jumlah_pemasukan', 'Jumlah Pemasukan', 'required|trim|numeric|greater_than[0]');
    }

    public function add()
    {
        $this->_validasi();
        if ($this->form_validation->run() == false) {
            $data['title'] = "Pemasukan";

            // Mendapatkan dan men-generate kode transaksi barang masuk
            $kode = 'PMSK-' . date('ymd');
            $kode_terakhir = $this->admin->getMax('pemasukan', 'id_pemasukan', $kode);
            $kode_tambah = substr($kode_terakhir, -5, 5);
            $kode_tambah++;
            $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
            $data['id_pemasukan'] = $kode . $number;


            $this->template->load('templates/dashboard', 'pemasukan/add', $data);
        } else {
            $input = $this->input->post(null, true);
            $insert = $this->admin->insert('pemasukan', $input);

            if ($insert) {
                set_pesan('data berhasil disimpan.');
                redirect('Pemasukan');
            } else {
                set_pesan('Opps ada kesalahan!');
                redirect('Pemasukan/add');
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('pemasukan', 'id_pemasukan', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('Pemasukan');
    }
}
