<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use App\Models\KtaExportBatchModel;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use App\Services\Kta\BulkCardExportService;
use App\Services\Kta\CardImageRenderer;
use App\Services\Kta\CardPdfRenderer;
use App\Services\MembershipVerificationService;

class KtaController extends BaseController
{
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;
    protected EventCategoryModel $categoryModel;
    protected KtaExportBatchModel $batchModel;
    protected CardImageRenderer $imageRenderer;
    protected CardPdfRenderer $pdfRenderer;
    protected BulkCardExportService $bulkService;
    protected MembershipVerificationService $verificationService;

    public function __construct()
    {
        $this->membershipModel = new MembershipModel();
        $this->profileModel = new MemberProfileModel();
        $this->categoryModel = new EventCategoryModel();
        $this->batchModel = new KtaExportBatchModel();
        $this->imageRenderer = new CardImageRenderer();
        $this->pdfRenderer = new CardPdfRenderer();
        $this->bulkService = new BulkCardExportService();
        $this->verificationService = new MembershipVerificationService();
    }

    /**
     * List members for KTA Management
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('memberships m')
            ->select('m.*, m.member_number as membership_number, p.full_name, p.business_name, p.business_name as company_name, p.member_type, p.city, p.photo_path, p.photo_path as avatar, c.name as category_name, u.username')
            ->join('member_profiles p', 'p.user_id = m.user_id', 'left')
            ->join('event_categories c', 'c.id = p.category_id', 'left')
            ->join('users u', 'u.id = m.user_id', 'left');

        // Search
        $search = trim((string) $this->request->getGet('q'));
        if ($search !== '') {
            $builder->groupStart()
                ->like('m.member_number', $search)
                ->orLike('p.full_name', $search)
                ->orLike('p.business_name', $search)
                ->orLike('u.username', $search)
                ->groupEnd();
        }

        // Status Filter
        $status = trim((string) $this->request->getGet('status'));
        if ($status !== '' && in_array($status, ['pending', 'active', 'rejected', 'suspended', 'expired'], true)) {
            $builder->where('m.status', $status);
        }

        // Category Filter
        $categoryId = (int) $this->request->getGet('category_id');
        if ($categoryId > 0) {
            $builder->where('p.category_id', $categoryId);
        }

        // Clone builder for count
        $countBuilder = clone $builder;
        $totalMembers = $countBuilder->countAllResults();

        // Pagination
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $members = $builder->orderBy('m.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();

        return view('admin/kta/index', [
            'title'       => 'Manajemen KTA Digital',
            'members'     => $members,
            'categories'  => $categories,
            'search'      => $search,
            'status'      => $status,
            'categoryId'  => $categoryId,
            'currentPage' => $page,
            'perPage'     => $perPage,
            'total'       => $totalMembers,
            'totalPages'  => (int) ceil($totalMembers / $perPage),
        ]);
    }

    /**
     * Preview Card Front or Back for Admin Modal/Viewer
     */
    public function previewCard(int $membershipId, string $side = 'front')
    {
        $membership = $this->membershipModel->find($membershipId);
        if (!$membership) {
            return $this->response->setStatusCode(404)->setBody('Keanggotaan tidak ditemukan.');
        }

        $imageStream = ($side === 'back')
            ? $this->imageRenderer->renderBack($membershipId)
            : $this->imageRenderer->renderFront($membershipId);

        return $this->response
            ->setContentType('image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($imageStream);
    }

    /**
     * Individual KTA Download for Admin
     */
    public function downloadSingle(int $membershipId, string $format = 'pdf')
    {
        $membership = $this->membershipModel->find($membershipId);
        if (!$membership) {
            return redirect()->back()->with('error', 'Keanggotaan tidak ditemukan.');
        }

        $profile = $this->profileModel->getByUserId($membership['user_id']);
        $memberName = $profile['full_name'] ?? 'Member';

        $memberNumber = $membership['member_number'] ?? $membership['membership_number'] ?? 'KMO-2026-000000';

        if ($format === 'front') {
            $safeName = preg_replace('/[^\w\-]+/u', '_', $memberNumber . '_' . $memberName . '_DEPAN.png');
            $data = $this->imageRenderer->renderFront($membershipId);
            return $this->response
                ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
                ->setContentType('image/png')
                ->setBody($data);
        }

        if ($format === 'back') {
            $safeName = preg_replace('/[^\w\-]+/u', '_', $memberNumber . '_' . $memberName . '_BELAKANG.png');
            $data = $this->imageRenderer->renderBack($membershipId);
            return $this->response
                ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
                ->setContentType('image/png')
                ->setBody($data);
        }

        // PDF
        $safeName = preg_replace('/[^\w\-]+/u', '_', $memberNumber . '_' . $memberName . '_KTA.pdf');
        $pdf = $this->pdfRenderer->renderIndividualPdf($membershipId);
        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
            ->setContentType('application/pdf')
            ->setBody($pdf);
    }

    /**
     * Bulk KTA Selection & Printing Interface
     */
    public function bulk()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('memberships m')
            ->select('m.*, m.member_number as membership_number, p.full_name, p.business_name, p.business_name as company_name, p.city, c.name as category_name, u.username')
            ->join('member_profiles p', 'p.user_id = m.user_id', 'left')
            ->join('event_categories c', 'c.id = p.category_id', 'left')
            ->join('users u', 'u.id = m.user_id', 'left');

        // Filters
        $search = trim((string) $this->request->getGet('q'));
        if ($search !== '') {
            $builder->groupStart()
                ->like('m.member_number', $search)
                ->orLike('p.full_name', $search)
                ->orLike('p.business_name', $search)
                ->orLike('p.city', $search)
                ->groupEnd();
        }

        $status = trim((string) $this->request->getGet('status'));
        if ($status !== '' && in_array($status, ['pending', 'active', 'rejected', 'suspended', 'expired'], true)) {
            $builder->where('m.status', $status);
        } else {
            // Default to active for mass printing
            $builder->where('m.status', 'active');
            $status = 'active';
        }

        $categoryId = (int) $this->request->getGet('category_id');
        if ($categoryId > 0) {
            $builder->where('p.category_id', $categoryId);
        }

        $city = trim((string) $this->request->getGet('city'));
        if ($city !== '') {
            $builder->like('p.city', $city);
        }

        $members = $builder->orderBy('m.member_number', 'ASC')
            ->limit(500)
            ->get()
            ->getResultArray();

        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $recentBatches = $this->batchModel->orderBy('id', 'DESC')->limit(10)->findAll();

        return view('admin/kta/bulk', [
            'title'         => 'Cetak KTA Massal',
            'members'       => $members,
            'categories'    => $categories,
            'search'        => $search,
            'status'        => $status,
            'categoryId'    => $categoryId,
            'city'          => $city,
            'recentBatches' => $recentBatches,
        ]);
    }

    /**
     * Process Bulk Export Action
     */
    public function processBulkExport()
    {
        $membershipIds = $this->request->getPost('membership_ids');
        if (empty($membershipIds) || !is_array($membershipIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu anggota untuk dicetak.');
        }

        $membershipIds = array_map('intval', $membershipIds);
        $exportFormat = trim((string) $this->request->getPost('export_format') ?: 'zip_png');
        $bleedMm = (float) ($this->request->getPost('bleed_mm') ?: 0.0);
        $showCropMarks = (bool) $this->request->getPost('crop_marks');
        $backRotation = (int) ($this->request->getPost('back_rotation') ?: 0);

        $options = [
            'bleed_mm'      => $bleedMm,
            'crop_marks'    => $showCropMarks,
            'back_rotation' => $backRotation,
        ];

        try {
            $adminId = (int) (auth()->id() ?? 1);
            $result = $this->bulkService->processExport($membershipIds, $exportFormat, $adminId, $options);

            if (empty($result['success'])) {
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal memproses ekspor KTA.');
            }

            $filePath = $result['file_path'] ?? null;
            if (!$filePath || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'Gagal menemukan berkas hasil cetak massal.');
            }

            $downloadFilename = $result['file_name'] ?? $result['filename'] ?? basename($filePath);
            return $this->response->download($filePath, null)->setFileName($downloadFilename);
        } catch (\Throwable $e) {
            log_message('error', 'Bulk export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses ekspor KTA: ' . $e->getMessage());
        }
    }

    /**
     * Download Existing Batch
     */
    public function downloadBatch(string $batchCode)
    {
        $batch = $this->batchModel->where('batch_code', $batchCode)->first();
        if (!$batch) {
            return redirect()->to(site_url('admin/kta/bulk'))->with('error', 'Batch cetak tidak ditemukan.');
        }

        $filePath = WRITEPATH . 'exports/kta/' . $batch['file_name'];
        if (!file_exists($filePath)) {
            return redirect()->to(site_url('admin/kta/bulk'))->with('error', 'Berkas batch telah kadaluarsa atau dihapus.');
        }

        return $this->response->download($filePath, null)->setFileName($batch['file_name']);
    }
}
