import Spreadsheet, { CellBase, Matrix } from "react-spreadsheet";
import { Button } from "@/components/ui/button"
import { useState } from "react"
import { router } from '@inertiajs/react';
import { toast } from 'sonner';

type Cell = CellBase<string> & { value: string };
type Row = Cell[];

// These are the display names for the UI
const columnDisplayNames = [
  "Numéro Rapport",
  "Date et lieu de prélèvement",
  "Date, heure et T°C à la réception",
  "Conditions de conservation",
  "Date de mise en analyse",
  "Fournisseur/Fabricant",
  "Conditionnement",
  "Agrément",
  "Lot",
  "Type de pèche",
  "Nom de produit",
  "Espèce",
  "Origine",
  "Date d'emballage",
  "Date de consommation",
  "IMP",
  "HX",
  "Note Nucléotide",
  "Cotation fraîcheur",
  "Observations",
  "Référence rapport",
];

// These are the field names that match the database columns
const columnFieldNames = [
  'numero_rapport',
  'lieu_prelevement',
  'date_heure_reception_laboratoire',
  'conditions_conservation',
  'date_heure_analyse',
  'fournisseur_fabricant',
  'conditionnement',
  'agrement',
  'lot',
  'type_peche',
  'nom_produit',
  'espece',
  'origine',
  'date_emballage',
  'date_consommation',
  'imp',
  'hx',
  'note_nucleotide',
  'cotation_fraicheur',
  'observations',
  'ref_rapport',
];

export default function AnalysisTable() {
  const [data, setData] = useState<Row[]>([]);

  const handleSave = async () => {
    try {
      // Transform the data to match the expected format and filter out empty rows
      const tableData = data
        .map((row: Row) => {
          const rowData: Record<string, any> = {};
          let hasData = false;
          
          // Map each cell to the correct field name
          row.forEach((cell, index) => {
            const fieldName = columnFieldNames[index];
            if (fieldName && cell?.value?.trim() !== '') {
              rowData[fieldName] = cell.value.trim();
              hasData = true;
            } else if (fieldName) {
              rowData[fieldName] = '';
            }
          });
          
          return hasData ? rowData : null;
        })
        .filter(Boolean); // Remove null entries (empty rows)
        console.log("date sent:", tableData);
      await router.post('/analyses', { rows: tableData }, {
        preserveScroll: true,
        preserveState: true,
        
        onSuccess: () => {
          toast.success('Data saved successfully');
          setData([]);
        },
        onError: (errors: any) => {
          const errorMessage = errors?.message || 'Failed to save data';
          toast.error(errorMessage);
        }
      });
    } catch (error) {
      console.error('Error in form submission:', error);
      toast.error('An unexpected error occurred. Please try again.');
    }
  }

  const handleChange = (newData: Matrix<CellBase<string>>) => {
    // Convert Matrix<CellBase> to our Row[] type
    const typedData = newData.map(row => 
      row.map(cell => ({
        value: cell?.value?.toString() || '',
        ...cell
      }))
    ) as Row[];
    setData(typedData)
    console.log(typedData)
  }

  const createEmptyRow = () => columnDisplayNames.map(() => ({ value: '' }))
  const rows = data.length > 0 ? data : Array(20).fill(null).map(createEmptyRow)

  return (
    <>
    <div className="mx-4 overflow-x-auto">
      <Spreadsheet
        data={rows}
        columnLabels={columnDisplayNames}
        onChange={handleChange}
      /></div>
      <Button className="my-4 ms-5" onClick={handleSave}>
        Sauvegarder
      </Button>
    </>
  )
}
