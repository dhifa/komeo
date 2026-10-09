<?php

namespace App\Controllers;

use App\Models\EventCategoryModel;
use App\Models\SpecializationModel;
use App\Services\DirectoryService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class DirectoryController extends BaseController
{
    protected DirectoryService $directoryService;

    public function __construct()
    {
        $this->directoryService = new DirectoryService();
    }

    /**
     * Main Public Member Directory (/member)
     */
    public function member(): string|ResponseInterface
    {
        if (site_setting('Directory.enable_public', '1') !== '1') {
            return view('directory/disabled', [
                'title'   => 'Direktori Member - KOMEO.ID',
                'message' => 'Direktori member publik sedang dalam pemeliharaan berkala oleh administrator.',
            ]);
        }

        $params = [
            'q'              => $this->request->getGet('q'),
            'type'           => $this->request->getGet('type'),
            'category'       => $this->request->getGet('category'),
            'specialization' => $this->request->getGet('specialization'),
            'province'       => $this->request->getGet('province'),
            'city'           => $this->request->getGet('city'),
            'sort'           => $this->request->getGet('sort'),
            'page'           => $this->request->getGet('page'),
        ];

        $data = $this->directoryService->search($params);
        $options = $this->directoryService->getFilterOptions();

        // Get featured members for carousel/highlight if on page 1 and no specific query
        $featuredCount = (int) site_setting('Directory.featured_count', 6);
        $featuredMembers = ($data['page'] === 1 && empty($params['q']) && empty($params['specialization']))
            ? $this->directoryService->getFeaturedMembers($featuredCount)
            : [];

        return view('directory/member', [
            'title'           => site_setting('Directory.title', 'Temukan Profesional Event Terbaik di KOMEO') . ' - KOMEO.ID',
            'metaDescription' => site_setting('Directory.subtitle', 'Jelajahi jaringan Event Organizer, vendor, freelancer, dan talent dari berbagai daerah di Indonesia.'),
            'canonicalUrl'    => base_url('member'),
            'ogTitle'         => site_setting('Directory.title', 'Temukan Profesional Event Terbaik di KOMEO'),
            'ogDescription'   => site_setting('Directory.subtitle', 'Jelajahi jaringan Event Organizer, vendor, freelancer, dan talent dari berbagai daerah di Indonesia.'),
            'data'            => $data,
            'options'         => $options,
            'featuredMembers' => $featuredMembers,
        ]);
    }

    /**
     * Vendor Discovery Directory (/cari-vendor)
     */
    public function vendor(): string|ResponseInterface
    {
        if (site_setting('Directory.enable_vendor', '1') !== '1') {
            return view('directory/disabled', [
                'title'   => 'Cari Vendor Event - KOMEO.ID',
                'message' => 'Pencarian direktori vendor sedang dalam pemeliharaan berkala.',
            ]);
        }

        $params = [
            'q'              => $this->request->getGet('q'),
            'type'           => 'business', // strictly vendors / business accounts
            'category'       => $this->request->getGet('category'),
            'specialization' => $this->request->getGet('specialization'),
            'province'       => $this->request->getGet('province'),
            'city'           => $this->request->getGet('city'),
            'sort'           => $this->request->getGet('sort'),
            'page'           => $this->request->getGet('page'),
        ];

        $data = $this->directoryService->search($params);
        $options = $this->directoryService->getFilterOptions();

        return view('directory/vendor', [
            'title'           => 'Cari Vendor Event - KOMEO.ID',
            'metaDescription' => 'Temukan vendor dan perusahaan penyedia kebutuhan event di berbagai kota di Indonesia.',
            'canonicalUrl'    => base_url('cari-vendor'),
            'ogTitle'         => 'Cari Vendor Event - KOMEO.ID',
            'ogDescription'   => 'Temukan vendor dan perusahaan penyedia kebutuhan event di berbagai kota di Indonesia.',
            'data'            => $data,
            'options'         => $options,
        ]);
    }

    /**
     * Crew & Freelancer Discovery (/cari-crew)
     */
    public function crew(): string|ResponseInterface
    {
        if (site_setting('Directory.enable_crew', '1') !== '1') {
            return view('directory/disabled', [
                'title'   => 'Cari Crew & Freelancer Event - KOMEO.ID',
                'message' => 'Pencarian crew & freelancer sedang dalam pemeliharaan berkala.',
            ]);
        }

        $params = [
            'q'              => $this->request->getGet('q'),
            'type'           => 'individual', // strictly individual / freelancer accounts
            'category'       => $this->request->getGet('category'),
            'specialization' => $this->request->getGet('specialization'),
            'province'       => $this->request->getGet('province'),
            'city'           => $this->request->getGet('city'),
            'sort'           => $this->request->getGet('sort'),
            'page'           => $this->request->getGet('page'),
        ];

        $data = $this->directoryService->search($params);
        $options = $this->directoryService->getFilterOptions();

        return view('directory/crew', [
            'title'           => 'Cari Crew & Freelancer Event - KOMEO.ID',
            'metaDescription' => 'Temukan profesional dan freelancer untuk mendukung kebutuhan produksi event Anda.',
            'canonicalUrl'    => base_url('cari-crew'),
            'ogTitle'         => 'Cari Crew & Freelancer Event - KOMEO.ID',
            'ogDescription'   => 'Temukan profesional dan freelancer untuk mendukung kebutuhan produksi event Anda.',
            'data'            => $data,
            'options'         => $options,
        ]);
    }

    /**
     * Category Landing Page (/kategori/{slug})
     */
    public function category(string $slug): string|ResponseInterface
    {
        $slug = strtolower(trim($slug));
        $categoryModel = model(EventCategoryModel::class);
        $category = $categoryModel->where('slug', $slug)->where('is_active', 1)->first();

        if (! $category) {
            throw PageNotFoundException::forPageNotFound("Kategori '{$slug}' tidak ditemukan atau tidak aktif.");
        }

        $params = [
            'q'              => $this->request->getGet('q'),
            'type'           => $this->request->getGet('type'),
            'category'       => $slug,
            'specialization' => $this->request->getGet('specialization'),
            'province'       => $this->request->getGet('province'),
            'city'           => $this->request->getGet('city'),
            'sort'           => $this->request->getGet('sort'),
            'page'           => $this->request->getGet('page'),
        ];

        $data = $this->directoryService->search($params);
        $options = $this->directoryService->getFilterOptions();

        // Get related categories
        $relatedCategories = $categoryModel->where('slug !=', $slug)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->limit(6)
            ->findAll();

        $metaDesc = ! empty($category['description'])
            ? $category['description']
            : "Temukan vendor, event organizer, dan talenta profesional dalam sektor {$category['name']} di seluruh Indonesia melalui KOMEO.ID.";

        return view('directory/category', [
            'title'             => "{$category['name']} - Direktori Komunitas KOMEO.ID",
            'metaDescription'   => $metaDesc,
            'canonicalUrl'      => base_url("kategori/{$slug}"),
            'ogTitle'           => "{$category['name']} - Direktori Komunitas KOMEO.ID",
            'ogDescription'     => $metaDesc,
            'category'          => $category,
            'relatedCategories' => $relatedCategories,
            'data'              => $data,
            'options'           => $options,
        ]);
    }
}
