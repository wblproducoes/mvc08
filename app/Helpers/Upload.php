<?php

namespace App\Helpers;

/**
 * Helper de Upload de Arquivos
 * 
 * Gerencia upload de arquivos com validação de tipo, tamanho e segurança.
 * Suporta imagens com redimensionamento automático.
 * 
 * @package App\Helpers
 * @author Sistema MVC08
 * @version 1.0.0
 * 
 * @example
 * ```php
 * $upload = new Upload();
 * $result = $upload->image($_FILES['photo'], 'users');
 * 
 * if ($result['success']) {
 *     $filename = $result['filename'];
 * }
 * ```
 */
class Upload
{
    /**
     * @var string Diretório base de uploads
     */
    private string $uploadDir;
    
    /**
     * @var int Tamanho máximo em bytes (5MB)
     */
    private int $maxSize = 5242880;
    
    /**
     * @var array Tipos MIME permitidos para imagens
     */
    private array $allowedImageTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    /**
     * @var array Extensões permitidas para imagens
     */
    private array $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
        
        // Cria diretório se não existir
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload de imagem
     * 
     * @param array $file Array $_FILES['campo']
     * @param string $folder Subpasta dentro de uploads/
     * @param int $maxWidth Largura máxima (0 = sem redimensionamento)
     * @param int $maxHeight Altura máxima (0 = sem redimensionamento)
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public function image(array $file, string $folder = '', int $maxWidth = 800, int $maxHeight = 800): array
    {
        // Validações básicas
        $validation = $this->validateUpload($file, $this->allowedImageTypes, $this->allowedImageExtensions);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Cria subpasta se necessário
        $targetDir = $this->uploadDir . ($folder ? $folder . '/' : '');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Gera nome único
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('img_', true) . '.' . $extension;
        $targetPath = $targetDir . $filename;
        
        // Move arquivo temporário
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'success' => false,
                'message' => 'Erro ao mover arquivo'
            ];
        }
        
        // Redimensiona se necessário
        if ($maxWidth > 0 || $maxHeight > 0) {
            $this->resizeImage($targetPath, $maxWidth, $maxHeight);
        }
        
        // Retorna caminho relativo
        $relativePath = ($folder ? $folder . '/' : '') . $filename;
        
        return [
            'success' => true,
            'filename' => $relativePath,
            'message' => 'Upload realizado com sucesso'
        ];
    }
    
    /**
     * Valida upload
     * 
     * @param array $file Array $_FILES['campo']
     * @param array $allowedTypes Tipos MIME permitidos
     * @param array $allowedExtensions Extensões permitidas
     * @return array
     */
    private function validateUpload(array $file, array $allowedTypes, array $allowedExtensions): array
    {
        // Verifica se houve erro no upload
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Erro no upload'];
        }
        
        // Verifica código de erro
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'message' => 'Arquivo muito grande'];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'message' => 'Nenhum arquivo enviado'];
            default:
                return ['success' => false, 'message' => 'Erro desconhecido no upload'];
        }
        
        // Verifica tamanho
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'message' => 'Arquivo muito grande (máx: 5MB)'];
        }
        
        // Verifica tipo MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
        }
        
        // Verifica extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Extensão de arquivo não permitida'];
        }
        
        return ['success' => true];
    }
    
    /**
     * Redimensiona imagem mantendo proporção
     * 
     * @param string $filepath Caminho do arquivo
     * @param int $maxWidth Largura máxima
     * @param int $maxHeight Altura máxima
     * @return bool
     */
    private function resizeImage(string $filepath, int $maxWidth, int $maxHeight): bool
    {
        // Obtém informações da imagem
        $imageInfo = getimagesize($filepath);
        if (!$imageInfo) {
            return false;
        }
        
        list($width, $height, $type) = $imageInfo;
        
        // Se já está dentro dos limites, não redimensiona
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }
        
        // Calcula novas dimensões mantendo proporção
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);
        
        // Cria imagem de origem
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filepath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filepath);
                break;
            default:
                return false;
        }
        
        // Cria imagem de destino
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserva transparência para PNG e GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Redimensiona
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Salva imagem redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filepath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filepath, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filepath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $filepath, 90);
                break;
        }
        
        // Libera memória
        imagedestroy($source);
        imagedestroy($destination);
        
        return true;
    }
    
    /**
     * Deleta arquivo
     * 
     * @param string $filename Nome do arquivo (caminho relativo)
     * @return bool
     */
    public function delete(string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        
        $filepath = $this->uploadDir . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    /**
     * Verifica se arquivo existe
     * 
     * @param string $filename Nome do arquivo (caminho relativo)
     * @return bool
     */
    public function exists(string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        
        return file_exists($this->uploadDir . $filename);
    }
}
