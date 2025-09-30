import Spreadsheet from "react-spreadsheet"

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
  const createEmptyRow = () => columnNames.map(() => ({value: ""}))

  const data = Array(20).fill(null).map(createEmptyRow)

  return <Spreadsheet data={data} columnLabels={columnNames} />
}
