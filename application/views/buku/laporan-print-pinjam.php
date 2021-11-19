<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Data Pinjam Buku Perpustakaan Online</title>
  <style>
    h3 {
      text-align: center;
    }

    .table-buku {
      width: 100%;
      border-collapse: collapse;
    }

    .table-buku tr th,
    .table-buku tr td {
      border: 1px solid #000;
      font-size: 11pt;
      font-family: 'Verdana', sans-serif;
      padding: 10px;
    }
  </style>
</head>

<body>
  <h3>Laporan Data Pinjam Buku Perpustakaan Online</h3>
  <table class="table-buku">
    <tr>
      <th>No</th>
      <th>Nama Anggota</th>
      <th>Judul Buku</th>
      <th>Tanggal Pinjam</th>
      <th>Tanggal Kembali</th>
      <th>Tanggal Pengembalian</th>
      <th>Total Denda</th>
      <th>Status</th>
    </tr>
    <?php foreach ($laporan as $key => $lp) : ?>
      <tr>
        <td><?= $key + 1; ?></td>
        <td><?= $lp['nama']; ?></td>
        <td><?= $lp['judul_buku'] ?></td>
        <td><?= $lp['tgl_pinjam'] ?></td>
        <td><?= $lp['tgl_kembali'] ?></td>
        <td><?= $lp['tgl_pengembalian'] ?></td>
        <td><?= $lp['total_denda'] ?></td>
        <td><?= $lp['status'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <script type="text/javascript">
    window.print();
  </script>
</body>

</html>