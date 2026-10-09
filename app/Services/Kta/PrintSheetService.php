<?php

namespace App\Services\Kta;

use TCPDF;

class PrintSheetService
{
    /**
     * Standard sheet dimensions in millimeters
     */
    public const SHEET_A4 = ['w' => 210.0, 'h' => 297.0]; // Portrait
    public const SHEET_A3 = ['w' => 297.0, 'h' => 420.0]; // Portrait

    public const CARD_WIDTH_MM  = 85.60;
    public const CARD_HEIGHT_MM = 53.98;

    /**
     * Instance convenience method
     */
    public function renderPrintSheet(array $membershipIds, string $paperSize = 'A4', float $bleedMm = 0.0, bool $cropMarks = true, int $backRotate = 0): string
    {
        $membersDataList = [];
        foreach ($membershipIds as $id) {
            $membersDataList[] = CardImageRenderer::prepareMemberCardData((int)$id);
        }
        return self::generatePrintSheetPdfString($membersDataList, $paperSize, [
            'bleed_mm'      => $bleedMm,
            'crop_marks'    => $cropMarks,
            'back_rotation' => $backRotate,
        ]);
    }

    /**
     * Generate commercial print sheets (PDF) for multiple members
     *
     * @param array $membersDataList List of member card data
     * @param string $paperSize 'A4' or 'A3'
     * @param array $options [bleed_mm => 0|3, spacing_mm => 4, crop_marks => true, back_rotation => 0|180]
     * @return string PDF binary string
     */
    public static function generatePrintSheetPdfString(array $membersDataList, string $paperSize = 'A4', array $options = []): string
    {
        $paperSize = strtoupper($paperSize) === 'A3' ? 'A3' : 'A4';
        $sheetDim  = ($paperSize === 'A3') ? self::SHEET_A3 : self::SHEET_A4;

        $bleedMm    = (float) ($options['bleed_mm'] ?? 0.0);
        $spacingMm  = (float) ($options['spacing_mm'] ?? 5.0);
        $cropMarks  = (bool) ($options['crop_marks'] ?? true);
        $backRotate = (int) ($options['back_rotation'] ?? 0); // 0 or 180

        // Calculate grid capacity
        // A4: 2 cols x 5 rows = 10 cards per sheet
        // A3: 3 cols x 7 rows = 21 cards per sheet
        $cols = ($paperSize === 'A3') ? 3 : 2;
        $rows = ($paperSize === 'A3') ? 7 : 5;
        $cardsPerSheet = $cols * $rows;

        $totalW = ($cols * self::CARD_WIDTH_MM) + (($cols - 1) * $spacingMm);
        $totalH = ($rows * self::CARD_HEIGHT_MM) + (($rows - 1) * $spacingMm);

        $marginLeft = max(5.0, ($sheetDim['w'] - $totalW) / 2.0);
        $marginTop  = max(5.0, ($sheetDim['h'] - $totalH) / 2.0);

        $pdf = new TCPDF('P', 'mm', [$sheetDim['w'], $sheetDim['h']], true, 'UTF-8', false);
        $pdf->SetCreator('KOMEO.ID');
        $pdf->SetAuthor('KOMEO.ID');
        $pdf->SetTitle("Lembar Cetak KTA {$paperSize} - " . count($membersDataList) . ' Anggota');
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Chunk members by sheet capacity
        $batches = array_chunk($membersDataList, $cardsPerSheet);
        $sheetNumber = 1;

        foreach ($batches as $batchMembers) {
            // ==========================================
            // 1. FRONT SHEET (Halaman Sisi Depan)
            // ==========================================
            $pdf->AddPage('P', [$sheetDim['w'], $sheetDim['h']]);

            // Draw header sheet label for printers
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(100, 116, 139);
            $sheetLabel = "KOMEO.ID | Lembar Cetak {$paperSize} - DEPAN (Halaman {$sheetNumber}A) | Bleed: {$bleedMm}mm | Total Kartu: " . count($batchMembers);
            $pdf->Text($marginLeft, 4, $sheetLabel);

            // Render each member on Front Sheet
            foreach ($batchMembers as $idx => $memberData) {
                $c = $idx % $cols;
                $r = (int) floor($idx / $cols);

                $x = $marginLeft + ($c * (self::CARD_WIDTH_MM + $spacingMm));
                $y = $marginTop + ($r * (self::CARD_HEIGHT_MM + $spacingMm));

                // Embed front PNG
                $frontPng = CardImageRenderer::renderFrontPngString($memberData);
                $pdf->Image('@' . $frontPng, $x, $y, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);

                // Draw Crop Marks if enabled
                if ($cropMarks) {
                    self::drawCropMarks($pdf, $x, $y, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM);
                }
            }

            // ==========================================
            // 2. BACK SHEET (Halaman Sisi Belakang)
            // ==========================================
            // Duplex Alignment: The column position is horizontally reversed
            // so col 0 matches col (cols - 1), ensuring perfect front-back registration when cut!
            $pdf->AddPage('P', [$sheetDim['w'], $sheetDim['h']]);

            $backSheetLabel = "KOMEO.ID | Lembar Cetak {$paperSize} - BELAKANG (Halaman {$sheetNumber}B) | Posisi Duplex Sesuai Registrasi";
            $pdf->Text($marginLeft, 4, $backSheetLabel);

            $backSettings = ($backRotate === 180) ? ['back_orientation' => 'rotate180'] : [];

            foreach ($batchMembers as $idx => $memberData) {
                $c = $idx % $cols;
                $r = (int) floor($idx / $cols);

                // Horizontal mirror for duplex flip along vertical/long edge
                $backCol = ($cols - 1) - $c;

                $x = $marginLeft + ($backCol * (self::CARD_WIDTH_MM + $spacingMm));
                $y = $marginTop + ($r * (self::CARD_HEIGHT_MM + $spacingMm));

                $backPng = CardImageRenderer::renderBackPngString($memberData, $backSettings);
                $pdf->Image('@' . $backPng, $x, $y, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);

                if ($cropMarks) {
                    self::drawCropMarks($pdf, $x, $y, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM);
                }
            }

            $sheetNumber++;
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Draw corner crop marks around a card
     */
    private static function drawCropMarks(TCPDF $pdf, float $x, float $y, float $w, float $h, float $markLen = 2.8): void
    {
        $lineStyle = [
            'width' => 0.12,
            'cap'   => 'butt',
            'join'  => 'miter',
            'dash'  => 0,
            'color' => [15, 23, 42],
        ];

        $offset = 1.0; // gap from card edge

        // Top-Left corner
        $pdf->Line($x - $offset - $markLen, $y, $x - $offset, $y, $lineStyle);
        $pdf->Line($x, $y - $offset - $markLen, $x, $y - $offset, $lineStyle);

        // Top-Right corner
        $pdf->Line($x + $w + $offset, $y, $x + $w + $offset + $markLen, $y, $lineStyle);
        $pdf->Line($x + $w, $y - $offset - $markLen, $x + $w, $y - $offset, $lineStyle);

        // Bottom-Left corner
        $pdf->Line($x - $offset - $markLen, $y + $h, $x - $offset, $y + $h, $lineStyle);
        $pdf->Line($x, $y + $h + $offset, $x, $y + $h + $offset + $markLen, $lineStyle);

        // Bottom-Right corner
        $pdf->Line($x + $w + $offset, $y + $h, $x + $w + $offset + $markLen, $y + $h, $lineStyle);
        $pdf->Line($x + $w, $y + $h + $offset, $x + $w, $y + $h + $offset + $markLen, $lineStyle);
    }
}
