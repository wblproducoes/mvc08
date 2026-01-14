<?php

namespace App\Helpers;

/**
 * Helper de validação de dados
 * 
 * @package App\Helpers
 */
class Validator
{
    private array $errors = [];
    
    /**
     * Valida email
     * 
     * @param string $email
     * @param string $field
     * @return self
     */
    public function email(string $email, string $field = 'email'): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Email inválido";
        }
        return $this;
    }
    
    /**
     * Valida campo obrigatório
     * 
     * @param mixed $value
     * @param string $field
     * @return self
     */
    public function required($value, string $field): self
    {
        if (empty($value)) {
            $this->errors[$field] = "Campo obrigatório";
        }
        return $this;
    }
    
    /**
     * Valida tamanho mínimo
     * 
     * @param string $value
     * @param int $min
     * @param string $field
     * @return self
     */
    public function min(string $value, int $min, string $field): self
    {
        if (strlen($value) < $min) {
            $this->errors[$field] = "Mínimo de {$min} caracteres";
        }
        return $this;
    }
    
    /**
     * Valida tamanho máximo
     * 
     * @param string $value
     * @param int $max
     * @param string $field
     * @return self
     */
    public function max(string $value, int $max, string $field): self
    {
        if (strlen($value) > $max) {
            $this->errors[$field] = "Máximo de {$max} caracteres";
        }
        return $this;
    }
    
    /**
     * Verifica se há erros
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Retorna erros
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
