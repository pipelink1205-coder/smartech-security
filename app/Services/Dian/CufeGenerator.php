<?php

namespace App\Services\Dian;

use App\Models\ElectronicInvoice;
use App\Models\DianCreditNote;

/**
 * Generador del CUFE (Código Único de Factura Electrónica) y CUDE
 * (Código Único de Documento Electrónico) según el Anexo Técnico DIAN 1.8.
 *
 * Fórmula CUFE Factura de Venta:
 *   SHA-384( NumFac + FecFac + HorFac + ValFac + CodImp1 + ValImp1
 *          + CodImp2 + ValImp2 + CodImp3 + ValImp3 + ValTot
 *          + NitOFE + NumAdq + ClTec + TipoAmbiente )
 *
 * Fórmula CUDE Nota Crédito/Débito (igual estructura, sin desglose de impuestos):
 *   SHA-384( NumNC + FecNC + HorNC + ValTot + NitOFE + NumAdq + ClTec + TipoAmbiente )
 */
class CufeGenerator
{
    public function __construct(private DianConfig $config) {}

    /**
     * Calcula el CUFE para una factura electrónica de venta.
     *
     * @param ElectronicInvoice $invoice Factura con dian_prefijo, dian_numero, subtotal, iva, ico, total y created_at.
     * @return string Hash SHA-384 en hexadecimal (96 caracteres).
     */
    public function forInvoice(ElectronicInvoice $invoice): string
    {
        $numFac = $invoice->full_number;                                  // p.e. SETP1
        $fecFac = $invoice->created_at->format('Y-m-d');
        $horFac = $invoice->created_at->format('H:i:s-05:00');            // Zona Colombia

        // Importes con 2 decimales y punto como separador
        $valFac = $this->money($invoice->subtotal);
        $valImp1 = $this->money($invoice->iva);
        $valImp2 = $this->money($invoice->ico);
        $valImp3 = '0.00';                                              // ICA u otros (no aplica)
        $valTot  = $this->money($invoice->total);

        $codImp1 = '01';   // IVA
        $codImp2 = '04';   // INC (Impuesto al Consumo)
        $codImp3 = '03';   // ICA

        $emisor = $this->config->emisor();
        $nitOFE = preg_replace('/\D/', '', (string) ($emisor['nit'] ?? ''));
        $numAdq = preg_replace('/\D/', '', (string) ($invoice->client_document ?? '222222222222'));

        $clTec = $this->config->claveTecnica();

        $tipoAmbiente = (string) $this->config->environment();          // 1 o 2

        $cadena = $numFac . $fecFac . $horFac
                . $valFac . $codImp1 . $valImp1
                . $codImp2 . $valImp2 . $codImp3 . $valImp3
                . $valTot . $nitOFE . $numAdq . $clTec . $tipoAmbiente;

        return hash('sha384', $cadena);
    }

    /**
     * Calcula el CUDE para una nota crédito/débito DIAN.
     */
    public function forCreditNote(DianCreditNote $note): string
    {
        $numNC = $note->full_number;
        $fecNC = $note->created_at->format('Y-m-d');
        $horNC = $note->created_at->format('H:i:s-05:00');

        $valTot = $this->money($note->total);

        $emisor = $this->config->emisor();
        $nitOFE = preg_replace('/\D/', '', (string) ($emisor['nit'] ?? ''));
        $numAdq = preg_replace('/\D/', '', (string) ($note->invoice->client_document ?? '222222222222'));

        $clTec = $this->config->claveTecnica();

        $tipoAmbiente = (string) $this->config->environment();

        $cadena = $numNC . $fecNC . $horNC . $valTot . $nitOFE . $numAdq . $clTec . $tipoAmbiente;

        return hash('sha384', $cadena);
    }

    private function money($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
