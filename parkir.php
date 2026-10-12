<?php
    $noParkir = '';
    $noKendaraan = '';
    $jenisKendaraan = '';
    $lamaParkir = '';
    $namaPetugas = '';
    $totalBayar = null;
    $pesanError = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $noParkir = isset($_POST['no_parkir']) && is_string($_POST['no_parkir']) ? trim($_POST['no_parkir']) : '';
        $noKendaraan = isset($_POST['no_kendaraan']) && is_string($_POST['no_kendaraan']) ? trim($_POST['no_kendaraan']) : '';
        $jenisKendaraan = isset($_POST['jenis_kendaraan']) && is_string($_POST['jenis_kendaraan']) ? $_POST['jenis_kendaraan'] : '';
        $lamaParkir = isset($_POST['lama_parkir']) && is_string($_POST['lama_parkir']) ? $_POST['lama_parkir'] : '';
        $namaPetugas = isset($_POST['nama_petugas']) && is_string($_POST['nama_petugas']) ? trim($_POST['nama_petugas']) : '';

        $tarif = array(
            'motor' => array('pertama' => 3000, 'berikutnya' => 2000),
            'mobil' => array('pertama' => 5000, 'berikutnya' => 3000)
        );
        $durasiValid = ctype_digit($lamaParkir) ? filter_var($lamaParkir, FILTER_VALIDATE_INT) : false;

        if ($noParkir === '' || $noKendaraan === '' || $namaPetugas === '') {
            $pesanError = 'Semua data wajib diisi.';
        } elseif (!isset($tarif[$jenisKendaraan])) {
            $pesanError = 'Silakan pilih jenis kendaraan yang valid.';
        } elseif ($durasiValid === false || $durasiValid < 1) {
            $pesanError = 'Lama parkir harus berupa jumlah jam bulat minimal 1.';
        } else {
            $tarifPertama = $tarif[$jenisKendaraan]['pertama'];
            $tarifBerikutnya = $tarif[$jenisKendaraan]['berikutnya'];
            $jamBerikutnya = $durasiValid - 1;

            if ($jamBerikutnya > intdiv(PHP_INT_MAX - $tarifPertama, $tarifBerikutnya)) {
                $pesanError = 'Lama parkir terlalu besar untuk dihitung.';
            } else {
                $totalBayar = $tarifPertama + ($jamBerikutnya * $tarifBerikutnya);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Form Pembayaran Parkir</title>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type=" text/css" href="style.css">
    </head>     
    <body>
        <div class="container">
            <center>
                <h2>FORM PEMBAYARAN PARKIR</h2>
            </center>

            <?php if ($pesanError !== '') { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($pesanError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>
            <?php if ($totalBayar !== null) { ?>
                <div class="alert alert-success" role="status">
                    Total bayar untuk kendaraan
                    <strong><?php echo htmlspecialchars($noKendaraan, ENT_QUOTES, 'UTF-8'); ?></strong>
                    selama <?php echo (int) $lamaParkir; ?> jam adalah
                    <strong>Rp. <?php echo number_format($totalBayar, 0, ',', '.'); ?></strong>.
                </div>
            <?php } ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="no_parkir">No parkir</label>
                    <input type="text" class="form-control" id="no_parkir" name="no_parkir" value="<?php echo htmlspecialchars($noParkir, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_kendaraan">No kendaraan</label>
                    <input type="text" class="form-control" id="no_kendaraan" name="no_kendaraan" value="<?php echo htmlspecialchars($noKendaraan, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="jenis_kendaraan">Jenis kendaraan</label>
                    <select class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" required>
                        <option value="">-- Pilih jenis kendaraan --</option>
                        <option value="mobil" <?php echo $jenisKendaraan === 'mobil' ? 'selected' : ''; ?>>Mobil</option>
                        <option value="motor" <?php echo $jenisKendaraan === 'motor' ? 'selected' : ''; ?>>Motor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lama_parkir">Lama parkir (jam)</label>
                    <input type="number" class="form-control" id="lama_parkir" name="lama_parkir" min="1" step="1" value="<?php echo htmlspecialchars($lamaParkir, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_petugas">Nama petugas</label>
                    <input type="text" class="form-control" id="nama_petugas" name="nama_petugas" value="<?php echo htmlspecialchars($namaPetugas, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </body>
</html>