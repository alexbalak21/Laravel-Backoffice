import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Analysis } from '@/types';

interface ShowProps {
  analyses: Analysis[];
}

export default function Show({ analyses }: ShowProps) {
  const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    });
  };

  const formatDateTime = (dateTimeString: string | null) => {
    if (!dateTimeString) return '-';
    return new Date(dateTimeString).toLocaleString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (!analyses || analyses.length === 0) {
    return <div className="text-center py-4">Aucune donnée disponible</div>;
  }

  return (
    <div className="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Numéro Rapport</TableHead>
            <TableHead>Date et lieu de prélèvement</TableHead>
            <TableHead>Date, heure et T°C à la réception</TableHead>
            <TableHead>Conditions de conservation</TableHead>
            <TableHead>Date de mise en analyse</TableHead>
            <TableHead>Fournisseur/Fabricant</TableHead>
            <TableHead>Conditionnement</TableHead>
            <TableHead>Agrément</TableHead>
            <TableHead>Lot</TableHead>
            <TableHead>Type de pèche</TableHead>
            <TableHead>Nom de produit</TableHead>
            <TableHead>Espèce</TableHead>
            <TableHead>Origine</TableHead>
            <TableHead>Date d'emballage</TableHead>
            <TableHead>A consommer jusqu'au</TableHead>
            <TableHead>IMP</TableHead>
            <TableHead>HX</TableHead>
            <TableHead>Note Nucléotide</TableHead>
            <TableHead>Cotation fraîcheur</TableHead>
            <TableHead>Observations</TableHead>
            <TableHead>Ref Rapport</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {analyses.map((analysis, index) => (
            <TableRow key={index}>
              <TableCell>{analysis.numero_rapport || '-'}</TableCell>
              <TableCell>{analysis.lieu_prelevement || '-'}</TableCell>
              <TableCell>
                {analysis.date_heure_reception_laboratoire 
                  ? formatDateTime(analysis.date_heure_reception_laboratoire) 
                  : '-'}
                {analysis.temperature_reception ? ` (${analysis.temperature_reception}°C)` : ''}
              </TableCell>
              <TableCell>{analysis.conditions_conservation || '-'}</TableCell>
              <TableCell>{formatDateTime(analysis.date_heure_analyse) || '-'}</TableCell>
              <TableCell>{analysis.fournisseur_fabricant || '-'}</TableCell>
              <TableCell>{analysis.conditionnement || '-'}</TableCell>
              <TableCell>{analysis.agrement || '-'}</TableCell>
              <TableCell>{analysis.lot || '-'}</TableCell>
              <TableCell>{analysis.type_peche || '-'}</TableCell>
              <TableCell>{analysis.nom_produit || '-'}</TableCell>
              <TableCell>{analysis.espece || '-'}</TableCell>
              <TableCell>{analysis.origine || '-'}</TableCell>
              <TableCell>{formatDate(analysis.date_emballage) || '-'}</TableCell>
              <TableCell>{formatDate(analysis.date_consommation) || '-'}</TableCell>
              <TableCell>{analysis.imp || '-'}</TableCell>
              <TableCell>{analysis.hx || '-'}</TableCell>
              <TableCell>{analysis.note_nucleotide || '-'}</TableCell>
              <TableCell>{analysis.cotation_fraicheur || '-'}</TableCell>
              <TableCell className="max-w-xs truncate">{analysis.observations || '-'}</TableCell>
              <TableCell>{analysis.ref_rapport || '-'}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}
