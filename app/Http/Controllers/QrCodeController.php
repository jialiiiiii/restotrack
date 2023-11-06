<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function show()
    {
        $data = QrCode::size(512)
            ->format('png')
            ->merge('/public/img/logo.png')
            ->errorCorrection('M')
            ->generate(
                'https://twitter.com/HarryKir',
            );

        return response($data)->header('Content-type', 'image/png');
    }

    public function download()
    {
        return response()->streamDownload(
            function () {
                echo QrCode::size(200)
                    ->format('png')
                    ->generate('https://harrk.dev');
            },
            'qr-code.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }
}
