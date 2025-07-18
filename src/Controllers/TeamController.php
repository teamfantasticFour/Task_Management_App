<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class TeamController
{
    protected $view, $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    public function index(Request $request, ResponseInterface $response): ResponseInterface
    {
        $teams = $this->db->select("tbl_teams", "*");
        return $this->view->render($response, 'teams/list.twig', ['teams' => $teams]);
    }

    public function createForm(Request $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'teams/create.twig');
    }

    public function store(Request $request, ResponseInterface $response): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();
        $photo = null;

        $uploadDir = __DIR__ . '/../../public/uploads/team/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $image = $uploadedFiles['image'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $image->moveTo($uploadDir . $filename);
            $photo = $filename;
        }

        $data = $request->getParsedBody();
        $this->db->insert("tbl_teams", [
            'name' => $data['name'],
            'description' => $data['description'],
            'photo' => $photo,
            'created_at' => date('Y-m-d')
        ]);

        return $response->withHeader('Location', '/teams')->withStatus(302);
    }

    public function editForm(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $team = $this->db->get("tbl_teams", "*", ["id" => $args['id']]);
        return $this->view->render($response, 'teams/edit.twig', ['team' => $team]);
    }

    public function update(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();
        $data = $request->getParsedBody();

        $updateData = [
            'name' => $data['name'],
            'description' => $data['description']
        ];

        $uploadDir = __DIR__ . '/../../public/uploads/team/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            // Hapus foto lama jika ada
            $old = $this->db->get("tbl_teams", "photo", ["id" => $args['id']]);
            if ($old && file_exists($uploadDir . $old)) {
                unlink($uploadDir . $old);
            }

            $image = $uploadedFiles['image'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $image->moveTo($uploadDir . $filename);
            $updateData['photo'] = $filename;
        }

        $this->db->update("tbl_teams", $updateData, ["id" => $args['id']]);
        return $response->withHeader('Location', '/teams')->withStatus(302);
    }

    public function delete(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Hapus foto tim jika ada
        $team = $this->db->get("tbl_teams", "*", ["id" => $args['id']]);
        if ($team && $team['photo']) {
            $photoPath = __DIR__ . '/../../public/uploads/team/' . $team['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        $this->db->delete("tbl_teams", ["id" => $args['id']]);
        return $response->withHeader('Location', '/teams')->withStatus(302);
    }

    public function members(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $teamId = $args['id'];

        $team = $this->db->get("tbl_teams", "*", ["id" => $teamId]);
        if (!$team) {
            return $response->withHeader('Location', '/teams')->withStatus(302);
        }

        $members = $this->db->select("tbl_members", [
            "[>]tbl_teams" => ["team_id" => "id"]
        ], [
            "tbl_members.id",
            "tbl_members.name",
            "tbl_members.photo",
            "tbl_members.role",
            "tbl_members.joined_at",
            "tbl_teams.name(team_name)"
        ], [
            "tbl_members.team_id" => $teamId
        ]);

        return $this->view->render($response, 'teams/memberTeam.twig', [
            'team' => $team,
            'members' => $members
        ]);
    }

    public function addMember(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $photo = null;

        if (empty($data["name"])) {
            return $response->withHeader('Location', "/teams/{$args['id']}/members")->withStatus(302);
        }

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
            "team_id" => $args['id'],
            "name" => $data["name"],
            "role" => $data["role"],
            "joined_at" => $data["joined_at"],
            "photo" => $photo
        ]);

        return $response->withHeader('Location', "/teams/{$args['id']}/members")->withStatus(302);
    }

    public function deleteMember(Request $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $member = $this->db->get("tbl_members", "*", ["id" => $args['member_id']]);
        if ($member && $member['photo']) {
            $path = __DIR__ . '/../../public/uploads/' . $member['photo'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->db->delete("tbl_members", ["id" => $args['member_id']]);
        return $response->withHeader('Location', "/teams/{$args['id']}/members")->withStatus(302);
    }
}
