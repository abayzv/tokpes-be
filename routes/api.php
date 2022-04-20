<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

// Category
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);


Route::get('/transaction/detail', function (Request $request) {
    $apiKey = 'DEV-ms8HV7Wru4B9TA2EHPcC9CjF1MMJ88tffWoAxBtg';

    $payload = ['reference'    => $request->ref];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/detail?' . http_build_query($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
        CURLOPT_FAILONERROR    => false,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    return empty($err) ? $response : $error;
});

Route::get('/transaction', function (Request $request) {

    $apiKey = 'DEV-ms8HV7Wru4B9TA2EHPcC9CjF1MMJ88tffWoAxBtg';

    $payload = [];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/merchant/transactions' . http_build_query($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
        CURLOPT_FAILONERROR    => false
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    return $response;
});

Route::get('/payment', function (Request $request) {

    $apiKey = 'DEV-ms8HV7Wru4B9TA2EHPcC9CjF1MMJ88tffWoAxBtg';

    $payload = [];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/merchant/payment-channel' . http_build_query($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
        CURLOPT_FAILONERROR    => false
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    return $response;
});

Route::post('checkout', function (Request $request) {
    // get time with format 'ydmhs'
    $time = date('ydmhs');

    $item = json_decode($request->item);
    $apiKey       = 'DEV-ms8HV7Wru4B9TA2EHPcC9CjF1MMJ88tffWoAxBtg';
    $privateKey   = 'AavSq-zhFa8-j2maK-PCi6u-NlNRR';
    $merchantCode = 'T3401';
    $merchantRef  = 'INV' . $time;
    $amount       = $request->amount;

    $data = [
        'method'         => $request->paymentCode,
        'merchant_ref'   => $merchantRef,
        'amount'         => $amount,
        'customer_name'  => $request->name,
        'customer_email' => 'ajipunk008@gmail.com',
        'customer_phone' => $request->phone,
        'order_items'    => $item,
        'return_url'   => 'https://domainanda.com/redirect',
        'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
        'signature'    => hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey)
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_FRESH_CONNECT  => true,
        CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
        CURLOPT_FAILONERROR    => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data)
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    return empty($error) ? $response : $error;
});

Route::post('/send-invoice', function (Request $request) {
    $data = [
        'api_key' => $request->api,
        'sender'  => '6285259622409',
        'number'  => $request->phone,
        'message' => $request->message
    ];

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "https://wa.mahesadev.com/app/api/send-message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data)
        )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
});
