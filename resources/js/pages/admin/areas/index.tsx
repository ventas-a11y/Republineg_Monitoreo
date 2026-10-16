import { Form, Head, Link } from '@inertiajs/react';
import { Building2 } from 'lucide-react';
import AreaController from '@/actions/App/Http/Controllers/Admin/AreaController';
import Heading from '@/components/heading';
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { edit, index } from '@/routes/admin/areas';

type AreaRow = {
    id: string;
    name: string;
    employees_count: number;
    reviewers_count: number;
};

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

                {areas.length === 0 ? (
                    <Empty className="max-w-3xl border">
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <Building2 aria-hidden="true" />
                            </EmptyMedia>
                            <EmptyTitle>Todavía no hay Áreas</EmptyTitle>
                            <EmptyDescription>
                                Crea la primera con el formulario de arriba.
                            </EmptyDescription>
                        </EmptyHeader>
                    </Empty>
                ) : (
                    <div className="max-w-3xl rounded-lg border">
                        <Table>
                            <TableHeader className="bg-muted/50">
                                <TableRow>
                                    <TableHead className="px-4">Área</TableHead>
                                    <TableHead className="px-4">
                                        Empleados
                                    </TableHead>
                                    <TableHead className="px-4">
                                        Revisores
                                    </TableHead>
                                    <TableHead className="px-4">
                                        <span className="sr-only">
                                            Acciones
                                        </span>
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {areas.map((area) => (
                                    <TableRow key={area.id}>
                                        <TableCell className="px-4 py-3 font-medium break-words whitespace-normal">
                                            {area.name}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 tabular-nums">
                                            {area.employees_count}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 tabular-nums">
                                            {area.reviewers_count}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={edit(area.id)}
                                                    aria-label={`Renombrar el Área ${area.name}`}
                                                >
                                                    Renombrar
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </div>
        </>
    );
}

AreasIndex.layout = {
    breadcrumbs: [{ title: 'Áreas', href: index() }],
};
