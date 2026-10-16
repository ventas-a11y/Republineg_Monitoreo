import { Form, Head } from '@inertiajs/react';
import { createColumnHelper } from '@tanstack/react-table';
import { Building2, Pencil } from 'lucide-react';
import AreaController from '@/actions/App/Http/Controllers/Admin/AreaController';
import { DataTableColumnHeader } from '@/components/data-table/column-header';
import { DataTable } from '@/components/data-table/data-table';
import type { DataTableFeatures } from '@/components/data-table/features';
import Heading from '@/components/heading';
import { IconAction } from '@/components/icon-action';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { edit, index } from '@/routes/admin/areas';

type AreaRow = {
    id: string;
    name: string;
    employees_count: number;
    reviewers_count: number;
};

const columnHelper = createColumnHelper<DataTableFeatures, AreaRow>();

const columns = columnHelper.columns([
    columnHelper.accessor('name', {
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Área" />
        ),
        cell: ({ getValue }) => (
            <span className="font-medium break-words whitespace-normal">
                {getValue()}
            </span>
        ),
    }),
    columnHelper.accessor('employees_count', {
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Empleados" />
        ),
        cell: ({ getValue }) => (
            <span className="tabular-nums">{getValue()}</span>
        ),
    }),
    columnHelper.accessor('reviewers_count', {
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Revisores" />
        ),
        cell: ({ getValue }) => (
            <span className="tabular-nums">{getValue()}</span>
        ),
    }),
    columnHelper.display({
        id: 'actions',
        header: () => <span className="sr-only">Acciones</span>,
        cell: ({ row }) => (
            <div className="flex justify-end">
                <IconAction
                    label={`Renombrar el Área ${row.original.name}`}
                    icon={Pencil}
                    href={edit(row.original.id)}
                />
            </div>
        ),
    }),
]);

export default function AreasIndex({ areas }: { areas: AreaRow[] }) {
    return (
        <>
            <Head title="Áreas" />

            <div className="space-y-8 p-4">
                <Heading
                    title="Áreas"
                    description="Grupos de Empleados que comparten reglas y clasificación de apps."
                />

                <Form
                    {...AreaController.store.form()}
                    resetOnSuccess
                    options={{ preserveScroll: true }}
                    className="flex max-w-xl flex-wrap items-end gap-3"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid flex-1 gap-2">
                                <Label htmlFor="name">Nueva Área</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    autoComplete="off"
                                    placeholder="p. ej., Contabilidad…"
                                />
                            </div>
                            <Button type="submit" disabled={processing}>
                                {processing && <Spinner />}
                                Crear Área
                            </Button>
                            <InputError
                                message={errors.name}
                                className="w-full"
                            />
                        </>
                    )}
                </Form>

                <div className="max-w-3xl">
                    <DataTable
                        columns={columns}
                        data={areas}
                        searchPlaceholder="Buscar Área…"
                        empty={
                            <Empty className="border">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Building2 aria-hidden="true" />
                                    </EmptyMedia>
                                    <EmptyTitle>
                                        Todavía no hay Áreas
                                    </EmptyTitle>
                                    <EmptyDescription>
                                        Crea la primera con el formulario de
                                        arriba.
                                    </EmptyDescription>
                                </EmptyHeader>
                            </Empty>
                        }
                    />
                </div>
            </div>
        </>
    );
}

AreasIndex.layout = {
    breadcrumbs: [{ title: 'Áreas', href: index() }],
};
