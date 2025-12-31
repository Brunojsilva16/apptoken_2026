<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

class PatientModel
{
    // ... métodos search, getAll, find mantidos ...
 public function search($term)
    {
        $pdo = Connection::getInstance();
        
        $cleanTerm = preg_replace('/[^a-zA-Z0-9 ]/', '', $term);
        
        // CORREÇÃO: Adicionados os campos telefone, nome_responsavel, responsavel_financeiro
        $sql = "SELECT id_paciente, nome, cpf, email, telefone, nome_responsavel, responsavel_financeiro 
                FROM pacientes 
                WHERE nome LIKE :nome OR cpf LIKE :cpf 
                LIMIT 20";

        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':nome', '%' . $term . '%');
        $stmt->bindValue(':cpf', '%' . $term . '%');
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query("SELECT * FROM pacientes ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id_paciente = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $pdo = Connection::getInstance();
        
        $sql = "UPDATE pacientes SET 
                nome = :nome, 
                cpf = :cpf, 
                email = :email, 
                telefone = :telefone,
                data_nascimento = :data_nascimento,
                genero = :genero,
                origem = :origem,
                tags = :tags,
                nome_responsavel = :nome_responsavel,
                responsavel_financeiro = :responsavel_financeiro,
                cep = :cep,
                logradouro = :logradouro,
                numero = :numero,
                complemento = :complemento,
                bairro = :bairro,
                cidade = :cidade,
                estado = :estado
                WHERE id_paciente = :id";
        
        $stmt = $pdo->prepare($sql);
        
        $this->bindCommonParams($stmt, $data); // Usa helper para binds
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }

    public function create($data)
    {
        $pdo = Connection::getInstance();
        
        $sql = "INSERT INTO pacientes (
                    nome, cpf, email, telefone, data_nascimento, genero, origem, tags,
                    nome_responsavel, responsavel_financeiro,
                    cep, logradouro, numero, complemento, bairro, cidade, estado
                ) VALUES (
                    :nome, :cpf, :email, :telefone, :data_nascimento, :genero, :origem, :tags,
                    :nome_responsavel, :responsavel_financeiro,
                    :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado
                )";
        
        $stmt = $pdo->prepare($sql);
        $this->bindCommonParams($stmt, $data);
        
        return $stmt->execute();
    }

    // Helper para evitar repetição de código
    private function bindCommonParams($stmt, $data)
    {
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':cpf', $data['cpf'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':telefone', $data['telefone'] ?? null);
        
        $dtNasc = (isset($data['data_nascimento']) && $data['data_nascimento'] !== '') ? $data['data_nascimento'] : null;
        $stmt->bindValue(':data_nascimento', $dtNasc);
        
        $stmt->bindValue(':genero', $data['genero'] ?? null);
        $stmt->bindValue(':origem', $data['origem'] ?? null);
        $stmt->bindValue(':tags', $data['tags'] ?? null); // Bind Tags
        $stmt->bindValue(':nome_responsavel', $data['nome_responsavel'] ?? null);
        $stmt->bindValue(':responsavel_financeiro', $data['responsavel_financeiro'] ?? null);
        
        $stmt->bindValue(':cep', $data['cep'] ?? null);
        $stmt->bindValue(':logradouro', $data['logradouro'] ?? null);
        $stmt->bindValue(':numero', $data['numero'] ?? null);
        $stmt->bindValue(':complemento', $data['complemento'] ?? null);
        $stmt->bindValue(':bairro', $data['bairro'] ?? null);
        $stmt->bindValue(':cidade', $data['cidade'] ?? null);
        $stmt->bindValue(':estado', $data['estado'] ?? null);
    }
}