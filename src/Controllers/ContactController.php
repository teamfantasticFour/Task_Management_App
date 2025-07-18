<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class ContactController
{
    public function submit(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = (array)$request->getParsedBody();
        $nama = $data['nama'] ?? '';
        $email = $data['email'] ?? '';
        $pesan = $data['pesan'] ?? '';

        // Simpan ke database jika ingin, misalnya menggunakan Medoo
        // $database->insert('kontak', [...])

        // Flash message
        $routeContext = RouteContext::fromRequest($request);
        $routeContext->getRouteParser()->urlFor('home');

        $flash = $request->getAttribute('flash');
        $flash->addMessage('success', 'Pesan berhasil dikirim!');

        // Redirect kembali ke halaman utama dengan anchor #contact
        return $response
            ->withHeader('Location', '/#contact')
            ->withStatus(302);
    }
}
