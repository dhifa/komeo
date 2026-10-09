<?php

namespace App\Services\Kta;

use App\Models\KtaExportBatchModel;
use App\Models\KtaExportLogModel;
use App\Models\MembershipModel;
use ZipArchive;

class BulkCardExportService
{
    public const MAX_BATCH_SIZE = 500;

    /**
     * Generate bulk KTA export package according to selected mode
     *
     * @param array $membershipIds List of membership IDs to export
     * @param string $mode 'zip_png' | 'pdf_bulk' | 'zip_complete' | 'print_sheet_a4' | 'print_sheet_a3'
     * @param int $adminId ID of the executing administrator
     * @param array $options [bleed_mm => 0, back_rotation => 0|180, crop_marks => true]
     * @return array [success => bool, batch_code => string, file_path => string, file_name => string, file_size => int, message => string]
     */
    public static function processExport(array $membershipIds, string $mode, mixed $param3 = 1, mixed $param4 = []): array
    {
        if (is_array($param3)) {
            $options = $param3;
            $adminId = is_numeric($param4) ? (int) $param4 : 1;
        } else {
            $adminId = is_numeric($param3) ? (int) $param3 : 1;
            $options = is_array($param4) ? $param4 : [];
        }

        if (empty($membershipIds)) {
            return ['success' => false, 'message' => 'Tidak ada anggota yang dipilih untuk ekspor.'];
        }

        if (count($membershipIds) > self::MAX_BATCH_SIZE) {
            $membershipIds = array_slice($membershipIds, 0, self::MAX_BATCH_SIZE);
        }

        // Normalize mode aliases
        $mode = match ($mode) {
            'pdf_multipage' => 'pdf_bulk',
            'package_zip'   => 'zip_complete',
            'sheet_a4'      => 'print_sheet_a4',
            'sheet_a3'      => 'print_sheet_a3',
            default         => $mode,
        };

        // Validate and load active members only
        $membershipModel = model(MembershipModel::class);
        $memberships = $membershipModel->whereIn('id', $membershipIds)->findAll();

        $activeDataList = [];
        foreach ($memberships as $m) {
            // Strict security check: Exclude non-active members from official active exports
            if ($m->status !== 'active') {
                continue;
            }
            $activeDataList[] = CardImageRenderer::prepareMemberCardData((int) $m->id);
        }

        if (empty($activeDataList)) {
            return ['success' => false, 'message' => 'Tidak ada anggota berstatus aktif yang valid dari daftar pilihan.'];
        }

        $batchCode = 'KTA_' . date('Ymd_His') . '_' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $exportDir = WRITEPATH . 'exports/' . $batchCode . '/';
        if (! is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $outputFilePath = '';
        $outputFileName = '';

        try {
            switch ($mode) {
                case 'zip_png':
                    // Mode 1: ZIP containing front and back PNGs
                    $outputFileName = 'KOMEO_KTA_PNG_' . date('Ymd_His') . '.zip';
                    $outputFilePath = $exportDir . $outputFileName;
                    self::createZipPng($activeDataList, $outputFilePath, $options);
                    break;

                case 'pdf_bulk':
                    // Mode 2: Multi-page CR80 PDF
                    $outputFileName = 'KOMEO_KTA_CR80_' . date('Ymd_His') . '.pdf';
                    $outputFilePath = $exportDir . $outputFileName;
                    CardPdfRenderer::renderBulkPdfFile($activeDataList, $outputFilePath, $options);
                    break;

                case 'zip_complete':
                    // Mode 3: Complete Package (PNG + PDF + Manifest CSV)
                    $outputFileName = 'KOMEO_KTA_PAKET_LENGKAP_' . date('Ymd_His') . '.zip';
                    $outputFilePath = $exportDir . $outputFileName;
                    self::createZipCompletePackage($activeDataList, $outputFilePath, $options);
                    break;

                case 'print_sheet_a4':
                    // Mode 4: A4 Print Sheet PDF
                    $outputFileName = 'KOMEO_KTA_LEMBAR_A4_' . date('Ymd_His') . '.pdf';
                    $outputFilePath = $exportDir . $outputFileName;
                    $pdfContent = PrintSheetService::generatePrintSheetPdfString($activeDataList, 'A4', $options);
                    file_put_contents($outputFilePath, $pdfContent);
                    break;

                case 'print_sheet_a3':
                    // Mode 5: A3 Print Sheet PDF
                    $outputFileName = 'KOMEO_KTA_LEMBAR_A3_' . date('Ymd_His') . '.pdf';
                    $outputFilePath = $exportDir . $outputFileName;
                    $pdfContent = PrintSheetService::generatePrintSheetPdfString($activeDataList, 'A3', $options);
                    file_put_contents($outputFilePath, $pdfContent);
                    break;

                default:
                    throw new \InvalidArgumentException('Mode ekspor tidak valid: ' . $mode);
            }

            $fileSize = file_exists($outputFilePath) ? (int) filesize($outputFilePath) : 0;

            // Record batch in kta_export_batches table
            $batchModel = model(KtaExportBatchModel::class);
            $batchModel->insert([
                'admin_id'     => $adminId,
                'batch_code'   => $batchCode,
                'export_mode'  => $mode,
                'paper_size'   => str_contains($mode, 'a3') ? 'A3' : (str_contains($mode, 'a4') ? 'A4' : 'CR80'),
                'member_count' => count($activeDataList),
                'file_path'    => $outputFilePath,
                'file_name'    => $outputFileName,
                'file_size'    => $fileSize,
                'status'       => 'completed',
            ]);

            // Audit log
            $logModel = model(KtaExportLogModel::class);
            $req = service('request');
            $ip = method_exists($req, 'getIPAddress') ? $req->getIPAddress() : '127.0.0.1';
            $ua = method_exists($req, 'getUserAgent') ? $req->getUserAgent()?->getAgentString() : 'CLI/System';
            $logModel->logExport($adminId, null, $mode, $ip, $ua);

            return [
                'success'      => true,
                'batch_code'   => $batchCode,
                'file_path'    => $outputFilePath,
                'file_name'    => $outputFileName,
                'filename'     => $outputFileName,
                'file_size'    => $fileSize,
                'member_count' => count($activeDataList),
                'message'      => 'Ekspor massal berhasil diproses (' . count($activeDataList) . ' anggota).',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Bulk export failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal memproses ekspor KTA: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create ZIP containing front and back PNGs
     */
    private static function createZipPng(array $membersList, string $zipPath, array $options): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Tidak dapat membuat file arsip ZIP.');
        }

        foreach ($membersList as $data) {
            $baseName = self::sanitizeFilename($data['member_number'] . '_' . $data['full_name']);

            $frontPng = CardImageRenderer::renderFrontPngString($data, $options);
            $backPng  = CardImageRenderer::renderBackPngString($data, $options);

            $zip->addFromString($baseName . '_DEPAN.png', $frontPng);
            $zip->addFromString($baseName . '_BELAKANG.png', $backPng);
        }

        $zip->close();
    }

    /**
     * Create complete print package (PNG + PDF + Manifest CSV)
     */
    private static function createZipCompletePackage(array $membersList, string $zipPath, array $options): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Tidak dapat membuat file arsip ZIP.');
        }

        $manifestRows = [];
        $manifestRows[] = [
            'Nomor Anggota',
            'Nama Anggota',
            'Tipe Anggota',
            'Nama Bisnis',
            'Kategori',
            'Domisili',
            'Tahun Bergabung',
            'Status',
            'File PNG Depan',
            'File PNG Belakang',
            'File PDF CR80',
        ];

        foreach ($membersList as $data) {
            $baseName = self::sanitizeFilename($data['member_number'] . '_' . $data['full_name']);

            $frontPngName = 'PNG/' . $baseName . '_DEPAN.png';
            $backPngName  = 'PNG/' . $baseName . '_BELAKANG.png';
            $pdfName      = 'PDF/' . $baseName . '.pdf';

            // Add PNGs
            $frontPng = CardImageRenderer::renderFrontPngString($data, $options);
            $backPng  = CardImageRenderer::renderBackPngString($data, $options);
            $zip->addFromString($frontPngName, $frontPng);
            $zip->addFromString($backPngName, $backPng);

            // Add individual 2-page PDF
            $pdfContent = CardPdfRenderer::renderSinglePdfString($data, $options);
            $zip->addFromString($pdfName, $pdfContent);

            // Add to manifest with CSV formula injection protection
            $manifestRows[] = [
                self::escapeCsv($data['member_number']),
                self::escapeCsv($data['full_name']),
                self::escapeCsv($data['member_type']),
                self::escapeCsv($data['business_name'] ?? '-'),
                self::escapeCsv($data['category_name']),
                self::escapeCsv($data['city']),
                self::escapeCsv($data['joined_year']),
                self::escapeCsv($data['status']),
                self::escapeCsv($frontPngName),
                self::escapeCsv($backPngName),
                self::escapeCsv($pdfName),
            ];
        }

        // Generate manifest.csv content
        $csvContent = '';
        foreach ($manifestRows as $row) {
            $csvContent .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\r\n";
        }
        $zip->addFromString('manifest.csv', $csvContent);

        $zip->close();
    }

    /**
     * Sanitize string for filesystem safety
     */
    public static function sanitizeFilename(string $string): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $string);
        $clean = preg_replace('/_+/', '_', (string) $clean);
        return trim((string) $clean, '_');
    }

    /**
     * Escape CSV values to prevent spreadsheet formula injection attacks (=, +, -, @)
     */
    public static function escapeCsv(string $val): string
    {
        $trimmed = trim($val);
        if (in_array(substr($trimmed, 0, 1), ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $trimmed;
        }
        return $trimmed;
    }
}
