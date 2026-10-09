<?php

namespace App\Controllers;

use App\Models\EventCategoryModel;
use App\Models\HomepageBenefitModel;
use App\Models\MembershipModel;
use App\Services\DirectoryService;

class Home extends BaseController
{
    public function index(): string
    {
        $membershipModel = model(MembershipModel::class);
        $totalMembers = $membershipModel->where('status', 'active')->countAllResults();

        $db = \Config\Database::connect();
        $totalCities = $db->table('member_profiles')
            ->select('city')
            ->where('city IS NOT NULL')
            ->where('city !=', '')
            ->distinct()
            ->countAllResults();

        $benefitModel = model(HomepageBenefitModel::class);
        $benefits = $benefitModel->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Phase 5: Featured Members & Categories for Homepage Showcase
        $directoryService = new DirectoryService();
        $featuredLimit = (int) site_setting('Directory.featured_count', 6);
        $featuredMembers = $directoryService->getFeaturedMembers($featuredLimit);

        $categoryModel = model(EventCategoryModel::class);
        $categories = $categoryModel->getActiveCategories();

        $blacklistModel = model(\App\Models\BlacklistModel::class);
        $blacklists = $blacklistModel->getPublicList(15);

        return view('home/index', [
            'title'           => site_setting('App.meta_title', 'KOMEO.ID - Komunitas Event Indonesia'),
            'totalMembers'    => $totalMembers,
            'totalCities'     => $totalCities,
            'benefits'        => $benefits,
            'featuredMembers' => $featuredMembers,
            'categories'      => $categories,
            'blacklists'      => $blacklists,
        ]);
    }
}
