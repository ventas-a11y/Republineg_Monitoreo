import { Head, Link } from '@inertiajs/react';
import { createColumnHelper } from '@tanstack/react-table';
import { Pencil, Plus, Users } from 'lucide-react';
import { DataTableColumnHeader } from '@/components/data-table/column-header';
import { DataTable } from '@/components/data-table/data-table';
import type { DataTableFeatures } from '@/components/data-table/features';
import Heading from '@/components/heading';
import { IconAction } from '@/components/icon-action';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import { create, edit, index } from '@/routes/admin/users';
import type { Role } from '@/types';

type UserRow = {
    id: string;
    name: string;
    email: string;
    role: Role;
    role_label: string;
    areas: string[];
    has_second_factor: boolean;
    invitation_pending: boolean;
    invitation_expired: boolean;
};

type AccessState = {
    label: string;
    variant: 'destructive' | 'outline' | 'secondary';
};

function accessState(user: UserRow): AccessState {
    if (user.invitation_expired) {
        return { label: 'Invitación vencida', variant: 'destructive' };
    }

    if (user.invitation_pending) {
        return { label: 'Invitación pendiente', variant: 'outline' };
    }

    if (!user.has_second_factor) {
        return { label: 'Sin segundo factor', variant: 'outline' };
    }

    return { label: 'Activa', variant: 'secondary' };
}

const columnHelper = createColumnHelper<DataTableFeatures, UserRow>();

const columns = columnHelper.columns([
    columnHelper.accessor((user) => `${user.name} ${user.email}`, {
        id: 'person',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Persona" />
        ),
        cell: ({ row }) => (
            <div className="min-w-48 break-words whitespace-normal">
                <div className="font-medium">{row.original.name}</div>
                <div className="text-muted-foreground" translate="no">
                    {row.original.email}
                </div>
            </div>
        ),
    }),
    columnHelper.accessor('role_label', {
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Rol" />
        ),
    }),
    columnHelper.accessor((user) => user.areas.join(', '), {
        id: 'areas',
        header: 'Áreas',
        enableSorting: false,
        cell: ({ getValue }) => (
            <span className="whitespace-normal">{getValue() || '—'}</span>
        ),
    }),
    columnHelper.accessor((user) => accessState(user).label, {
        id: 'access',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Acceso" />
        ),
        cell: ({ row }) => {
            const state = accessState(row.original);

            return <Badge variant={state.variant}>{state.label}</Badge>;
        },
    }),
    columnHelper.display({
        id: 'actions',
        header: () => <span className="sr-only">Acciones</span>,
        cell: ({ row }) => (
            <div className="flex justify-end">
                <IconAction
                    label={`Editar la cuenta de ${row.original.name}`}
                    icon={Pencil}
                    href={edit(row.original.id)}
                />
            </div>
        ),
    }),
]);

export default function UsersIndex({ users }: { users: UserRow[] }) {
    return (
        <>
            <Head title="Cuentas" />

            <div className="space-y-6 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <Heading
                        title="Cuentas"
                        description="Solo un Administrador crea cuentas. Cada persona recibe una invitación por correo."
                    />
                    <Button asChild>
                        <Link href={create()}>
                            <Plus aria-hidden="true" />
                            Nueva cuenta
                        </Link>
                    </Button>
                </div>

                <DataTable
                    columns={columns}
                    data={users}
                    searchPlaceholder="Buscar por nombre, correo, rol, Área o estado…"
                    empty={
                        <Empty className="border">
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <Users aria-hidden="true" />
                                </EmptyMedia>
                                <EmptyTitle>Todavía no hay cuentas</EmptyTitle>
                                <EmptyDescription>
                                    Crea la primera con «Nueva cuenta».
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    }
                />
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [{ title: 'Cuentas', href: index() }],
};
