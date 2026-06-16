<?php
// ── App Constants ──────────────────────────────────────────
define('APP_NAME',    'NexTix');
define('APP_URL',     'http://localhost/nexttix');
define('APP_VERSION', '1.0.0');

define('UPLOAD_DIR',    __DIR__ . '/../uploads/');
define('POSTER_DIR',    UPLOAD_DIR . 'posters/');
define('FOTO_DIR',      UPLOAD_DIR . 'foto/');
define('ITEMS_PER_PAGE', 10);

// ── Flash Message ──────────────────────────────────────────
function flash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// ── Sanitize (input). Output escaping is done separately via htmlspecialchars() ──
function clean(string $val): string {
    return trim(strip_tags($val));
}

// ── Format Rupiah ──────────────────────────────────────────
function rupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// ── Format Tanggal (ID) ────────────────────────────────────
function tglID(string $date): string {
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $d = explode('-', $date);
    return (int)$d[2] . ' ' . $bulan[(int)$d[1]] . ' ' . $d[0];
}

// ── Slug generator ─────────────────────────────────────────
function makeSlug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── Redirect ───────────────────────────────────────────────
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

// ── Order kode ────────────────────────────────────────────
function generateKode(): string {
    return 'NXT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
}

// ── Status badge helper ────────────────────────────────────
function statusBadge(string $status): string {
    $map = [
        'paid'      => ['label' => 'Lunas',     'class' => 'badge-success'],
        'pending'   => ['label' => 'Pending',   'class' => 'badge-warning'],
        'cancelled' => ['label' => 'Dibatalkan','class' => 'badge-danger'],
        'refunded'  => ['label' => 'Refund',    'class' => 'badge-info'],
        'upcoming'  => ['label' => 'Upcoming',  'class' => 'badge-info'],
        'ongoing'   => ['label' => 'Berlangsung','class' => 'badge-success'],
        'completed' => ['label' => 'Selesai',   'class' => 'badge-secondary'],
    ];
    $s = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-secondary'];
    return '<span class="badge ' . $s['class'] . '">' . $s['label'] . '</span>';
}

// ── Pagination helper ──────────────────────────────────────
function paginate(int $total, int $page, int $perPage, string $baseUrl): string {
    $total    = (int)$total;
    $page     = (int)$page;
    $perPage  = (int)$perPage;
    $totalPages = (int)ceil($total / $perPage);
    if ($totalPages <= 1) return '';

    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    $html = '<div class="pagination">';
    if ($page > 1)
        $html .= '<a href="' . $baseUrl . $sep . 'page=' . ($page-1) . '" class="page-btn">&#8592;</a>';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === $page ? ' active' : '';
        $html .= '<a href="' . $baseUrl . $sep . 'page=' . $i . '" class="page-btn' . $active . '">' . $i . '</a>';
    }
    if ($page < $totalPages)
        $html .= '<a href="' . $baseUrl . $sep . 'page=' . ($page+1) . '" class="page-btn">&#8594;</a>';
    $html .= '</div>';
    return $html;
}

// ── Is Admin ───────────────────────────────────────────────
function isAdmin(): bool {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin';
}

function isLoggedInAdmin(): bool {
    return isset($_SESSION['admin_id']);
}

function isLoggedInUser(): bool {
    return isset($_SESSION['user_id']);
}