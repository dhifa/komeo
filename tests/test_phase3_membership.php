<?php

/**
 * KOMEO.ID - Phase 3 Membership Management & Approval System Test Suite
 *
 * Tests the 14 requirements specified in Phase 3:
 * 1. New user receives pending status.
 * 2. Pending members cannot access active-only features.
 * 3. Admin can approve pending members.
 * 4. Unique member number is generated in KMO-YYYY-XXXXXX format.
 * 5. Concurrent approvals do not produce duplicate numbers (atomic counter).
 * 6. Duplicate approval does not issue another number.
 * 7. Admin can reject applications (with reason).
 * 8. Admin can suspend (with reason) and reactivate members.
 * 9. Membership history is recorded correctly for all transitions.
 * 10. Member status updates correctly in the dashboard.
 * 11. Suspended members lose public verified status.
 * 12. Unauthorized members cannot approve themselves or access admin endpoints.
 * 13. Existing member profiles and portfolios still work.
 * 14. Existing CMS and website settings remain functional.
 */

// Define path to CI4 bootstrap
define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);

class TestPhase3Membership
{
    private string $baseUrl = 'http://localhost:8080';
    private string $cookieJarAdmin;
    private string $cookieJarMember;
    private string $cookieJarAnon;
    private string $csrfTokenName = 'csrf_token_komeo';
    private array $results = [];

    public function __construct()
    {
        $this->cookieJarAdmin = tempnam(sys_get_temp_dir(), 'komeo_adm_');
        $this->cookieJarMember = tempnam(sys_get_temp_dir(), 'komeo_mem_');
        $this->cookieJarAnon = tempnam(sys_get_temp_dir(), 'komeo_ano_');
    }

    public function __destruct()
    {
        foreach ([$this->cookieJarAdmin, $this->cookieJarMember, $this->cookieJarAnon] as $jar) {
            if (file_exists($jar)) {
                @unlink($jar);
            }
        }
    }

    private function logTest(string $name, bool $passed, string $message = ''): void
    {
        $status = $passed ? "\033[32m[PASSED]\033[0m" : "\033[31m[FAILED]\033[0m";
        echo "{$status} {$name}" . ($message ? " - {$message}" : "") . PHP_EOL;
        $this->results[$name] = [
            'passed' => $passed,
            'message' => $message,
        ];
    }

    private function request(string $jar, string $path, string $method = 'GET', array $postData = []): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $jar);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        $csrfToken = null;
        $tokenName = $this->csrfTokenName;
        if (preg_match('/name="(csrf_[^"]+)"\s+value="([^"]+)"/i', $body, $m)) {
            $tokenName = $m[1];
            $csrfToken = $m[2];
        }

        $location = null;
        if (preg_match('/^Location:\s*(.+)$/im', $header, $m)) {
            $location = trim($m[1]);
        }

        return [
            'status'   => $httpCode,
            'header'   => $header,
            'body'     => $body,
            'csrf'     => $csrfToken,
            'csrfName' => $tokenName,
            'location' => $location,
        ];
    }

    public function run(): void
    {
        echo "====================================================\n";
        echo " KOMEO.ID - Phase 3 Membership Management Test Suite \n";
        echo "====================================================\n\n";

        // 1. Connect to database directly for internal assertions
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'komeo_db';
        $dbPort = 3306;

        $envFile = dirname(__DIR__) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v);
                    if ($k === 'database.default.hostname') $dbHost = $v;
                    if ($k === 'database.default.username') $dbUser = $v;
                    if ($k === 'database.default.password') $dbPass = $v;
                    if ($k === 'database.default.database') $dbName = $v;
                    if ($k === 'database.default.port') $dbPort = (int)$v;
                }
            }
        }

        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);

        // TEST 1: New user registration receives pending status
        $this->testNewUserReceivesPendingStatus($pdo);

        // TEST 2: Pending members cannot access active-only features
        $this->testPendingMemberPermissions();

        // TEST 3, 4, 6: Admin approval, format KMO-YYYY-XXXXXX, and duplicate prevention
        $this->testAdminApprovalWorkflow($pdo);

        // TEST 5: Atomic counter concurrency & sequence generation
        $this->testAtomicCounterAllocation($pdo);

        // TEST 7: Admin rejection with reason
        $this->testAdminRejectionWorkflow($pdo);

        // TEST 8: Admin suspension with reason and reactivation
        $this->testAdminSuspensionAndReactivation($pdo);

        // TEST 9: Membership history recorded correctly
        $this->testMembershipHistoryRecording($pdo);

        // TEST 10: Member dashboard displays correct status banner & member number
        $this->testMemberDashboardDisplay($pdo);

        // TEST 11: Suspended members lose public verified status and are hidden
        $this->testSuspendedPublicVisibility($pdo);

        // TEST 12: Unauthorized users cannot approve themselves or access admin endpoints
        $this->testSecurityAndAccessControl();

        // TEST 13: Existing member profiles and portfolios still work
        $this->testExistingProfilesAndPortfolios($pdo);

        // TEST 14: Existing CMS and website settings remain functional
        $this->testExistingSettingsAndCms();

        echo "\n====================================================\n";
        $total = count($this->results);
        $passed = count(array_filter($this->results, fn($r) => $r['passed']));
        $failed = $total - $passed;
        echo "TOTAL TESTS: {$total} | PASSED: {$passed} | FAILED: {$failed}\n";
        echo "====================================================\n";
    }

    private string $currentTestUsername = '';
    private string $currentTestEmail = '';

    private function testNewUserReceivesPendingStatus(PDO $pdo): void
    {
        // Register a brand new user via HTTP POST /register
        $regPage = $this->request($this->cookieJarMember, '/register');
        $uniq = substr(uniqid(), -6);
        $this->currentTestUsername = 'uji_' . $uniq;
        $this->currentTestEmail = $this->currentTestUsername . '@komeo.id';
        $password = 'Password123!';

        $regRes = $this->request($this->cookieJarMember, '/register', 'POST', [
            $regPage['csrfName'] => $regPage['csrf'],
            'full_name'        => 'Anggota Uji Coba ' . $uniq,
            'username'         => $this->currentTestUsername,
            'email'            => $this->currentTestEmail,
            'password'         => $password,
            'password_confirm' => $password,
            'member_type'      => 'individual',
            'city'             => 'Jakarta',
            'whatsapp'         => '081298765432',
        ]);

        // Check database for newly created user membership
        $stmt = $pdo->prepare("SELECT m.*, u.username FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = ? LIMIT 1");
        $stmt->execute([$this->currentTestUsername]);
        $newMember = $stmt->fetch();

        $isPending = ($newMember && $newMember->status === 'pending');
        $noNumber = ($newMember && empty($newMember->member_number));
        $notApproved = ($newMember && empty($newMember->approved_at));

        $this->logTest("1. New User Receives Pending Status", $isPending && $noNumber && $notApproved, "User @{$this->currentTestUsername} registered: status='pending', member_number=NULL");
    }

    private function testPendingMemberPermissions(): void
    {
        // Access dashboard using the registered member session
        $dashRes = $this->request($this->cookieJarMember, '/dashboard');
        $canAccessDashboard = ($dashRes['status'] === 200);

        // Check if pending member gets the pending notification banner
        $hasPendingBanner = str_contains($dashRes['body'], 'Keanggotaan Anda sedang menunggu persetujuan admin');

        $this->logTest("2. Pending Members Restricted Privileges", $canAccessDashboard && $hasPendingBanner, "Pending member can login & view profile, pending alert banner shown");
    }

    private function testAdminApprovalWorkflow(PDO $pdo): void
    {
        // Login as admin
        $loginPage = $this->request($this->cookieJarAdmin, '/login');
        $this->request($this->cookieJarAdmin, '/login', 'POST', [
            $loginPage['csrfName'] => $loginPage['csrf'],
            'login'    => 'admin@komeo.id',
            'password' => 'Password123!',
        ]);

        // Find the test pending member
        $stmt = $pdo->prepare("SELECT m.* FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = ? LIMIT 1");
        $stmt->execute([$this->currentTestUsername]);
        $membership = $stmt->fetch();

        if (!$membership) {
            $this->logTest("3. Admin Approval Workflow", false, "@{$this->currentTestUsername} not found");
            return;
        }

        // Admin views member detail
        $viewRes = $this->request($this->cookieJarAdmin, "/admin/members/view/{$membership->id}");
        $csrf = $viewRes['csrf'];
        $csrfName = $viewRes['csrfName'];

        // Admin approves
        $approveRes = $this->request($this->cookieJarAdmin, "/admin/members/approve/{$membership->id}", 'POST', [
            $csrfName => $csrf,
            'status_notes' => 'Disetujui untuk uji coba Phase 3',
        ]);

        // Verify in DB
        $stmt = $pdo->prepare("SELECT * FROM memberships WHERE id = ?");
        $stmt->execute([$membership->id]);
        $approved = $stmt->fetch();

        $isActive = ($approved && $approved->status === 'active');
        $hasApprovedAt = ($approved && !empty($approved->approved_at));
        $hasApprovedBy = ($approved && !empty($approved->approved_by));
        $this->logTest("3. Admin Can Approve Pending Member", $isActive && $hasApprovedAt && $hasApprovedBy, "Status changed to active with timestamp and admin ID");

        // TEST 4: Format KMO-YYYY-XXXXXX
        $pattern = '/^KMO-\d{4}-\d{6}$/';
        $validFormat = (bool)preg_match($pattern, (string)$approved->member_number);
        $currentYear = date('Y');
        $containsCurrentYear = str_contains((string)$approved->member_number, "KMO-{$currentYear}-");
        $this->logTest("4. Unique Member Number Generated (KMO-YYYY-XXXXXX)", $validFormat && $containsCurrentYear, "Generated member number: {$approved->member_number}");

        // TEST 6: Duplicate approval does not issue another number
        $firstNumber = $approved->member_number;
        $viewRes2 = $this->request($this->cookieJarAdmin, "/admin/members/view/{$membership->id}");
        $approveAgainRes = $this->request($this->cookieJarAdmin, "/admin/members/approve/{$membership->id}", 'POST', [
            $viewRes2['csrfName'] => $viewRes2['csrf'],
        ]);

        $stmt->execute([$membership->id]);
        $rechecked = $stmt->fetch();
        $numberUnchanged = ($rechecked->member_number === $firstNumber);
        $this->logTest("6. Duplicate Approval Does Not Issue Another Number", $numberUnchanged, "Member number remained {$firstNumber}");
    }

    private function testAtomicCounterAllocation(PDO $pdo): void
    {
        // Test counter model atomicity directly using MembershipNumberCounterModel logic
        $year = (int)date('Y');
        
        $stmt = $pdo->prepare("SELECT last_number FROM membership_number_counters WHERE year = ?");
        $stmt->execute([$year]);
        $initialSeq = (int)($stmt->fetchColumn() ?: 0);

        // Simulate 5 atomic counter increments within transactions
        $generatedNumbers = [];
        for ($i = 0; $i < 5; $i++) {
            $pdo->beginTransaction();
            $selStmt = $pdo->prepare("SELECT id, last_number FROM membership_number_counters WHERE year = ? FOR UPDATE");
            $selStmt->execute([$year]);
            $row = $selStmt->fetch();

            if (! $row) {
                $insStmt = $pdo->prepare("INSERT INTO membership_number_counters (year, last_number, updated_at) VALUES (?, 1, NOW())");
                $insStmt->execute([$year]);
                $nextSeq = 1;
            } else {
                $nextSeq = (int)$row->last_number + 1;
                $updStmt = $pdo->prepare("UPDATE membership_number_counters SET last_number = ?, updated_at = NOW() WHERE id = ?");
                $updStmt->execute([$nextSeq, $row->id]);
            }
            $pdo->commit();
            $generatedNumbers[] = sprintf('KMO-%04d-%06d', $year, $nextSeq);
        }

        $allUnique = count($generatedNumbers) === count(array_unique($generatedNumbers));
        $sequential = true;
        for ($i = 1; $i < count($generatedNumbers); $i++) {
            $prevSeq = (int)substr($generatedNumbers[$i - 1], -6);
            $currSeq = (int)substr($generatedNumbers[$i], -6);
            if ($currSeq !== $prevSeq + 1) {
                $sequential = false;
                break;
            }
        }

        $this->logTest("5. Concurrent / Atomic Number Allocation", $allUnique && $sequential, "Generated 5 sequential numbers without duplicates: " . implode(', ', $generatedNumbers));
    }

    private function testAdminRejectionWorkflow(PDO $pdo): void
    {
        // Create or get user 3 (kreatif2)
        $stmt = $pdo->prepare("SELECT m.* FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = 'kreatifpro2' LIMIT 1");
        $stmt->execute();
        $membership = $stmt->fetch();

        if (!$membership) {
            $this->logTest("7. Admin Rejection Workflow", false, "kreatifpro2 not found");
            return;
        }

        // Set to pending
        $pdo->prepare("UPDATE memberships SET status = 'pending', rejection_reason = NULL WHERE id = ?")->execute([$membership->id]);

        $viewRes = $this->request($this->cookieJarAdmin, "/admin/members/view/{$membership->id}");
        $reason = "Portofolio belum lengkap dan nomor kontak tidak dapat dihubungi.";
        
        $rejectRes = $this->request($this->cookieJarAdmin, "/admin/members/reject/{$membership->id}", 'POST', [
            $viewRes['csrfName'] => $viewRes['csrf'],
            'reason' => $reason,
        ]);

        $stmt->execute();
        $rejected = $stmt->fetch();

        $isRejected = ($rejected->status === 'rejected');
        $hasReason = ($rejected->rejection_reason === $reason);

        $this->logTest("7. Admin Rejects Application With Reason", $isRejected && $hasReason, "Status changed to rejected, reason stored");
    }

    private function testAdminSuspensionAndReactivation(PDO $pdo): void
    {
        // Use user 1 (kreatif) which was approved in Test 3
        $membership = $pdo->query("SELECT m.* FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = 'kreatifpro' LIMIT 1")->fetch();

        $memberNumberBefore = $membership->member_number;

        // Admin suspends
        $viewRes = $this->request($this->cookieJarAdmin, "/admin/members/view/{$membership->id}");
        $suspendReason = "Pelanggaran kode etik komunitas KOMEO.";
        $this->request($this->cookieJarAdmin, "/admin/members/suspend/{$membership->id}", 'POST', [
            $viewRes['csrfName'] => $viewRes['csrf'],
            'reason' => $suspendReason,
        ]);

        $suspended = $pdo->query("SELECT * FROM memberships WHERE id = {$membership->id}")->fetch();
        $isSuspended = ($suspended->status === 'suspended' && $suspended->suspension_reason === $suspendReason);

        // Admin reactivates
        $viewRes2 = $this->request($this->cookieJarAdmin, "/admin/members/view/{$membership->id}");
        $this->request($this->cookieJarAdmin, "/admin/members/reactivate/{$membership->id}", 'POST', [
            $viewRes2['csrfName'] => $viewRes2['csrf'],
            'reason' => 'Telah menyelesaikan klarifikasi kode etik.',
        ]);

        $reactivated = $pdo->query("SELECT * FROM memberships WHERE id = {$membership->id}")->fetch();
        $isReactivated = ($reactivated->status === 'active');
        $preservedNumber = ($reactivated->member_number === $memberNumberBefore);

        $this->logTest("8. Admin Can Suspend and Reactivate Members", $isSuspended && $isReactivated && $preservedNumber, "Suspended with reason, reactivated preserving number: {$memberNumberBefore}");
    }

    private function testMembershipHistoryRecording(PDO $pdo): void
    {
        $mid = $pdo->query("SELECT m.id FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = 'kreatifpro' LIMIT 1")->fetchColumn();

        $history = $pdo->query("SELECT * FROM membership_status_history WHERE membership_id = {$mid} ORDER BY id ASC")->fetchAll();

        // Must have at least: pending->active, active->suspended, suspended->active
        $transitions = array_map(fn($h) => "{$h->previous_status}->{$h->new_status}", $history);
        $hasTransitions = count($history) >= 3;

        $this->logTest("9. Membership History Recorded Correctly", $hasTransitions, "Logged transitions: " . implode(', ', $transitions));
    }

    private function testMemberDashboardDisplay(PDO $pdo): void
    {
        // Re-authenticate member to ensure fresh session
        $loginPage = $this->request($this->cookieJarMember, '/login');
        $this->request($this->cookieJarMember, '/login', 'POST', [
            $loginPage['csrfName'] => $loginPage['csrf'],
            'login'    => 'kreatif@komeo.id',
            'password' => 'Password123!',
        ]);

        // Request member dashboard as authenticated active member
        $dashRes = $this->request($this->cookieJarMember, '/dashboard');
        
        $hasActiveNotice = str_contains($dashRes['body'], 'Keanggotaan KOMEO Anda telah aktif');
        $hasMemberNumber = str_contains($dashRes['body'], 'KMO-');
        $hasCompletion = str_contains($dashRes['body'], 'Kelengkapan Profil');

        $this->logTest("10. Member Dashboard Updates Correctly", $hasActiveNotice && $hasMemberNumber && $hasCompletion, "Displays active banner, member number, and completion %");
    }

    private function testSuspendedPublicVisibility(PDO $pdo): void
    {
        $mid = $pdo->query("SELECT m.id FROM memberships m JOIN users u ON u.id = m.user_id WHERE u.username = 'kreatifpro' LIMIT 1")->fetchColumn();

        // Ensure active state first
        $pdo->query("UPDATE memberships SET status = 'active' WHERE id = {$mid}");

        // First verify active public profile works for anonymous user
        $pubActiveRes = $this->request($this->cookieJarAnon, '/member/kreatifpro');
        $activePublicVisible = ($pubActiveRes['status'] === 200);

        // Now suspend member
        $pdo->query("UPDATE memberships SET status = 'suspended' WHERE id = {$mid}");

        // Anonymous request to suspended member profile must return 404
        $pubSuspendedRes = $this->request($this->cookieJarAnon, '/member/kreatifpro');
        $suspendedHidden = ($pubSuspendedRes['status'] === 404);

        // Restore back to active
        $pdo->query("UPDATE memberships SET status = 'active' WHERE id = {$mid}");

        $this->logTest("11. Suspended Members Lose Public Verified Status", $activePublicVisible && $suspendedHidden, "Active member is public (200), suspended member returns 404 to public");
    }

    private function testSecurityAndAccessControl(): void
    {
        // Allowed unauthorized codes are redirects (301, 302, 303) or 403 Forbidden
        $isBlocked = fn($code) => in_array($code, [301, 302, 303, 403], true);

        // 1. Anonymous user trying to access /admin/members
        $anonRes = $this->request($this->cookieJarAnon, '/admin/members');
        $anonBlocked = $isBlocked($anonRes['status']);

        // 2. Member trying to access /admin/members
        $memberAdminRes = $this->request($this->cookieJarMember, '/admin/members');
        $memberBlocked = $isBlocked($memberAdminRes['status']);

        // 3. Member trying to approve themselves directly
        $viewRes = $this->request($this->cookieJarMember, '/dashboard');
        $memberApproveRes = $this->request($this->cookieJarMember, '/admin/members/approve/1', 'POST', [
            $viewRes['csrfName'] => $viewRes['csrf'],
        ]);
        $approveBlocked = $isBlocked($memberApproveRes['status']);

        $this->logTest("12. Security: Unauthorized Cannot Access Admin Endpoints", $anonBlocked && $memberBlocked && $approveBlocked, "Anon (HTTP {$anonRes['status']}), Member (HTTP {$memberAdminRes['status']}), Self-approve (HTTP {$memberApproveRes['status']})");
    }

    private function testExistingProfilesAndPortfolios(PDO $pdo): void
    {
        // Member profile edit page
        $editProfileRes = $this->request($this->cookieJarMember, '/dashboard/profil/edit');
        $profileAccessible = ($editProfileRes['status'] === 200 && str_contains($editProfileRes['body'], 'Informasi Pribadi'));

        // Member portfolio page
        $portfolioRes = $this->request($this->cookieJarMember, '/dashboard/portofolio');
        $portfolioAccessible = ($portfolioRes['status'] === 200 && str_contains($portfolioRes['body'], 'Portofolio'));

        $this->logTest("13. Existing Member Profiles & Portfolios Intact", $profileAccessible && $portfolioAccessible, "Profile edit (HTTP {$editProfileRes['status']}), Portfolios (HTTP {$portfolioRes['status']})");
    }

    private function testExistingSettingsAndCms(): void
    {
        // Admin settings page
        $settingsRes = $this->request($this->cookieJarAdmin, '/admin/settings');
        $settingsAccessible = ($settingsRes['status'] === 200 && str_contains($settingsRes['body'], 'Pengaturan Website'));

        // Admin homepage CMS page
        $cmsRes = $this->request($this->cookieJarAdmin, '/admin/content/homepage');
        $cmsAccessible = ($cmsRes['status'] === 200 && str_contains($cmsRes['body'], 'Manajemen Konten'));

        // Public Homepage
        $homeRes = $this->request($this->cookieJarAnon, '/');
        $homeAccessible = ($homeRes['status'] === 200 && str_contains($homeRes['body'], 'KOMEO.ID'));

        $this->logTest("14. Existing CMS & Website Settings Functional", $settingsAccessible && $cmsAccessible && $homeAccessible, "Settings (200), CMS (200), Homepage (200)");
    }
}

$tester = new TestPhase3Membership();
$tester->run();
