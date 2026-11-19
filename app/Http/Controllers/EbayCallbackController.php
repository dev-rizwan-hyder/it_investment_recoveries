<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EbayCallbackController extends Controller
{
  
    public function handle(Request $request)
    {
        // Log all incoming parameters ($request->all())
        Log::info('eBay Callback Received:', $request->all());

        $requestData = $request->all();

    
        return response()->json([
            'status' => 'OK',
            'message' => 'Callback processed successfully',
            'received_data' => $requestData
        ]);
    }
}
