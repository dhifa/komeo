<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MembershipModel;
use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    /**
     * Admin Dashboard index
     */
    public function index(): RedirectResponse|string
    {
        $user = auth()->user();

        // Extra guard: Ensure only admin/superadmin can access
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses area Administrator.');
        }

        $membershipModel = model(MembershipModel::class);
        $stats = $membershipModel->getStats();
        $recentRegistrations = $membershipModel->getRecentWithDetails(10);

        return view('admin/dashboard', [
            'title'               => 'Panel Administrator - KOMEO.ID',
            'user'                => $user,
            'stats'               => $stats,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }
}
