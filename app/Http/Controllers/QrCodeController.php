<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    private function generateQrCode($tableID, $size = 200)
    {
        return QrCode::size($size)
            ->format('png')
            ->merge('/public/img/logo.png', 0.5)
            ->errorCorrection('Q')
            ->generate('http://127.0.0.1:8000/menu?table=' . $tableID);
    }

    public function show($tableID)
    {
        return $this->generateQrCode($tableID);
    }

    public function download(Request $request)
    {
        $tableID = $request->table;
        $qrCode = $this->generateQrCode($tableID, 512);

        return response()->streamDownload(
            function () use ($qrCode) {
                echo $qrCode;
            },
            'qr-code-table-' . $tableID . '.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }

}
