<?php
/**
 * Shared report data fetcher.
 * Reads $_GET['from'], $_GET['to'], $_GET['type'] and produces:
 *   $from, $to, $type, $summary, $data
 * Used by index.php, export_excel.php, export_pdf.php
 */

$from = clean($_GET['from'] ?? date('Y-m-01'));
$to   = clean($_GET['to']   ?? date('Y-m-d'));
$type = clean($_GET['type'] ?? 'orders');
if (!in_array($type, ['orders','konser','tiket'])) $type = 'orders';

$summary = DB::row("SELECT COUNT(*) as total, SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) as paid,
  SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
  SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) as cancelled,
  SUM(CASE WHEN status='paid' THEN total ELSE 0 END) as revenue,
  SUM(CASE WHEN status='paid' THEN jumlah ELSE 0 END) as tiket_terjual
  FROM orders WHERE DATE(created_at) BETWEEN ? AND ?", [$from,$to]);

if ($type==='orders') {
    $data = DB::rows("SELECT o.*,u.nama as nama_pembeli,u.email,t.nama as nama_tiket,k.nama as nama_konser
      FROM orders o JOIN users u ON o.user_id=u.id JOIN tiket t ON o.tiket_id=t.id JOIN konser k ON t.konser_id=k.id
      WHERE DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.created_at DESC", [$from,$to]);
} elseif ($type==='konser') {
    $data = DB::rows("SELECT k.nama,k.artis,k.tanggal,k.kota,k.status,
      COUNT(DISTINCT t.id) as jml_tiket, SUM(t.stok) as total_stok, SUM(t.terjual) as total_terjual,
      SUM(t.terjual*t.harga) as pendapatan FROM konser k LEFT JOIN tiket t ON t.konser_id=k.id
      GROUP BY k.id ORDER BY pendapatan DESC");
} else {
    $data = DB::rows("SELECT t.*,k.nama as nama_konser,k.artis,(t.stok-t.terjual) as sisa,(t.terjual*t.harga) as pendapatan
      FROM tiket t JOIN konser k ON t.konser_id=k.id ORDER BY pendapatan DESC");
}

$typeLabels = ['orders'=>'Laporan Transaksi','konser'=>'Laporan Per Konser','tiket'=>'Laporan Per Tiket'];
$reportTitle = $typeLabels[$type];
