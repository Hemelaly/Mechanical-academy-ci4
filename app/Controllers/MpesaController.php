<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use emagombe\Mpesa;

class MpesaController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function send()
    {
        helper(['form']);

        // Validação simples
        $validation = \Config\Services::validation();
        $validation->setRules([
            'client_number' => 'required',
            'value' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Dados para transação
        $data = [
            "value" => $this->request->getPost('value'),
            "client_number" => $this->request->getPost('client_number'),
            "agent_id" => 171717,
            "third_party_reference" => rand(10000, 99999),
            "transaction_reference" => time(),
        ];

        // Inicializa a biblioteca M-Pesa (correto agora)
        $apiKey = getenv('MPESA_API_KEY');
        $publicKey = getenv('MPESA_PUBLIC_KEY');
        $mpesa = Mpesa::init($apiKey, $publicKey, 'development');

        try {
            $response = $mpesa->c2b($data);
            return view('welcome_message', ['response' => $response]);
        } catch (\Exception $e) {
            return view('welcome_message', ['error' => $e->getMessage()]);
        }
    }
}
