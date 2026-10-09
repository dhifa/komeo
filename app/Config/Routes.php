<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('member', 'DirectoryController::member');
$routes->get('cari-vendor', 'DirectoryController::vendor');
$routes->get('cari-crew', 'DirectoryController::crew');
$routes->get('kategori/(:segment)', 'DirectoryController::category/$1');
$routes->get('member/(:segment)', 'MemberController::publicProfile/$1');
$routes->get('verifikasi/(:segment)', 'VerificationController::verify/$1');

// Phase 5.5 Public Event Routes
$routes->get('kegiatan', 'EventController::index');
$routes->get('kegiatan/(:segment)/daftar', 'EventController::registerGuest/$1');
$routes->post('kegiatan/(:segment)/daftar', 'EventController::processGuestRegistration/$1');
$routes->get('kegiatan/verifikasi-email/(:segment)', 'EventController::verifyGuestEmail/$1');
$routes->get('kegiatan/tiket/(:segment)/png', 'EventController::downloadTicketPng/$1');
$routes->get('kegiatan/tiket/(:segment)/pdf', 'EventController::downloadTicketPdf/$1');
$routes->get('kegiatan/tiket/(:segment)', 'EventController::ticketView/$1');
$routes->get('kegiatan/(:segment)', 'EventController::show/$1');

// Phase 5.6 Client Inquiry & Conversation Public Routes
$routes->get('member/(:segment)/hubungi', 'InquiryController::contactMember/$1');
$routes->post('member/(:segment)/hubungi', 'InquiryController::submitInquiry/$1');
$routes->get('inquiry/verify/(:segment)', 'InquiryController::verifyClientEmail/$1');
$routes->get('inquiry/conversation/(:segment)', 'InquiryController::conversation/$1');
$routes->post('inquiry/conversation/(:segment)/reply', 'InquiryController::clientReply/$1');
$routes->get('shared-document/(:segment)', 'InquiryController::viewSharedDocument/$1');

// Authentication Routes
$routes->get('login', 'Auth\LoginController::loginView');
$routes->post('login', 'Auth\LoginController::loginAction');
$routes->get('register', 'Auth\RegisterController::registerView');
$routes->post('register', 'Auth\RegisterController::registerAction');
$routes->get('logout', 'Auth\LoginController::logoutAction');
$routes->get('forgot-password', 'Auth\PasswordResetController::forgotPasswordView');
$routes->post('forgot-password', 'Auth\PasswordResetController::forgotPasswordAction');

// Member Protected Routes
$routes->group('dashboard', ['filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    
    // Profile Management
    $routes->get('profil', 'Member\ProfileController::show');
    $routes->get('profil/edit', 'Member\ProfileController::edit');
    $routes->post('profil/update', 'Member\ProfileController::update');

    // Portfolio Management (Phase 2 Image Portfolios)
    $routes->get('portofolio', 'Member\PortfolioController::index');
    $routes->post('portofolio/save', 'Member\PortfolioController::save');
    $routes->post('portofolio/delete/(:num)', 'Member\PortfolioController::delete/$1');

    // Digital Membership Card (KTA)
    $routes->get('kta', 'Member\KtaController::index');
    $routes->get('kta/preview/(:segment)', 'Member\KtaController::preview/$1');
    $routes->get('kta/download/front', 'Member\KtaController::downloadFront');
    $routes->get('kta/download/back', 'Member\KtaController::downloadBack');
    $routes->get('kta/download/pdf', 'Member\KtaController::downloadPdf');

    // Phase 5.5 Member Event Management
    $routes->get('kegiatan', 'Member\EventController::index');
    $routes->post('kegiatan/daftar/(:num)', 'Member\EventController::register/$1');
    $routes->post('kegiatan/batal/(:num)', 'Member\EventController::cancel/$1');

    // Phase 5.6 Professional Documents (CV & Portfolio PDFs)
    $routes->get('dokumen', 'Member\DocumentController::index');
    $routes->post('dokumen/cv', 'Member\DocumentController::uploadCv');
    $routes->post('dokumen/portfolio/pdf', 'Member\DocumentController::uploadPortfolioPdf');
    $routes->post('dokumen/portfolio/external', 'Member\DocumentController::addExternalLink');
    $routes->post('dokumen/visibility/(:num)', 'Member\DocumentController::updateVisibility/$1');
    $routes->get('dokumen/download/(:num)', 'Member\DocumentController::download/$1');
    $routes->post('dokumen/delete/(:num)', 'Member\DocumentController::delete/$1');

    // Phase 5.6 Member Inquiries & Messages
    $routes->get('permintaan', 'Member\InquiryController::index');
    $routes->get('permintaan/(:num)', 'Member\InquiryController::show/$1');
    $routes->post('permintaan/(:num)/reply', 'Member\InquiryController::reply/$1');
    $routes->post('permintaan/(:num)/status', 'Member\InquiryController::updateStatus/$1');
    $routes->post('permintaan/(:num)/share-document', 'Member\InquiryController::shareDocument/$1');
    $routes->post('permintaan/(:num)/revoke-share/(:num)', 'Member\InquiryController::revokeShare/$1/$2');

    // Extension: Member Identity & Business Verification
    $routes->get('verifikasi-identitas', 'Member\VerificationController::index');
    $routes->post('verifikasi-identitas/submit', 'Member\VerificationController::submit');
    $routes->get('verifikasi-identitas/dokumen/(:segment)', 'Member\VerificationController::previewMyDocument/$1');
});

// Admin Protected Routes (defense-in-depth: session filter + group filter + controller check)
$routes->group('admin', ['filter' => ['session', 'group:admin,superadmin,moderator,editor,event_staff']], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');
    // Member Management & Approval System
    $routes->get('members', 'Admin\MemberController::index');
    $routes->get('members/view/(:num)', 'Admin\MemberController::show/$1');
    $routes->post('members/approve/(:num)', 'Admin\MemberController::approve/$1');
    $routes->post('members/reject/(:num)', 'Admin\MemberController::reject/$1');
    $routes->post('members/suspend/(:num)', 'Admin\MemberController::suspend/$1');
    $routes->post('members/reactivate/(:num)', 'Admin\MemberController::reactivate/$1');
    $routes->post('members/reset-password/(:num)', 'Admin\MemberController::resetPassword/$1');
    $routes->post('members/warning/issue/(:num)', 'Admin\MemberController::issueWarning/$1');
    $routes->post('members/warning/resolve/(:num)', 'Admin\MemberController::resolveWarning/$1');
    $routes->post('members/warning/revoke/(:num)', 'Admin\MemberController::revokeWarning/$1');
    $routes->post('members/warning/delete/(:num)', 'Admin\MemberController::deleteWarning/$1');

    // Phase 5 Extension: Custom Member Badge Management (Module A)
    $routes->get('badges', 'Admin\BadgeController::index');
    $routes->get('badges/create', 'Admin\BadgeController::create');
    $routes->post('badges/create', 'Admin\BadgeController::store');
    $routes->get('badges/edit/(:num)', 'Admin\BadgeController::edit/$1');
    $routes->post('badges/edit/(:num)', 'Admin\BadgeController::update/$1');
    $routes->post('badges/toggle/(:num)', 'Admin\BadgeController::toggle/$1');
    $routes->get('badges/members/(:num)', 'Admin\BadgeController::members/$1');
    $routes->post('badges/members/(:num)/assign', 'Admin\BadgeController::assignMember/$1');
    $routes->post('badges/members/(:num)/revoke/(:num)', 'Admin\BadgeController::revokeMember/$1/$2');

    // Phase 5 Extension: Member Badge Assignment via Member Detail
    $routes->post('members/badges/assign/(:num)', 'Admin\MemberController::assignBadge/$1');
    $routes->post('members/badges/revoke/(:num)', 'Admin\MemberController::revokeBadge/$1');

    // Phase 5 Extension: Manual Membership Activation & Contact Workflow (Module B)
    $routes->post('members/activation/confirm-contact/(:num)', 'Admin\MemberController::confirmContact/$1');
    $routes->post('members/activation/approve/(:num)', 'Admin\MemberController::approveActivation/$1');
    $routes->post('members/activation/reject/(:num)', 'Admin\MemberController::rejectActivation/$1');
    $routes->get('settings/membership', 'Admin\MembershipSettingsController::index');
    $routes->post('settings/membership', 'Admin\MembershipSettingsController::update');

    // Extension: Business & Member Identity Verification Management
    $routes->get('identity-verifications', 'Admin\IdentityVerificationController::index');
    $routes->get('identity-verifications/view/(:num)', 'Admin\IdentityVerificationController::show/$1');
    $routes->post('identity-verifications/approve/(:num)', 'Admin\IdentityVerificationController::approve/$1');
    $routes->post('identity-verifications/reject/(:num)', 'Admin\IdentityVerificationController::reject/$1');
    $routes->post('identity-verifications/revoke/(:num)', 'Admin\IdentityVerificationController::revoke/$1');
    $routes->post('identity-verifications/delete-documents/(:num)', 'Admin\IdentityVerificationController::deleteDocuments/$1');
    $routes->get('identity-verifications/document/(:num)/(:segment)', 'Admin\IdentityVerificationController::viewDocument/$1/$2');

    // Digital Membership Card (KTA) & Mass Printing
    $routes->get('kta', 'Admin\KtaController::index');
    $routes->get('kta/preview/(:num)/(:segment)', 'Admin\KtaController::previewCard/$1/$2');
    $routes->get('kta/download/(:num)/(:segment)', 'Admin\KtaController::downloadSingle/$1/$2');
    $routes->get('kta/bulk', 'Admin\KtaController::bulk');
    $routes->post('kta/bulk/export', 'Admin\KtaController::processBulkExport');
    $routes->get('kta/batch/download/(:segment)', 'Admin\KtaController::downloadBatch/$1');

    // Phase 5.5 Admin KTA Scanner
    $routes->get('kta/scan', 'Admin\KtaScannerController::index');
    $routes->post('kta/scan/verify', 'Admin\KtaScannerController::verify');

    // Phase 5.5 Admin Event Management
    $routes->get('events', 'Admin\EventController::index');
    $routes->get('events/create', 'Admin\EventController::create');
    $routes->post('events/create', 'Admin\EventController::store');
    $routes->get('events/(:num)/edit', 'Admin\EventController::edit/$1');
    $routes->post('events/(:num)/edit', 'Admin\EventController::update/$1');
    $routes->get('events/(:num)/participants', 'Admin\EventController::participants/$1');
    $routes->post('events/(:num)/participants/(:num)/approve', 'Admin\EventController::approveParticipant/$1/$2');
    $routes->post('events/(:num)/participants/(:num)/reject', 'Admin\EventController::rejectParticipant/$1/$2');
    $routes->get('events/(:num)/attendance', 'Admin\EventController::attendance/$1');
    $routes->get('events/(:num)/attendance/csv', 'Admin\EventController::exportCsv/$1');
    $routes->get('events/(:num)/checkin', 'Admin\EventController::checkin/$1');
    $routes->post('events/(:num)/checkin', 'Admin\EventController::processCheckin/$1');
    $routes->get('events/(:num)/search-participant', 'Admin\EventController::searchParticipant/$1');

    // Phase 5.6 KOMEO Connect Management
    $routes->get('connect', 'Admin\ConnectController::index');
    $routes->post('connect/settings', 'Admin\ConnectController::updateSettings');
    $routes->post('connect/inquiry/(:num)/status', 'Admin\ConnectController::updateInquiryStatus/$1');

    // KTA Design & Print Settings
    $routes->get('settings/kta', 'Admin\KtaSettingsController::index');
    $routes->get('settings/kta/preview-sample/(:segment)', 'Admin\KtaSettingsController::previewSample/$1');
    $routes->post('settings/kta/update/(:segment)', 'Admin\KtaSettingsController::update/$1');
    $routes->post('settings/kta/delete-image/(:segment)', 'Admin\KtaSettingsController::deleteImage/$1');

    // Industry Categories Management
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->post('categories/save', 'Admin\CategoryController::save');
    $routes->post('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');

    // Specializations Management
    $routes->get('specializations', 'Admin\SpecializationController::index');
    $routes->post('specializations/save', 'Admin\SpecializationController::save');
    $routes->post('specializations/delete/(:num)', 'Admin\SpecializationController::delete/$1');

    // Website Settings
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings/update/(:segment)', 'Admin\SettingsController::update/$1');
    $routes->post('settings/reset-colors', 'Admin\SettingsController::resetColors');
    $routes->post('settings/delete-image/(:segment)', 'Admin\SettingsController::deleteImage/$1');

    // Homepage Content Management
    $routes->get('content/homepage', 'Admin\ContentController::homepage');
    $routes->post('content/homepage/update', 'Admin\ContentController::updateHomepage');
    $routes->post('content/homepage/benefit/save', 'Admin\ContentController::benefitSave');
    $routes->post('content/homepage/benefit/delete/(:num)', 'Admin\ContentController::benefitDelete/$1');

    // Professional Directory Management (Phase 5)
    $routes->get('directory', 'Admin\DirectoryController::index');
    $routes->post('directory/feature/(:num)', 'Admin\DirectoryController::toggleFeature/$1');
    $routes->post('directory/visibility/(:num)', 'Admin\DirectoryController::toggleVisibility/$1');
    $routes->get('settings/directory', 'Admin\DirectoryController::settings');
    $routes->post('settings/directory', 'Admin\DirectoryController::updateSettings');

    // User & Custom Role Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->post('users/role/(:num)', 'Admin\UserController::updateRole/$1');

    // Event Industry Blacklist Management
    $routes->get('blacklist', 'Admin\BlacklistController::index');
    $routes->post('blacklist/save', 'Admin\BlacklistController::save');
    $routes->post('blacklist/delete/(:num)', 'Admin\BlacklistController::delete/$1');
    $routes->post('blacklist/category/save', 'Admin\BlacklistController::saveCategory');
    $routes->post('blacklist/category/delete/(:num)', 'Admin\BlacklistController::deleteCategory/$1');
});
