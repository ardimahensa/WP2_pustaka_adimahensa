<?php if (!defined('BASEPATH')) exit('No Direct Script Access Allowed');
date_default_timezone_set('Asia/Jakarta');
class Booking extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        cek_login();
        $this->load->model(['ModelBooking', 'ModelUser']);
    }

    function index()
    {
        $id                 = ['bo.id_user' => $this->uri->segment(3)];
        $id_user            = $this->session->userdata('id');
        $data['booking']    = $this->ModelBooking->joinOrder($id)->result();
        $user = $this->ModelUser->cekData(['email' => $this->session->userdata('email')])->row_array();
        foreach ($user as $a) {
            $data = [
                'image'         => $user['image'],
                'user'          => $user['nama'],
                'email'         => $user['email'],
                'tanggal_input' => $user['tanggal_input']
            ];
        }

        $dtb = $this->ModelBooking->showtemp(['id_user' => $id_user])->num_rows();
        if ($dtb < 1) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-massege alert-danger" role="alert">Tidak Ada Buku dikeranjang</div>');
            redirect(base_url());
        } else {
            $data['temp'] = $this->db->query("select image, judul_buku, penulis, penerbit, tahun_terbit,id_buku from temp where id_user='$id_user'")->result_array();
        }
        $data['judul'] = "Data Booking";
        $this->load->view('templates/templates-user/header', $data);
        $this->load->view('booking/data-booking', $data);
        $this->load->view('templates/templates-user/footer');
    }

    function tambahBooking()
    {
        $data['user']   = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $id_buku        = $this->uri->segment(3);
        $d              = $this->db->query("Select*from buku where id='$id_buku'")->row();
        $isi            =  [
            'tgl_booking'   => date('Y-m-d H:i:s'),
            'id_user'       => $data['user']['id'],
            'email_user'    => $this->session->userdata('email'),
            'id_buku'       => $id_buku,
            'judul_buku'    => $d->judul_buku,
            'image'         => $d->image,
            'penulis'       => $d->pengarang,
            'penerbit'      => $d->penerbit,
            'tahun_terbit'  => $d->tahun_terbit
        ];
        $temp           = $this->ModelBooking->getDataWhere('temp', ['id_buku' => $id_buku])->num_rows();
        $userid         = $this->session->userdata('id_user');
        $tempuser       = $this->db->query("select*from temp where id_user ='$userid'")->num_rows();
        $databooking    = $this->db->query("select*from booking where id_user='$userid'")->num_rows();
        if ($databooking > 0) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Masih Ada booking buku sebelumnya yang belum diambil.<br> Ambil Buku yang dibooking atau tunggu 1x24 Jam untuk bisa booking kembali </div>');
            redirect(base_url());
        }
        if ($temp > 0) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Buku ini Sudah anda booking </div>');
            redirect(base_url() . 'home');
        }
        if ($tempuser == 3) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Booking Buku Tidak Boleh Lebih dari 3</div>');
            redirect(base_url() . 'home');
        }
        $this->ModelBooking->createTemp();
        $this->ModelBooking->insertData('temp', $isi);
        $this->session->set_flashdata('pesan', '<div class="alert alert-success alert-message" role="alert">Buku berhasil ditambahkan ke keranjang </div>');
        redirect(base_url() . 'home');
    }

    function hapusbooking()
    {
        $id_buku    = $this->uri->segment(3);
        $id_user    = $this->session->userdata('id_user');
        $this->ModelBooking->deleteData(['id_buku' => $id_buku], 'temp');
        $kosong = $this->db->query("select*from temp where id_user='$id_user'")->num_rows();
        if ($kosong < 1) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-massege alert-danger" role="alert">Tidak Ada Buku dikeranjang</div>');
            redirect(base_url());
        } else {
            redirect(base_url() . 'booking');
        }
        $this->ModelBooking->deleteData(['id_buku' => $id_buku], 'temp');
        $kosong     = $this->db->query("select*from temp where id_user='$id_user'")->num_rows();
        if ($kosong < 1) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-massege alert-danger" role="alert">Tidak Ada Buku dikeranjang</div>');
            redirect(base_url());
        } else {
            redirect(base_url() . 'booking');
        }
    }

    function bookingSelesai($id)
    {
        $this->db->query("UPDATE buku, temp SET buku.dibooking=buku.dibooking+1, buku.stok=buku.stok-1 WHERE buku.id=temp.id_buku");
        $tglsekarang    = date('Y-m-d');
        $isibooking     = [
            'id_booking'    => $this->ModelBooking->kodeOtomatis('booking', 'id_booking'),
            'tgl_booking'   => date('Y-m-d H:m:s'),
            'batas_ambil'   => date('Y-m-d', strtotime('+2 days', strtotime($tglsekarang))),
            'id_user'       => $id
        ];
        $this->ModelBooking->insertData('booking', $isibooking);
        $this->ModelBooking->simpanDetail($id);
        $this->ModelBooking->kosongkanData('temp');
        redirect(base_url() . 'booking/info');
    }

    function info()
    {
        $where              = $this->session->userdata('id');
        $data['user']       = $this->session->userdata('nama');
        $data['judul']      = "Selesai Booking";
        $data['useraktif']  = $this->ModelUser->cekData(['id' => $this->session->userdata('id_user')])->result();
        $data['items']      = $this->db->query("select*from booking bo, booking_detail d, buku bu where d.id_booking=bo.id_booking and d.id_buku=bu.id and bo.id_user='$where'")->result_array();
        $this->load->view('templates/templates-user/header', $data);
        $this->load->view('booking/info-booking', $data);
        $this->load->view('templates/templates-user/footer');
    }

    function exportToPdf()
    {
        $id_user        = $this->session->userdata('id');
        $data = [
            'user'      => $this->session->userdata('nama'),
            'judul'     => "Cetak Bukti Booking",
            'useraktif' => $this->ModelUser->cekData(['id' => $this->session->userdata('id')])->result(),
            'items'     => $this->db->query("select*from booking bo, booking_detail d, buku bu where d.id_booking=bo.id_booking and d.id_buku=bu.id and bo.id_user='$id_user'")->result_array()
        ];
        $this->load->view('booking/bukti-pdf', $data);
        $html           = $this->output->get_output();
        $this->load->library('dompdf_gen');
        $paper_size     = 'A4';
        $orientation    = 'landscape';
        $this->dompdf->set_paper($paper_size, $orientation);
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("bukti-booking-$id_user.pdf", array('Attachment' => 0));
    }
}
