import Spreadsheet from "react-spreadsheet"
import { Button } from "@/components/ui/button"
import { useState } from "react"
import axios from "axios";
import { toast } from 'sonner';

const columnNames = [
  "Numéro Rapport",
  "Date et lieu de prélèvement",
  "Date, heure et T°C à la réception au laboratoire",
  "Conditions de conservation à la réception",
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
  "A consommer jusqu’au",
  "IMP",
  "HX",
  "Note Nucléotide",
  "Cotation fraîcheur",
  "Observations",
  "Ref Rapport",
]

export default function AnalysisTable() {
  const [data, setData] = useState<Array<Array<{value: string}>>>([])

  const handleSave = async () => {
    const tableData = data.map(row => {
      return row.reduce((acc, cell, index) => {
        const columnName = columnNames[index];
        return {
          ...acc,
          [columnName]: cell?.value || ''
        };
      }, {});
    });
    
    try {
      const response = await axios.post('/analyses/save', tableData, {
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      });
      
      console.log('Data saved successfully:', response.data);
      toast.success(`Successfully saved ${response.data.saved_rows} rows`);
    } catch (error) {
      console.error('Error saving data:', error);
      toast.error('Failed to save data');
    }
  }

  const handleChange = (newData: any) => {
    setData(newData);
    console.log(newData)
  }

  const createEmptyRow = () => columnNames.map(() => ({ value: '' }));

  const rows = data.length > 0 ? data : Array(20).fill(null).map(createEmptyRow);

  return (
    <>
      <Spreadsheet 
        data={rows} 
        columnLabels={columnNames} 
        onChange={handleChange}
      />
      <Button className="mt-5 mb-5" onClick={handleSave}>
        Sauvegarder
      </Button>
    </>
  )
}
