<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{
    public function card(Request $request): View
    {
        $anggota = $request->user()->anggota()->with('user')->first();
        $qrCode = null;
        $verificationUrl = null;

        if ($anggota?->status_verifikasi === 'disetujui' && $anggota->QRcode) {
            $baseUrl = $request->getSchemeAndHttpHost();
            $configuredUrl = trim((string) config('membership.verification_base_url'));
            $configuredHost = parse_url($configuredUrl, PHP_URL_HOST);
            if (filter_var($configuredUrl, FILTER_VALIDATE_URL)
                && in_array(parse_url($configuredUrl, PHP_URL_SCHEME), ['http', 'https'], true)
                && is_string($configuredHost)
                && !in_array(strtolower($configuredHost), ['localhost', '127.0.0.1', '::1'], true)
                && !parse_url($configuredUrl, PHP_URL_USER)
                && !parse_url($configuredUrl, PHP_URL_PASS)
                && !parse_url($configuredUrl, PHP_URL_QUERY)
                && !parse_url($configuredUrl, PHP_URL_FRAGMENT)
                && in_array(parse_url($configuredUrl, PHP_URL_PATH), [null, '', '/'], true)) {
                $baseUrl = rtrim($configuredUrl, '/');
            } elseif (in_array(strtolower($request->getHost()), ['localhost', '127.0.0.1', '::1'], true)) {
                $lanFile = config('membership.lan_url_file');
                $lanUrl = is_string($lanFile) && is_file($lanFile) ? trim((string) file_get_contents($lanFile)) : '';
                $lanHost = parse_url($lanUrl, PHP_URL_HOST);
                if (parse_url($lanUrl, PHP_URL_SCHEME) === 'http' && filter_var($lanHost, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $parts = array_map('intval', explode('.', $lanHost));
                    $private = $parts[0] === 10 || ($parts[0] === 172 && $parts[1] >= 16 && $parts[1] <= 31)
                        || ($parts[0] === 192 && $parts[1] === 168);
                    if ($private) {
                        $baseUrl = rtrim($lanUrl, '/');
                    }
                }
            }
            $verificationUrl = $baseUrl.route('anggota.verifikasi', ['token' => $anggota->QRcode], false);
            $qrCode = (string) QrCode::format('svg')->size(240)->margin(4)->errorCorrection('M')
                ->generate($verificationUrl);
        }

        return view('anggota.kartu', compact('anggota', 'qrCode', 'verificationUrl'));
    }

    public function verify(string $token): View
    {
        abort_unless(preg_match('/\A[A-Za-z0-9]{64}\z/', $token) === 1, 404);

        $anggota = Anggota::query()
            ->select(['id_anggota', 'id_user', 'nim', 'prodi', 'fakultas', 'tanggal_terbit', 'status_verifikasi', 'foto_stnghBadan'])
            ->with('user:id_user,nama')
            ->where('QRcode', $token)
            ->where('status_verifikasi', 'disetujui')
            ->firstOrFail();

        return view('anggota.verifikasi', compact('anggota'));
    }
}
