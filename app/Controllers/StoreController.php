<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\StoreSettings;
use App\Services\TemplateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StoreController
{
    protected $storeModel;
    protected $templateService;

    public function __construct(StoreSettings $storeModel, TemplateService $templateService)
    {
        $this->storeModel = $storeModel;
        $this->templateService = $templateService;
    }

    /**
     * Página de configurações da loja
     */
    public function settings(Request $request, Response $response, array $args): Response
    {
        $settings = $this->storeModel->getSettings();
        
        $data = [
            'pageTitle' => 'Configurações da Loja',
            'settings' => $settings
        ];

        $html = $this->templateService->render('admin.store.settings', $data);
        $response->getBody()->write($html);
        return $response;
    }

    /**
     * Atualizar configurações da loja
     */
    public function updateSettings(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        
        try {
            // Processar upload de logo se houver
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['store_logo']) && $uploadedFiles['store_logo']->getError() === UPLOAD_ERR_OK) {
                $logoFile = $uploadedFiles['store_logo'];
                
                // Converter UploadedFile para array para compatibilidade
                $fileArray = [
                    'name' => $logoFile->getClientFilename(),
                    'type' => $logoFile->getClientMediaType(),
                    'size' => $logoFile->getSize(),
                    'tmp_name' => $logoFile->getStream()->getMetadata('uri'),
                    'error' => $logoFile->getError()
                ];
                
                // Fazer upload manualmente
                $logoPath = $this->uploadLogo($fileArray);
                $data['store_logo'] = $logoPath;
            }
            
            // Converter valores numéricos
            if (isset($data['delivery_fee'])) {
                $data['delivery_fee'] = floatval(str_replace(',', '.', $data['delivery_fee']));
            }
            if (isset($data['loyalty_orders_required'])) {
                $data['loyalty_orders_required'] = intval($data['loyalty_orders_required']);
            }
            if (isset($data['loyalty_discount_percent'])) {
                $data['loyalty_discount_percent'] = floatval(str_replace(',', '.', $data['loyalty_discount_percent']));
            }
            
            $this->storeModel->updateSettings($data);
            
            // Redirecionar com sucesso
            $response = $response->withHeader('Location', '/admin/loja/configuracoes?success=1');
            return $response->withStatus(302);
            
        } catch (\Exception $e) {
            // Redirecionar com erro
            $errorMsg = urlencode($e->getMessage());
            $response = $response->withHeader('Location', "/admin/loja/configuracoes?error={$errorMsg}");
            return $response->withStatus(302);
        }
    }
    
    /**
     * Upload de logo (método auxiliar)
     */
    private function uploadLogo($fileArray)
    {
        // Verificar se é uma imagem
        $imageInfo = getimagesize($fileArray['tmp_name']);
        if (!$imageInfo) {
            throw new \Exception('O arquivo deve ser uma imagem válida');
        }
        
        // Tipos permitidos
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileArray['type'], $allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP');
        }
        
        // Tamanho máximo (2MB)
        if ($fileArray['size'] > 2 * 1024 * 1024) {
            throw new \Exception('O arquivo deve ter no máximo 2MB');
        }
        
        // Criar diretório se não existir
        $uploadDir = __DIR__ . '/../../public/uploads/store/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Nome único para o arquivo
        $extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        $fileName = 'logo_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        
        // Mover arquivo
        if (!move_uploaded_file($fileArray['tmp_name'], $filePath)) {
            throw new \Exception('Erro ao fazer upload do arquivo');
        }
        
        // Retornar caminho relativo
        return '/uploads/store/' . $fileName;
    }

    /**
     * API para verificar fidelidade do cliente
     */
    public function checkCustomerLoyalty(Request $request, Response $response, array $args): Response
    {
        $customerId = (int)$args['customerId'];
        
        try {
            $loyaltyInfo = $this->storeModel->checkCustomerLoyalty($customerId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'loyalty' => $loyaltyInfo
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}