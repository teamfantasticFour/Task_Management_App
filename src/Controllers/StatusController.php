<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class StatusController
{
    protected $view, $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    public function index(Request $request, Response $response): Response
    {
        $statuses = $this->db->select("tbl_statuses", "*");
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'statuses/index.twig', [
            'statuses' => $statuses,
            'flash' => $flash
        ]);
    }

 public function store(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    $statusName = trim($data['status'] ?? '');

    if (empty($statusName)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Nama status tidak boleh kosong.'];
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // Cek jika nama status sudah ada
    $existing = $this->db->has("tbl_statuses", ["name" => $statusName]);
    if ($existing) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Nama status sudah tersedia.'];
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    $this->db->insert("tbl_statuses", ["name" => $statusName]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status berhasil ditambahkan.'];
    return $response->withHeader('Location', '/dashboard')->withStatus(302);
}



    public function editForm(Request $request, Response $response, $args): Response
    {
        $status = $this->db->get("tbl_statuses", "*", ["id" => $args['id']]);

        if (!$status) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Status tidak ditemukan.'];
            return $response->withHeader('Location', '/statuses')->withStatus(302);
        }

        return $this->view->render($response, 'statuses/edit.twig', [
            'status' => $status
        ]);
    }

    public function update(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $statusName = trim($data['status'] ?? '');

        if (empty($statusName)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Nama status tidak boleh kosong.'];
            return $response->withHeader('Location', '/statuses')->withStatus(302);
        }

        $this->db->update("tbl_statuses", [
            "name" => $statusName
        ], ["id" => $args['id']]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status berhasil diperbarui.'];
        return $response->withHeader('Location', '/statuses')->withStatus(302);
    }

    public function delete(Request $request, Response $response, $args): Response
    {
        $this->db->delete("tbl_statuses", ["id" => $args['id']]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status berhasil dihapus.'];
        return $response->withHeader('Location', '/statuses')->withStatus(302);
    }
}
