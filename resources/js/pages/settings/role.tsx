import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { RollerCoasterIcon } from 'lucide-react';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role',
        href: '/settings/role',
    },
];

interface RoleProps {
    role: string;
}

export default function Role({ role }: RoleProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Role" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Role"
                        description="See your role"
                    />
                    <div className="bg-white p-6 rounded-lg border">
                        <h2 className="text-lg font-medium text-gray-900">Your Role</h2>
                        <p className="mt-2 text-gray-600">
                            Current role: <span className="font-bold uppercase  ">{role}</span>
                        </p>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
