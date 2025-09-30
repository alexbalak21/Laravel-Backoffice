import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AnalysisTable from '@/components/AnalysisTable';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Analyses',
        href: '/analyses',
    },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Analyses" />
            <AnalysisTable />
        </AppLayout>
    );
}
