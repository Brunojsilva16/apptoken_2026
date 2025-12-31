<?php
// app/Models/TokenModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;
use Exception;

class TokenModel
{
    protected $conn;
    private $table = 'tokens';
    private $colunaData = 'data_cadastro';

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    /**
     * Conta o total de registros para paginação (Contexto Geral)
     */
    public function countAll($search = '')
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof 
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE (t.paciente LIKE :search OR t.token LIKE :search OR t.cpf LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Conta o total de registros por usuário (Contexto Meus Tokens)
     */
    public function countByUserId($userId, $search = '')
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} t WHERE t.id_user = :id_user";
        $params = [':id_user' => $userId];

        if (!empty($search)) {
            $sql .= " AND (t.paciente LIKE :search OR t.token LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getAllWithDetails($limit = 25, $search = '', $offset = 0)
    {
        $sql = "SELECT t.id_token, t.token, t.paciente, t.cpf, t.statuspag, t.valor, t.formapag, t.nome_banco, t.vencimento, t.origem, t.nome_resp, t.{$this->colunaData} as data_registro, p.nome as nome_profissional, u.nome as nome_usuario 
                FROM {$this->table} t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof 
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE (t.paciente LIKE :search OR t.token LIKE :search OR t.cpf LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        // Adicionado OFFSET
        $sql .= " ORDER BY t.{$this->colunaData} DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', $params[':search']);
        }
        
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT); // Bind do Offset
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($userId, $limit = 25, $search = '', $offset = 0)
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, t.vencimento, t.valor, t.origem, t.nome_resp, t.{$this->colunaData} as data_registro, p.nome as nome_profissional 
                FROM {$this->table} t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof 
                WHERE t.id_user = :id_user";
        
        $params = [':id_user' => $userId];
        
        if (!empty($search)) {
            $sql .= " AND (t.paciente LIKE :search OR t.token LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        // Adicionado OFFSET
        $sql .= " ORDER BY t.{$this->colunaData} DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', $params[':search']);
        }
        
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT); // Bind do Offset
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ... (Métodos create, getReportData, getById, getSessoes, etc. mantidos iguais) ...
    
    public function getReportData($startDate, $endDate, $profId = null, $filters = [])
    {
        $start = $startDate . ' 00:00:00';
        $end = $endDate . ' 23:59:59';

        $sql = "SELECT t.id_token, 
                       t.token, 
                       t.paciente, 
                       t.responsavel_f,
                       t.modalidadep,
                       t.origem,
                       t.vencimento,
                       t.nome_banco,
                       t.valor, 
                       t.formapag, 
                       t.statuspag,
                       t.{$this->colunaData} as data_registro,
                       p.nome as nome_profissional, 
                       p.porcento
                FROM {$this->table} t
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.{$this->colunaData} BETWEEN :start AND :end";

        $params = [
            ':start' => $start,
            ':end' => $end
        ];

        if (!empty($profId)) {
            $sql .= " AND t.id_prof = :profId";
            $params[':profId'] = $profId;
        }

        if (!empty($filters['responsavel_f'])) {
            $sql .= " AND t.responsavel_f LIKE :resp";
            $params[':resp'] = "%" . $filters['responsavel_f'] . "%";
        }

        if (!empty($filters['formapag'])) {
            $sql .= " AND t.formapag = :formapag";
            $params[':formapag'] = $filters['formapag'];
        }

        if (!empty($filters['nome_banco'])) {
            $sql .= " AND t.nome_banco = :banco";
            $params[':banco'] = $filters['nome_banco'];
        }

        if (!empty($filters['origem'])) {
            $sql .= " AND t.origem = :origem";
            $params[':origem'] = $filters['origem'];
        }

        $sql .= " ORDER BY t.{$this->colunaData} ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();
            $sql = "INSERT INTO {$this->table} (token, id_user, id_prof, paciente, cpf, telefone, nome_resp, responsavel_f, nome_banco, origem, valor, formapag, modalidadep, vencimento, statuspag, {$this->colunaData}) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'efetuado', NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$data['token'], $data['id_user'], $data['id_prof'], $data['paciente'], $data['cpf'] ?? null, $data['telefone'] ?? null, $data['nome_resp'] ?? null, $data['responsavel_f'] ?? null, $data['nome_banco'] ?? null, $data['origem'] ?? null, $data['valor'] ?? 0, $data['formapag'] ?? null, $data['modalidadep'] ?? null, $data['vencimento'] ?? date('Y-m-d')]);
            $idToken = $this->conn->lastInsertId();
            if (!empty($data['sessoes']) && is_array($data['sessoes'])) {
                $sqlSessao = "INSERT INTO sessoes (id_token, data_sessao) VALUES (?, ?)";
                $stmtSessao = $this->conn->prepare($sqlSessao);
                foreach ($data['sessoes'] as $dataSessao) {
                    if (!empty($dataSessao)) $stmtSessao->execute([$idToken, $dataSessao]);
                }
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function getById($id)
    {
        $sql = "SELECT t.*, p.nome as nome_profissional, u.nome as nome_usuario FROM {$this->table} t LEFT JOIN profissionais p ON t.id_prof = p.id_prof LEFT JOIN usuarios_a u ON t.id_user = u.id_user WHERE t.id_token = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getSessoes($idToken)
    {
        $sql = "SELECT data_sessao FROM sessoes WHERE id_token = :id ORDER BY data_sessao ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idToken]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function generateUniqueCode()
    {
        $limit = 0;
        do {
            $part1 = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $part2 = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = $part1 . $part2;
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE token = :token");
            $stmt->execute([':token' => $code]);
            $exists = $stmt->fetchColumn() > 0;
            $limit++;
        } while ($exists && $limit < 10);
        return $code;
    }

    public function update($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET id_prof = ?, paciente = ?, cpf = ?, nome_resp = ?, responsavel_f = ?, nome_banco = ?, valor = ?, formapag = ?, modalidadep = ?, vencimento = ?, origem = ? WHERE id_token = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$data['id_prof'], $data['paciente'], $data['cpf'] ?? null, $data['nome_resp'] ?? null, $data['responsavel_f'] ?? null, $data['nome_banco'] ?? null, $data['valor'] ?? 0, $data['formapag'] ?? null, $data['modalidadep'] ?? null, $data['vencimento'] ?? null, $data['origem'] ?? null, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}