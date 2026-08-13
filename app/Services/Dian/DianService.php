<?php

namespace App\Services\Dian;

use App\Models\DianEvent;
use App\Models\ElectronicInvoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Orquestador de la facturación electrónica DIAN para ElectronicInvoice.
 *
 *   build XML  →  firmar XAdES  →  guardar XML  →  enviar SOAP
 *              →  guardar ApplicationResponse  →  actualizar factura
 */
class DianService
{
    public function __construct(
        private ?DianConfig $config = null,
        private ?InvoiceXmlBuilder $xmlBuilder = null,
        private ?XadesSigner $signer = null,
        private ?DianSoapClient $soap = null,
        private ?CufeGenerator $cufe = null,
    ) {
        $this->config     = $this->config     ?? new DianConfig();
        $this->cufe       = $this->cufe       ?? new CufeGenerator($this->config);
        $this->xmlBuilder = $this->xmlBuilder ?? new InvoiceXmlBuilder($this->config, $this->cufe);
        $this->signer     = $this->signer     ?? new XadesSigner($this->config);
        $this->soap       = $this->soap       ?? new DianSoapClient($this->config);
    }

    public function sendInvoice(ElectronicInvoice $invoice): ElectronicInvoice
    {
        if (! config('dian.enabled')) {
            $invoice->update([
                'dian_status'      => 'ERROR',
                'dian_description' => 'DIAN deshabilitada (config dian.enabled / DIAN_ENABLED=false).',
            ]);

            return $invoice;
        }

        if (! $invoice->isElectronic()) {
            return $invoice;
        }

        if (! $this->config->isConfigured()) {
            $invoice->update([
                'dian_status'      => 'ERROR',
                'dian_description' => 'DIAN no configurada. Completa settings dian_* (empresa, software, certificado).',
            ]);

            return $invoice;
        }

        try {
            $xml = $this->xmlBuilder->build($invoice);
            $cufe = $this->cufe->forInvoice($invoice);

            $signed = $this->signer->sign($xml);

            $fileNameNoExt = $this->buildFileName($invoice);
            $xmlPath = 'dian/xml_firmado/'.$fileNameNoExt.'.xml';
            Storage::disk('local')->put($xmlPath, $signed);

            $invoice->update([
                'cufe'        => $cufe,
                'qr_url'      => $this->config->qrBaseUrl().$cufe,
                'xml_path'    => $xmlPath,
                'dian_status' => 'SIGNED',
            ]);

            DianEvent::log($invoice, 'SIGN', null, 'XML firmado correctamente', null, null);

            $result = $this->soap->sendBillSync($signed, $fileNameNoExt);

            $arPath = null;
            if (! empty($result['response_xml'])) {
                $arPath = 'dian/application_response/AR-'.$fileNameNoExt.'.xml';
                Storage::disk('local')->put($arPath, $result['response_xml']);
            }

            $accepted = in_array($result['code'], ['00', '0', 'Procesado Correctamente'], true);

            $invoice->update([
                'dian_status'        => $accepted ? 'ACCEPTED' : 'REJECTED',
                'dian_response_code' => $result['code'],
                'dian_description'   => $result['description'],
                'dian_zip_id'        => $result['zip_key'],
                'ar_path'            => $arPath,
                'sent_at'            => now(),
                'accepted_at'        => $accepted ? now() : null,
            ]);

            DianEvent::log(
                $invoice,
                $accepted ? 'ACCEPT' : 'REJECT',
                $result['code'],
                $result['description'],
                $signed,
                $result['raw']
            );

            return $invoice->refresh();
        } catch (\Throwable $e) {
            Log::error('DIAN envío falló', [
                'electronic_invoice_id' => $invoice->id,
                'error'                 => $e->getMessage(),
                'trace'                 => $e->getTraceAsString(),
            ]);

            $invoice->update([
                'dian_status'      => 'ERROR',
                'dian_description' => $e->getMessage(),
            ]);

            DianEvent::log($invoice, 'ERROR', null, $e->getMessage());

            return $invoice;
        }
    }

    public function retry(ElectronicInvoice $invoice): ElectronicInvoice
    {
        if (! $invoice->isElectronic()) {
            return $invoice;
        }

        $invoice->update(['dian_status' => 'PENDING']);

        return $this->sendInvoice($invoice);
    }

    private function buildFileName(ElectronicInvoice $invoice): string
    {
        $nit = preg_replace('/\D/', '', (string) $this->config->emisor()['nit']);

        return sprintf('%s-01-%s', $nit, $invoice->full_number);
    }
}
