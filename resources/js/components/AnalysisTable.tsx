import Spreadsheet, { CellBase, Matrix } from "react-spreadsheet";
import { Button } from "@/components/ui/button"
import { useState } from "react"
import { router } from '@inertiajs/react'
import { toast } from 'sonner'

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

  const handleSave = () => {
    // Transform the data to match the expected format
    const tableData = data.map((row: Row) => {
      return row.reduce((acc: Record<string, string>, cell: Cell, index: number) => {
        const fieldName = columnFieldNames[index];
        return {
          ...acc,
          [fieldName]: cell?.value || ''
        };
      }, {} as Record<string, string>);
    });

    // Add the current date and time for created_at and updated_at
    const now = new Date().toISOString();
    const dataWithTimestamps = tableData.map(row => ({
      ...row,
      created_at: now,
      updated_at: now,
    }));

    router.post('/analyses/save',
      { rows: dataWithTimestamps },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          toast.success(`Successfully saved ${dataWithTimestamps.length} rows`);
          // Optionally refresh the data
          // router.reload();
        },
        onError: (errors) => {
          console.error('Save error:', errors);
          toast.error('Failed to save data. Please check the console for details.');
        }
      }
    );
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
      <Spreadsheet
        data={rows}
        columnLabels={columnDisplayNames}
        onChange={handleChange}
      />
      <Button className="mt-5 mb-5" onClick={handleSave}>
        Sauvegarder
      </Button>
    </>
  )
}
