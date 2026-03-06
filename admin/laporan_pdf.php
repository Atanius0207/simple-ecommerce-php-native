<?php
include '../db.php';
require('../fpdf186/fpdf.php');

// Ambil parameter
$type      = isset($_GET['type']) ? $_GET['type'] : 'penjualan_offline';
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Query sesuai type
if ($type == 'penjualan_offline') {
    $q = mysqli_query($conn, "SELECT t.id, t.tanggal, t.total, o.nama_outlet 
                              FROM transaksi t 
                              LEFT JOIN outlet o ON t.outlet_id=o.id
                              WHERE t.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY t.tanggal DESC");
    $judul = "Laporan Penjualan Offline";
} elseif ($type == 'penjualan_online') {
    $q = mysqli_query($conn, "SELECT toln.id, toln.tanggal, toln.total, toln.nama_penerima 
                              FROM transaksi_online toln
                              WHERE DATE(toln.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY toln.tanggal DESC");
    $judul = "Laporan Penjualan Online";
} else {
    $q = mysqli_query($conn, "SELECT p.id, p.tanggal, p.supplier, p.total 
                              FROM pembelian p 
                              WHERE p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY p.tanggal DESC");
    $judul = "Laporan Pembelian";
}

// Buat PDF
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(190,10,$judul,0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(190,7,"Periode: $tgl_awal s/d $tgl_akhir",0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',11);
if ($type == 'penjualan_offline') {
    $pdf->Cell(30,10,'ID',1,0,'C');
    $pdf->Cell(40,10,'Tanggal',1,0,'C');
    $pdf->Cell(60,10,'Outlet',1,0,'C');
    $pdf->Cell(60,10,'Total',1,1,'C');
} elseif ($type == 'penjualan_online') {
    $pdf->Cell(30,10,'ID',1,0,'C');
    $pdf->Cell(40,10,'Tanggal',1,0,'C');
    $pdf->Cell(60,10,'Customer',1,0,'C');
    $pdf->Cell(60,10,'Total',1,1,'C');
} else {
    $pdf->Cell(30,10,'ID',1,0,'C');
    $pdf->Cell(40,10,'Tanggal',1,0,'C');
    $pdf->Cell(60,10,'Supplier',1,0,'C');
    $pdf->Cell(60,10,'Total',1,1,'C');
}

// Isi data
$pdf->SetFont('Arial','',10);
$grand = 0;
while($row = mysqli_fetch_assoc($q)){
    $grand += $row['total'];

    $pdf->Cell(30,8,$row['id'],1,0,'C');
    $pdf->Cell(40,8,$row['tanggal'],1,0,'C');

    if ($type == 'penjualan_offline') {
        $pdf->Cell(60,8,$row['nama_outlet'],1,0,'L');
    } elseif ($type == 'penjualan_online') {
        $pdf->Cell(60,8,$row['nama_penerima'],1,0,'L');
    } else {
        $pdf->Cell(60,8,$row['supplier'],1,0,'L');
    }

    $pdf->Cell(60,8,"Rp ".number_format($row['total'],0,',','.'),1,1,'R');
}

// Total
$pdf->SetFont('Arial','B',11);
$pdf->Cell(130,10,'TOTAL',1,0,'R');
$pdf->Cell(60,10,"Rp ".number_format($grand,0,',','.'),1,1,'R');

// Output PDF (fix error "Some data has already been output")
ob_end_clean();
$pdf->Output("D",$judul."_".$tgl_awal."_".$tgl_akhir.".pdf");
exit;
