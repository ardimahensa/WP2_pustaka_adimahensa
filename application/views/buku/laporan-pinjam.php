<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <a href="<?= base_url('laporan/cetak_laporan_pinjam'); ?>" class="btn btn-primary mb-3">
        <i class="fas fa-print"></i>
      </a>
      <a href="<?= base_url('laporan/laporan_pinjam_pdf'); ?>" class="btn btn-danger mb-3">
        <i class="far fa-file-pdf"></i>
      </a>
      <a href="<?= base_url('laporan/export_excel_pinjam'); ?>" class="btn btn-success mb-3">
        <i class="far fa-file-excel"></i>
      </a>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nama Anggota</th>
                  <th scope="col">Judul Buku</th>
                  <th scope="col">Tanggal Pinjam</th>
                  <th scope="col">Tanggal Kembali </th>
                  <th scope="col">Tanggal Dikembalikan</th>
                  <th scope="col">Total Denda</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $a = 1;
                foreach ($laporan as $l) { ?> <tr>
                    <th scope="row"><?= $a++; ?></th>
                    <td scope="row"><?= $l['nama']; ?></td>
                    <td scope="row"><?= $l['judul_buku']; ?></td>
                    <td scope="row"><?= $l['tgl_pinjam']; ?></td>
                    <td scope="row"><?= $l['tgl_kembali']; ?></td>
                    <td scope="row"><?= $l['tgl_pengembalian']; ?></td>
                    <td scope="row"><?= $l['total_denda']; ?></td>
                    <td scope="row"><?= $l['status']; ?></td>
                  </tr> <?php } ?> </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>