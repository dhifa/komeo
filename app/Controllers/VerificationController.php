<?php

namespace App\Controllers;

use App\Services\MembershipVerificationService;
use CodeIgniter\HTTP\ResponseInterface;

class VerificationController extends BaseController
{
    /**
     * Public QR Code Membership Verification
     */
    public function verify(string $token): string|ResponseInterface
    {
        // Security header: Do not allow search engines to index verification query results
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        // Simple IP rate-limiting protection
        $ip = $this->request->getIPAddress();
        $rateKey = 'verif_rate_' . md5($ip);
        $attempts = (int) cache($rateKey);
        if ($attempts > 60) { // max 60 verification scans per minute per IP
            return $this->response->setStatusCode(429)->setBody(view('errors/html/error_429', [
                'message' => 'Terlalu banyak permintaan verifikasi. Silakan coba lagi beberapa saat.',
            ]));
        }
        cache()->save($rateKey, $attempts + 1, 60);

        // Process verification
        $result = MembershipVerificationService::verify($token);

        return view('verification/index', [
            'title'  => 'Verifikasi Keanggotaan - KOMEO.ID',
            'token'  => $token,
            'result' => $result,
        ]);
    }
}
