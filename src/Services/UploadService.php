<?php

namespace App\Services;

use Exception;

class UploadService
{
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private int $maxFileSize = 5242880; 
    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/logements/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function uploadImage(array $file, int $logementId): string
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Aucun fichier n'a été téléchargé.");
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new Exception("Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP.");
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Le fichier est trop volumineux. Taille maximale: 5MB.");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logement_' . $logementId . '_' . uniqid() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Impossible de sauvegarder le fichier.");
        }

        return '/uploads/logements/' . $filename;
    }

    public function deleteImage(string $imagePath): bool
    {
        $fullPath = __DIR__ . '/../../public' . $imagePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function uploadMultipleImages(array $files, int $logementId): array
    {
        $uploadedPaths = [];
        
        if (!isset($files['tmp_name']) || !is_array($files['tmp_name'])) {
            throw new Exception("Aucun fichier n'a été téléchargé.");
        }

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if (!empty($tmpName) && is_uploaded_file($tmpName)) {
                $file = [
                    'tmp_name' => $tmpName,
                    'name' => $files['name'][$key],
                    'size' => $files['size'][$key],
                    'error' => $files['error'][$key]
                ];
                
                try {
                    $path = $this->uploadImage($file, $logementId);
                    $uploadedPaths[] = $path;
                } catch (Exception $e) {
                    error_log("Error uploading image: " . $e->getMessage());
                }
            }
        }

        return $uploadedPaths;
    }
}
