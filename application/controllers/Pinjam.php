<?php if (!defined('BASEPATH')) exit('No Direct Script Access Allowed');

class Pinjam extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(['ModelUser', 'ModelBuku', 'ModelPinjam']);
        cek_login();
    }

    function index()
    {
        $data = [
            'judul'     => "Data Pinjam",
            'user'      => $this->ModelUser->cekData(['email' => $this->session->userdata('email')])->row_array(),
            'pinjam'    => $this->ModelPinjam->joinData()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pinjam/data-pinjam', $data);
        $this->load->view('templates/footer');
    }

    function daftarBooking()
    {
        $data   = [
            'judul'     => "Daftar Booking",
            'user'      => $this->ModelUser->cekData(['email' => $this->session->userdata('email')])->row_array(),
            'pinjam'    => $this->db->query("select*from booking")->result_array()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('booking/daftar-booking', $data);
        $this->load->view('templates/footer');
    }

    function bookingDetail()
    {
        $id_booking           = $this->uri->segment(3);
        $data   = [
            'judul'         => "Booking Detail",
            'user'          => $this->ModelUser->cekData(['email' => $this->session->userdata('email')])->row_array(),
            'agt_booking'   => $this->db->query("select*from booking b, user u where b.id_user=u.id and b.id_booking='$id_booking'")->result_array(),
            'detail'        => $this->db->query("select id_buku,judul_buku,pengarang,penerbit,tahun_terbit from booking_detail d, buku b where d.id_buku=b.id and d.id_booking='$id_booking'")->result_array()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('booking/booking-detail', $data);
        $this->load->view('templates/footer');
    }

    function pinjamAct()
    {
        $id_booking     = $this->uri->segment(3);
        $lama           = $this->input->post('lama', TRUE);
        $bo             = $this->db->query("SELECT*FROM booking WHERE id_booking='$id_booking'")->row();
        $tglsekarang    = date('Y-m-d');
        $no_pinjam      = $this->ModelBooking->kodeotomatis('pinjam', 'no_pinjam');
        $databooking    = [
            'no_pinjam'         => $no_pinjam,
            'id_booking'        => $id_booking,
            'tgl_pinjam'        => $tglsekarang,
            'id_user'           => $bo->id_user,
            'tgl_kembali'       => date('Y-m-d', strtotime('+' . $lama . ' days', strtotime($tglsekarang))),
            'tgl_pengembalian'  => '0000-00-00',
            'status'            => 'Pinjam',
            'total_denda'       => 0
        ];
        $this->ModelPinjam->simpanPinjam($databooking);
        $this->ModelPinjam->simpanDetail($id_booking, $no_pinjam);
        $denda  = $this->input->post('denda', TRUE);
        $this->db->query("update detail_pinjam set denda='$denda'");
        $this->ModelPinjam->deleteData('booking', ['id_booking' => $id_booking]);
        $this->ModelPinjam->deleteData('booking_detail', ['id_booking' => $id_booking]);
        $this->db->query("UPDATE buku, detail_pinjam SET buku.dipinjam=buku.dipinjam+1, buku.dibooking=buku.dibooking-1 WHERE buku.id=detail_pinjam.id_buku");
        $this->session->set_flashdata('pesan', '<div class="alert alert-message alert-success" role="alert">Data Peminjaman Berhasil Disimpan</div>');
        redirect(base_url() . 'pinjam', 'refresh');
    }

    function ubahStatus()
    {
        $id_buku    = $this->uri->segment(3);
        $no_pinjam  = $this->uri->segment(4);
        $tgl        = date('Y-m-d');
        $status     = 'Kembali';
        $this->db->query("UPDATE pinjam, detail_pinjam SET pinjam.status='$status', pinjam.tgl_pengembalian='$tgl' WHERE detail_pinjam.id_buku='$id_buku' AND pinjam.no_pinjam='$no_pinjam'");
        $this->db->query("UPDATE buku, detail_pinjam SET buku.dipinjam=buku.dipinjam-1, buku.stok=buku.stok+1 WHERE buku.id=detail_pinjam.id_buku");
        $this->session->set_flashdata('pesan', '<div class="laert alert-message alert-success" role="alert"></div>');
        redirect(base_url('pinjam'), 'refresh');
    }
}