<?php
/**
 * Directory Pagination Partial
 * @var array $data
 * @var string $baseUrl
 */
$page       = (int) ($data['page'] ?? 1);
$totalPages = (int) ($data['total_pages'] ?? 1);
$total      = (int) ($data['total'] ?? 0);
$perPage    = (int) ($data['per_page'] ?? 12);
$filters    = $data['filters'] ?? [];

if ($totalPages <= 1) {
    return;
}

// Function to build query string preserving current filters but changing page
$buildPageUrl = static function (int $targetPage) use ($baseUrl, $filters): string {
    $q = array_merge($filters, ['page' => $targetPage]);
    // Filter out empty values
    $q = array_filter($q, static fn($v) => $v !== '' && $v !== null);
    return $baseUrl . '?' . http_build_query($q);
};
?>

<div class="pt-8 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200">
    <div class="text-xs text-slate-500 font-medium">
        Menampilkan halaman <span class="font-bold text-slate-800"><?= $page ?></span> dari <span class="font-bold text-slate-800"><?= $totalPages ?></span> (Total <?= number_format($total) ?> member terverifikasi)
    </div>

    <div class="flex items-center gap-1.5">
        <!-- Prev Button -->
        <?php if ($page > 1): ?>
            <a href="<?= esc($buildPageUrl($page - 1)) ?>" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Sebelumnya</span>
            </a>
        <?php else: ?>
            <span class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Sebelumnya</span>
            </span>
        <?php endif; ?>

        <!-- Page Number Links (sliding window) -->
        <div class="hidden sm:flex items-center gap-1">
            <?php
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);

            if ($start > 1) {
                echo '<a href="' . esc($buildPageUrl(1)) . '" class="w-9 h-9 rounded-xl text-xs font-bold flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 text-slate-700">1</a>';
                if ($start > 2) {
                    echo '<span class="px-1 text-slate-400 text-xs">...</span>';
                }
            }

            for ($p = $start; $p <= $end; $p++) {
                if ($p === $page) {
                    echo '<span class="w-9 h-9 rounded-xl text-xs font-bold flex items-center justify-center bg-brand-600 text-white shadow-xs">' . $p . '</span>';
                } else {
                    echo '<a href="' . esc($buildPageUrl($p)) . '" class="w-9 h-9 rounded-xl text-xs font-bold flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 transition">' . $p . '</a>';
                }
            }

            if ($end < $totalPages) {
                if ($end < $totalPages - 1) {
                    echo '<span class="px-1 text-slate-400 text-xs">...</span>';
                }
                echo '<a href="' . esc($buildPageUrl($totalPages)) . '" class="w-9 h-9 rounded-xl text-xs font-bold flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 text-slate-700">' . $totalPages . '</a>';
            }
            ?>
        </div>

        <!-- Next Button -->
        <?php if ($page < $totalPages): ?>
            <a href="<?= esc($buildPageUrl($page + 1)) ?>" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1">
                <span>Berikutnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        <?php else: ?>
            <span class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed flex items-center gap-1">
                <span>Berikutnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        <?php endif; ?>
    </div>
</div>
