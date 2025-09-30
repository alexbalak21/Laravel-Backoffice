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
            <div className="mx-4 overflow-x-auto">
                <h1 className="">Analyses</h1>
                <AnalysisTable />
            </div>
        </AppLayout>
    );
}
