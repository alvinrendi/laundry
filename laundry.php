<?php
date_default_timezone_set("Asia/Jakarta");
$conn = new mysqli("localhost", "root", "", "laundry_db");

// Ambil data dropdown
$pelanggan = $conn->query("SELECT * FROM pelanggan");
$jenis = $conn->query("SELECT * FROM jenis_laundry");

// CREATE
if (isset($_POST['save'])) {
  $id_pelanggan = $_POST['id_pelanggan'];
  $id_jenis = $_POST['id_jenis'];
  $jumlah = $_POST['jumlah'];

  $row = $conn->query("SELECT harga FROM jenis_laundry WHERE id_jenis='$id_jenis'")->fetch_assoc();
  $total = $row['harga'] * $jumlah;

  $tanggal_terima = date("Y-m-d");
  $tanggal_selesai = date("Y-m-d", strtotime("+3 days"));

  $conn->query("INSERT INTO transaksi (id_pelanggan,tanggal_terima,tanggal_selesai) 
                VALUES ('$id_pelanggan','$tanggal_terima','$tanggal_selesai')");
  $id_transaksi = $conn->insert_id;

  $conn->query("INSERT INTO detail_transaksi (id_transaksi,id_jenis,jumlah,total) 
                VALUES ('$id_transaksi','$id_jenis','$jumlah','$total')");
}

// DELETE
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM detail_transaksi WHERE id_transaksi='$id'");
  $conn->query("DELETE FROM transaksi WHERE id_transaksi='$id'");
}

// EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $edit_data = $conn->query("SELECT t.id_transaksi,t.id_pelanggan,j.id_jenis,j.harga,d.jumlah,d.total 
                             FROM transaksi t 
                             JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi
                             JOIN jenis_laundry j ON d.id_jenis=j.id_jenis 
                             WHERE t.id_transaksi='$id'")->fetch_assoc();
}

// UPDATE
if (isset($_POST['update'])) {
  $id_transaksi = $_POST['id_transaksi'];
  $id_pelanggan = $_POST['id_pelanggan'];
  $id_jenis = $_POST['id_jenis'];
  $jumlah = $_POST['jumlah'];

  $row = $conn->query("SELECT harga FROM jenis_laundry WHERE id_jenis='$id_jenis'")->fetch_assoc();
  $total = $row['harga'] * $jumlah;

  $conn->query("UPDATE transaksi t 
                JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi 
                SET t.id_pelanggan='$id_pelanggan', 
                    d.id_jenis='$id_jenis', 
                    d.jumlah='$jumlah', 
                    d.total='$total' 
                WHERE t.id_transaksi='$id_transaksi'");
  header("Location: laundry.php");
}

// READ
$data = $conn->query("SELECT t.id_transaksi,p.nama_pelanggan,j.nama_jenis,j.harga,d.jumlah,d.total,
                             t.tanggal_terima,t.tanggal_selesai 
                      FROM transaksi t 
                      JOIN pelanggan p ON t.id_pelanggan=p.id_pelanggan 
                      JOIN detail_transaksi d ON t.id_transaksi=d.id_transaksi 
                      JOIN jenis_laundry j ON d.id_jenis=j.id_jenis");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Laundry</title>
  <style>
    body {
      background: #e7e7e7ff;
      font-family: Arial
    }

    h1 {
      text-align: center;
      color: #000
    }

    h2 {
      margin-bottom: 15px;
      margin-top: 0px;
      text-align: left;
      color: #000;
    }

    .container {
      display: flex;
      justify-content: space-between;
      padding: 20px
    }

    .table-container,
    .form-container {
      background: #ffffffff;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .1)
    }

    .table-container {
      width: 65%
    }

    .form-container {
      width: 30%
    }

    table {
      width: 100%;
      border-collapse: collapse
    }

    th,
    td {
      border: 1px solid #000000ff;
      padding: 8px;
      text-align: center
    }

    th {
      background: #c3c3c3ff;
      color: #fff
    }

    tr:nth-child(even) {
      background: #f9f9f9
    }

    .btn {
      padding: 6px 8px;
      margin: 3px;
      border-radius: 4px;
      text-decoration: none;
      display: inline-block;
      width: 70px;
      color: #000;
      background: #b0b0b0;
      border: none;
      cursor: pointer;
    }

    .btn-save {
      background: #66bb6a;
      color: #fff
    }

    .btn-update {
      background: #ffd54f;
      color: #000
    }

    .btn-delete {
      background: #ef5350;
      color: #fff
    }

    .btn:hover { background: #a2a2a2ff; }

    .form-group {
      margin-bottom: 10px
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
      display: block
    }

    select,
    input {
      width: 100%;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 4px
    }
  </style>
</head>

<body>
  <h1>Sistem Informasi Laundry</h1>
  <div class="container">   
    <div class="table-container">
      <h2>Transaksi Laundry</h2>
      <table>
        <tr>
          <th>No</th>
          <th>Pelanggan</th>
          <th>Terima</th>
          <th>Selesai</th>
          <th>Jenis</th>
          <th>Harga</th>
          <th>Jumlah</th>
          <th>Total</th>
          <th>Aksi</th>
        </tr>
        <?php $no = 1;
        while ($row = $data->fetch_assoc()) { ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_pelanggan'] ?></td>
            <td><?= $row['tanggal_terima'] ?></td>
            <td><?= $row['tanggal_selesai'] ?></td>
            <td><?= $row['nama_jenis'] ?></td>
            <td><?= $row['harga'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= $row['total'] ?></td>
            <td>
              <a href="?edit=<?= $row['id_transaksi'] ?>" class="btn btn-update">Edit</a>
              <a href="?delete=<?= $row['id_transaksi'] ?>" class="btn btn-delete"
                onclick="return confirm('Yakin hapus?')">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <div class="form-container">
      <form method="post">
        <input type="hidden" name="id_transaksi" value="<?= $edit_data['id_transaksi'] ?? '' ?>">
        <div class="form-group">
          <label>Pilih Pelanggan:</label>
          <select name="id_pelanggan" required>
            <option value="">--Pilih Pelanggan--</option>
            <?php $pelanggan->data_seek(0);
            while ($row = $pelanggan->fetch_assoc()) { ?>
              <option value="<?= $row['id_pelanggan'] ?>" <?= ($edit_data['id_pelanggan'] ?? '') == $row['id_pelanggan'] ? 'selected' : '' ?>>
                <?= $row['nama_pelanggan'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="form-group">
          <label>Pilih Jenis:</label>
          <select name="id_jenis" id="id_jenis" required>
            <option value="">--Pilih Jenis--</option>
            <?php $jenis->data_seek(0);
            while ($row = $jenis->fetch_assoc()) { ?>
              <option value="<?= $row['id_jenis'] ?>" data-harga="<?= $row['harga'] ?>" <?= ($edit_data['id_jenis'] ?? '') == $row['id_jenis'] ? 'selected' : '' ?>>
                <?= $row['nama_jenis'] ?> - Rp<?= $row['harga'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="form-group">
          <label>Harga:</label>
          <input type="text" id="harga" readonly value="<?= $edit_data['harga'] ?? '' ?>">
        </div>
        <div class="form-group">
          <label>Jumlah:</label>
          <input type="number" name="jumlah" id="jumlah" value="<?= $edit_data['jumlah'] ?? '' ?>" required>
        </div>
        <div class="form-group">
          <label>Total:</label>
          <input type="text" id="total" readonly value="<?= $edit_data['total'] ?? '' ?>">
        </div>

        <button type="submit" name="<?= $edit_data ? 'update' : 'save' ?>"
          class="btn <?= $edit_data ? 'btn-update' : 'btn-save' ?>">
          <?= $edit_data ? 'UPDATE' : 'SAVE' ?>
        </button>
      </form>
    </div>
  </div>

  <script>
    document.getElementById("id_jenis").addEventListener("change", function () {
      document.getElementById("harga").value = this.options[this.selectedIndex].dataset.harga;
      hitungTotal();
    });
    document.getElementById("jumlah").addEventListener("input", hitungTotal);
    function hitungTotal() {
      let h = document.getElementById("harga").value, j = document.getElementById("jumlah").value;
      document.getElementById("total").value = h && j ? h * j : '';
    }
  </script>
</body>

</html>