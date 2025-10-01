import AnalysisTable from '@/components/AnalysisTable';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Analyses',
        href: '/analyses',
    },
];

export default function index() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Analyses" />
   
                <h1 className="text-2xl font-bold text-center my-4">Analyses</h1>
                <AnalysisTable />
            
        </AppLayout>
    );
}
