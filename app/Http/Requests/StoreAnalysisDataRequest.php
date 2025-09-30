<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnalysisDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Or add your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rows' => 'required|array',
            'rows.*' => 'array',
        ];
    }

    /**
     * Process and validate the incoming data from the spreadsheet
     *
     * @return array
     */
    public function validatedData(): array
    {
        $validated = $this->validated();
        $result = [];

        // Define a mapping between display names and database fields
        $fieldMap = [
            'Numéro Rapport' => 'numero_rapport',
            'Date et lieu de prélèvement' => 'prelevement_data', // Will be split into two fields
            'Date, heure et T°C à la réception' => 'date_heure_reception_laboratoire',
            'Conditions de conservation' => 'conditions_conservation',
            'Date de mise en analyse' => 'date_heure_analyse',
            'Fournisseur/Fabricant' => 'fournisseur_fabricant',
            'Conditionnement' => 'conditionnement',
            'Agrément' => 'agrement',
            'Lot' => 'lot',
            'Type de pèche' => 'type_peche',
            'Nom de produit' => 'nom_produit',
            'Espèce' => 'espece',
            'Origine' => 'origine',
            'Date d\'emballage' => 'date_emballage',
            'Date de consommation' => 'date_consommation',
            'IMP' => 'imp',
            'HX' => 'hx',
            'Note Nucléotide' => 'note_nucleotide',
            'Cotation fraîcheur' => 'cotation_fraicheur',
            'Observations' => 'observations',
            'Référence rapport' => 'ref_rapport',
        ];

        // Define default values for required fields
        $defaultValues = [
            'conditions_conservation' => null,
            'date_emballage' => now()->format('Y-m-d'),
            'date_consommation' => now()->addDays(7)->format('Y-m-d'),
            'imp' => 0,
            'hx' => 0,
            'note_nucleotide' => null,
            'cotation_fraicheur' => null,
            'observations' => null,
            'ref_rapport' => 'REF-' . now()->format('YmdHis'),
        ];

        foreach ($validated['rows'] as $row) {
            if (!is_array($row)) continue;

            $processedRow = [];

            // First, map all the fields that don't need special processing
            foreach ($fieldMap as $displayName => $dbField) {
                $value = $row[$displayName] ?? null;
                
                // If value is empty, use default if available
                if (empty($value) && array_key_exists($dbField, $defaultValues)) {
                    $value = $defaultValues[$dbField];
                }
                
                $processedRow[$dbField] = $value;
            }

            // Special processing for dates and numbers
            try {
                // Set default values for required fields if they're still empty
                foreach ($defaultValues as $field => $defaultValue) {
                    if (empty($processedRow[$field])) {
                        $processedRow[$field] = $defaultValue;
                    }
                }

                // Parse prelevement data (e.g., "COPROMER, 27/08/2025, 11h30")
                if (!empty($processedRow['prelevement_data'])) {
                    $prelevement = $this->parsePrelevementData($processedRow['prelevement_data']);
                    $processedRow['lieu_prelevement'] = $prelevement['lieu'] ?? null;
                    $processedRow['date_heure_prelevement'] = $prelevement['date_heure'] ?? now();
                    unset($processedRow['prelevement_data']); // Remove the temporary field
                } else {
                    $processedRow['lieu_prelevement'] = null;
                    $processedRow['date_heure_prelevement'] = now();
                }

                // Parse reception data (e.g., "25/08/2025, 11h45, 4°C")
                if (!empty($processedRow['date_heure_reception_laboratoire'])) {
                    $reception = $this->parseReceptionData($processedRow['date_heure_reception_laboratoire']);
                    $processedRow['temperature_reception'] = $reception['temperature'] ?? 0;
                    $processedRow['date_heure_reception_laboratoire'] = $reception['date_heure'] ?? now();
                } else {
                    $processedRow['temperature_reception'] = 0;
                    $processedRow['date_heure_reception_laboratoire'] = now();
                }

                // Parse analysis date (e.g., "25/08/2025, 13h")
                if (!empty($processedRow['date_heure_analyse'])) {
                    $processedRow['date_heure_analyse'] = $this->parseDateTime($processedRow['date_heure_analyse']);
                } else {
                    $processedRow['date_heure_analyse'] = now();
                }

                // Parse numeric values from IMP and HX (e.g., "1%" => 1.0)
                $processedRow['imp'] = is_numeric(str_replace(['%', ' '], '', $processedRow['imp'])) ? 
                    (float)str_replace(['%', ' '], '', $processedRow['imp']) : 0;
                    
                $processedRow['hx'] = is_numeric(str_replace(['%', ' '], '', $processedRow['hx'])) ? 
                    (float)str_replace(['%', ' '], '', $processedRow['hx']) : 0;

                // Ensure required fields have values
                $requiredFields = [
                    'numero_rapport' => 'RAPPORT-' . now()->format('YmdHis'),
                    'lieu_prelevement' => null,
                    'fournisseur_fabricant' => null,
                    'conditionnement' => null,
                    'agrement' => null,
                    'lot' => null,
                    'type_peche' => null,
                    'nom_produit' => null,
                    'espece' => null,
                    'origine' => null,
                ];

                foreach ($requiredFields as $field => $defaultValue) {
                    if (empty($processedRow[$field])) {
                        $processedRow[$field] = $defaultValue;
                    }
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the entire import
                \Log::error('Error processing row: ' . $e->getMessage(), [
                    'row' => $row,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Add the row with default values if possible
                if (!empty($processedRow['numero_rapport'])) {
                    $result[] = array_merge($defaultValues, $processedRow, [
                        'error' => $e->getMessage()
                    ]);
                }
                continue;
            }

            $result[] = $processedRow;
        }
        
        return $result;
    }
    
    /**
     * Parse reception data string (e.g., "25/08/2025, 11h45, 4°C")
     */
    protected function parseReceptionData(string $input): array
    {
        $result = [
            'date_heure' => null,
            'temperature' => null
        ];
        
        // Extract date and time
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2})h(\d{0,2})?/i', $input, $matches)) {
            $time = $matches[2] . ':' . (!empty($matches[3]) ? $matches[3] : '00') . ':00';
            $result['date_heure'] = Carbon::createFromFormat('d/m/Y H:i:s', $matches[1] . ' ' . $time);
        }
        
        // Extract temperature
        if (preg_match('/(\d+(\.\d+)?)\s*°C/i', $input, $tempMatch)) {
            $result['temperature'] = (float)$tempMatch[1];
        }
        
        return $result;
    }
    
    /**
     * Parse prelevement data string (e.g., "COPROMER, 27/08/2025, 11h30")
     */
    protected function parsePrelevementData(string $input): array
    {
        $result = [
            'lieu' => null,
            'date_heure' => now()
        ];
        
        // Split by comma and clean up the parts
        $parts = array_map('trim', explode(',', $input));
        
        // First part is the location
        if (count($parts) > 0) {
            $result['lieu'] = $parts[0];
        }
        
        // Second part should be the date (and possibly time)
        if (count($parts) > 1) {
            $dateTimeStr = $parts[1];
            
            // Check if there's a third part with time
            if (count($parts) > 2 && preg_match('/^(\d{1,2})h(\d{0,2})?/i', $parts[2], $timeMatches)) {
                $time = $timeMatches[1] . ':' . (!empty($timeMatches[2]) ? $timeMatches[2] : '00') . ':00';
                $dateTimeStr .= ' ' . $time;
            } else {
                $dateTimeStr .= ' 00:00:00';
            }
            
            try {
                $result['date_heure'] = Carbon::createFromFormat('d/m/Y H:i:s', $dateTimeStr);
            } catch (\Exception $e) {
                \Log::warning('Failed to parse prelevement date: ' . $dateTimeStr);
            }
        }
        
        return $result;
    }
    
    /**
     * Parse date time string (e.g., "25/08/2025, 13h")
     */
    protected function parseDateTime(string $dateTime): ?string
    {
        if (preg_match('/(\d{2}\/\d{2}\/\d{4}),?\s*(\d{1,2})h(\d{0,2})?/i', $dateTime, $matches)) {
            $time = $matches[2] . ':' . (!empty($matches[3]) ? $matches[3] : '00') . ':00';
            return Carbon::createFromFormat('d/m/Y H:i:s', $matches[1] . ' ' . $time)->toDateTimeString();
        }
        
        return null;
    }
}
