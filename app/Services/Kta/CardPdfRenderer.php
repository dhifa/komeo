<?php

namespace App\Services\Kta;

use TCPDF;

class CardPdfRenderer
{
    public const CARD_WIDTH_MM  = 85.60;
    public const CARD_HEIGHT_MM = 53.98;

    /**
     * Instance convenience methods
     */
    public function renderIndividualPdf(int $membershipId, array $customSettings = []): string
    {
        $data = CardImageRenderer::prepareMemberCardData($membershipId);
        return self::renderSinglePdfString($data, $customSettings);
    }

    public function renderBulkPdf(array $membershipIds, array $customSettings = []): string
    {
        $dataList = [];
        foreach ($membershipIds as $id) {
            $dataList[] = CardImageRenderer::prepareMemberCardData((int)$id);
        }
        return self::renderBulkPdfString($dataList, $customSettings);
    }

    /**
     * Render an individual two-page CR80 PDF as binary string
     */
    public static function renderSinglePdfString(array $memberData, array $customSettings = []): string
    {
        $pdf = self::createBasePdf('KTA - ' . ($memberData['member_number'] ?? 'KOMEO'));

        // Page 1: Front
        $pdf->AddPage('L', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM]);
        $frontPng = CardImageRenderer::renderFrontPngString($memberData, $customSettings);
        $pdf->Image('@' . $frontPng, 0, 0, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);

        // Page 2: Back
        $pdf->AddPage('L', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM]);
        $backPng = CardImageRenderer::renderBackPngString($memberData, $customSettings);
        $pdf->Image('@' . $backPng, 0, 0, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);

        return $pdf->Output('', 'S');
    }

    /**
     * Render an individual two-page CR80 PDF directly to file
     */
    public static function renderSinglePdfFile(array $memberData, string $savePath, array $customSettings = []): bool
    {
        $dir = dirname($savePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $content = self::renderSinglePdfString($memberData, $customSettings);
        return (file_put_contents($savePath, $content) !== false);
    }

    /**
     * Render a bulk multi-page CR80 PDF containing all selected members
     * Ordering:
     * Member 1 Front, Member 1 Back, Member 2 Front, Member 2 Back...
     */
    public static function renderBulkPdfString(array $membersDataList, array $customSettings = []): string
    {
        $pdf = self::createBasePdf('KTA Batch - KOMEO.ID (' . count($membersDataList) . ' Anggota)');

        foreach ($membersDataList as $memberData) {
            // Front Page
            $pdf->AddPage('L', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM]);
            $frontPng = CardImageRenderer::renderFrontPngString($memberData, $customSettings);
            $pdf->Image('@' . $frontPng, 0, 0, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);

            // Back Page
            $pdf->AddPage('L', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM]);
            $backPng = CardImageRenderer::renderBackPngString($memberData, $customSettings);
            $pdf->Image('@' . $backPng, 0, 0, self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM, 'PNG', '', '', false, 300, '', false, false, 0);
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Render a bulk multi-page CR80 PDF directly to file
     */
    public static function renderBulkPdfFile(array $membersDataList, string $savePath, array $customSettings = []): bool
    {
        $dir = dirname($savePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $content = self::renderBulkPdfString($membersDataList, $customSettings);
        return (file_put_contents($savePath, $content) !== false);
    }

    /**
     * Create base TCPDF instance configured for zero-margin CR80 cards
     */
    private static function createBasePdf(string $title): TCPDF
    {
        $pdf = new TCPDF('L', 'mm', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM], true, 'UTF-8', false);
        $pdf->SetCreator('KOMEO.ID');
        $pdf->SetAuthor('KOMEO.ID - Komunitas Event Organizer Indonesia');
        $pdf->SetTitle($title);
        $pdf->SetSubject('Kartu Tanda Anggota Resmi KOMEO.ID');
        $pdf->SetKeywords('KTA, KOMEO, ID-1, CR80, Membership Card');

        // Zero margins for exact card border
        $pdf->SetMargins(0, 0, 0, true);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        return $pdf;
    }
}
