<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class MemberController
{
    protected $view, $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    // Tampilkan semua member
    public function index(Request $request, ResponseInterface $response): ResponseInterface
    {
        $members = $this->db->select("tbl_members", [
            "[>]tbl_teams" => ["team_id" => "id"]
        ], [
            "tbl_members.id",
            "tbl_members.name",
            "tbl_members.photo",
            "tbl_members.role",
            "tbl_members.joined_at",
            "tbl_teams.name(team_name)"
        ]);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'members/members.twig', [
            'members' => $members,
            'session' => ['flash' => $flash]
        ]);
    }

    // Form tambah member
    public function createForm(Request $request, ResponseInterface $response): ResponseInterface
    {
        $teams = $this->db->select("tbl_teams", ["id", "name"]);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'members/create.twig', [
            'teams' => $teams,
            'session' => ['flash' => $flash]
        ]);
    }

    // Simpan member baru
    public function store(Request $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $photo = null;

        if (!empty($uploadedFiles['photo']) && $uploadedFiles['photo']->getError() === UPLOAD_ERR_OK) {
            $image = $uploadedFiles['photo'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $image->moveTo($uploadDir . $filename);
            $photo = $filename;
        }

        $this->db->insert("tbl_members", [
            "team_id"    => $data["team_id"],
            "name"       => $data["name"],
            "role"       => $data["role"],
            "joined_at"  => $data["joined_at"],
            "photo"      => $photo
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member berhasil ditambahkan'];
        return $response->withHeader('Location', '/members')->withStatus(302);
    }

    // Form edit member
    public function editForm(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $member = $this->db->get("tbl_members", "*", ["id" => $args['id']]);
        $teams = $this->db->select("tbl_teams", ["id", "name"]);

        if (!$member) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Member tidak ditemukan'];
            return $response->withHeader('Location', '/members')->withStatus(302);
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, 'members/edit.twig', [
            'member' => $member,
            'teams' => $teams,
            'session' => ['flash' => $flash]
        ]);
    }

    // Update member
    public function update(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        $updateData = [
            "team_id"   => $data["team_id"],
            "name"      => $data["name"],
            "role"      => $data["role"],
            "joined_at" => $data["joined_at"]
        ];

        if (!empty($uploadedFiles['photo']) && $uploadedFiles['photo']->getError() === UPLOAD_ERR_OK) {
            $oldPhoto = $this->db->get("tbl_members", "photo", ["id" => $args['id']]);
            if ($oldPhoto) {
                $oldPath = __DIR__ . '/../../public/uploads/' . $oldPhoto;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image = $uploadedFiles['photo'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $image->moveTo($uploadDir . $filename);
            $updateData['photo'] = $filename;
        }

        $this->db->update("tbl_members", $updateData, ["id" => $args['id']]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member berhasil diperbarui'];
        return $response->withHeader('Location', '/members')->withStatus(302);
    }

    // Hapus member
    public function delete(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $photo = $this->db->get("tbl_members", "photo", ["id" => $args['id']]);

        if ($photo) {
            $filepath = __DIR__ . '/../../public/uploads/' . $photo;
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $this->db->delete("tbl_members", ["id" => $args['id']]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member berhasil dihapus'];
        return $response->withHeader('Location', '/members')->withStatus(302);
    }
}
