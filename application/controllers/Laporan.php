<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Laporan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('dompdf_gen');
    }

    function laporan_buku()
    {
        $data   = [
            'judul'     => "laporan Data Buku",
            'user'      => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array(),
            'buku'      => $this->ModelBuku->getBuku()->result_array(),
            'kategori'  => $this->ModelBuku->getKategori()->result_array()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buku/laporan_buku', $data);
        $this->load->view('templates/footer');
    }

    function cetak_laporan_buku()
    {
        $data   = [
            'user'  => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array(),
            'buku'  => $this->ModelBuku->getBuku()->result_array()
        ];
        $this->load->view('buku/laporan_print_buku', $data);
    }

    function laporan_buku_pdf()
    {
        $data   = [
            'user'      => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array(),
            'buku'      => $this->ModelBuku->getBuku()->result_array()
        ];
        $this->load->view('buku/laporan_buku_pdf', $data);
        $html           = $this->output->get_output();
        $this->load->library('dompdf_gen');
        $paper_size     = 'A4';
        $orientation    = 'landscape';
        $this->dompdf->set_paper($paper_size, $orientation);
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("laporan_data_buku.pdf", array('Attachment' => 0));
    }

    function export_excel()
    {
        $data  = [
            'title' => "Laporan Buku",
            'buku'  => $this->ModelBuku->getBuku()->result_array()
        ];
        $this->load->view('buku/export_excel_buku', $data);
    }

    function laporan_pinjam()
    {
        $data = [
            'judul'      => "Laporan Pinjam",
            'user'       => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array(),
            'laporan'    => $this->db->query("SELECT * FROM pinjam p,detail_pinjam d, buku b, user u 
                                            WHERE d.id_buku=b.id AND p.id_user=u.id AND p.no_pinjam=d.no_pinjam")->result_array()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buku/laporan-pinjam', $data);
        $this->load->view('templates/footer');
    }

    function cetak_laporan_pinjam()
    {
        $data = [
            'laporan'    => $this->db->query("SELECT * FROM pinjam p,detail_pinjam d, buku b, user u 
                                            WHERE d.id_buku=b.id AND p.id_user=u.id AND p.no_pinjam=d.no_pinjam")->result_array()
        ];
        $this->load->view('buku/laporan-print-pinjam', $data);
    }

    function laporan_pinjam_pdf()
    {
        $data = [
            'laporan'    => $this->db->query("SELECT * FROM pinjam p,detail_pinjam d, buku b, user u 
                                            WHERE d.id_buku=b.id AND p.id_user=u.id AND p.no_pinjam=d.no_pinjam")->result_array()
        ];
        $this->load->view('buku/laporan-pdf-pinjam', $data);
        $html   = $this->output->get_output();
        $this->load->library('dompdf_gen');
        $paper_size     = 'A4';
        $orientation    = 'Landscape';
        $this->dompdf->set_paper($paper_size, $orientation);
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("laporan-data-peminjaman.pdf", array("Attachment" => 0));
    }

    function laporan_anggota()
    {
        $data = [
            'judul'     => "Data Anggota",
            'user'      => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array(),
            'anggota'   => $this->db->get_where('user', ['role_id' => 2])->result_array()
        ];
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/laporan-anggota', $data);
        $this->load->view('templates/footer');
    }

    function laporan_anggota_pdf()
    {
        $data = [
            'judul'     => "Laporan Data Anggota",
            'anggota'   => $this->db->get_where('user', ['role_id' => 2])->result_array()
        ];
        $this->load->view('admin/laporan-anggota-pdf', $data);
        $html   = $this->output->get_output();
        $this->load->library('dompdf_gen');
        $paper_size     = 'A4';
        $orientation    = 'Landscape';
        $this->dompdf->set_paper($paper_size, $orientation);
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("laporan-data-anggota.pdf", array("Attachment" => 0));
    }

    function export_excel_pinjam()
    {
        $data = [
            'title'      => 'Export Excel Pinjam',
            'laporan'    => $this->db->query("SELECT * FROM pinjam p,detail_pinjam d, buku b, user u 
                                            WHERE d.id_buku=b.id AND p.id_user=u.id AND p.no_pinjam=d.no_pinjam")->result_array()
        ];
        $this->load->view('admin/export-excel-pinjam', $data);
    }
}
