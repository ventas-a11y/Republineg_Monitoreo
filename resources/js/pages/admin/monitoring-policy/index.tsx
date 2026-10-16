import { Form, Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { PolicyDocument } from '@/components/policy-document';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { Textarea } from '@/components/ui/textarea';
import { index, store } from '@/routes/admin/monitoring-policy';

type Props = {
    current: {
        version: number;
        title: string;
        body: string;
        body_html: string;
    } | null;
    versions: {
        id: string;
        version: number;
        title: string;
        published_at: string;
        publisher: string | null;
        acceptances_count: number;
    }[];
    pending: { id: string; name: string; email: string; area: string | null }[];
    empleadosCount: number;
};

const dateFormat = new Intl.DateTimeFormat('es-EC', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

export default function MonitoringPolicyAdmin({
    current,
    versions,
    pending,
    empleadosCount,
}: Props) {
    const accepted = empleadosCount - pending.length;

    return (
        <>
            <Head title="Política de monitoreo" />

            <div className="space-y-8 p-4">
                <Heading
                    title="Política de monitoreo"
                    description="Cada Empleado debe aceptar la versión vigente antes de usar el panel. Sin esa aceptación no se reciben datos de su Equipo."
                />

                {current !== null && (
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                Versión {current.version} vigente
                            </CardTitle>
                            <CardDescription>
                                <span className="tabular-nums">
                                    {accepted} de {empleadosCount}
                                </span>{' '}
                                Empleados la aceptaron.
                            </CardDescription>
                        </CardHeader>
                        {pending.length > 0 && (
                            <CardContent>
                                <h3 className="mb-2 text-sm font-medium">
                                    Aceptación pendiente
                                </h3>
                                <div className="rounded-lg border">
                                    <Table>
                                        <TableHeader className="bg-muted/50">
                                            <TableRow>
                                                <TableHead className="px-4">
                                                    Persona
                                                </TableHead>
                                                <TableHead className="px-4">
                                                    Área
                                                </TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {pending.map((person) => (
                                                <TableRow key={person.id}>
                                                    <TableCell className="px-4 py-3 break-words whitespace-normal">
                                                        <div className="font-medium">
                                                            {person.name}
                                                        </div>
                                                        <div
                                                            className="text-muted-foreground"
                                                            translate="no"
                                                        >
                                                            {person.email}
                                                        </div>
                                                    </TableCell>
                                                    <TableCell className="px-4 py-3">
                                                        {person.area ?? '—'}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            </CardContent>
                        )}
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {current === null
                                ? 'Publicar la primera versión'
                                : 'Publicar una versión nueva'}
                        </CardTitle>
                        <CardDescription>
                            Una versión publicada no se edita. Al publicar otra,
                            cada Empleado deberá aceptarla de nuevo. Puedes usar
                            Markdown: <code>## Título</code>, listas con{' '}
                            <code>-</code> y <code>**negrita**</code>.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            {...store.form()}
                            options={{ preserveScroll: true }}
                            className="space-y-6"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="title">Título</Label>
                                        <Input
                                            id="title"
                                            name="title"
                                            required
                                            autoComplete="off"
                                            defaultValue={
                                                current?.title ??
                                                'Política de monitoreo de equipos'
                                            }
                                        />
                                        <InputError message={errors.title} />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="body">
                                            Texto de la política
                                        </Label>
                                        <Textarea
                                            id="body"
                                            name="body"
                                            required
                                            rows={16}
                                            className="font-mono text-sm"
                                            defaultValue={current?.body}
                                            placeholder="## Qué se registra…"
                                        />
                                        <InputError message={errors.body} />
                                    </div>
                                    <Button type="submit" disabled={processing}>
                                        {processing && <Spinner />}
                                        Publicar versión
                                    </Button>
                                </>
                            )}
                        </Form>
                    </CardContent>
                </Card>

                {current !== null && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Así la ven los Empleados</CardTitle>
                            <CardDescription>{current.title}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <PolicyDocument html={current.body_html} />
                        </CardContent>
                    </Card>
                )}

                <section className="space-y-3">
                    <h2 className="text-base font-medium">Versiones</h2>
                    {versions.length === 0 ? (
                        <Empty className="border">
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <FileText aria-hidden="true" />
                                </EmptyMedia>
                                <EmptyTitle>
                                    Todavía no hay versiones publicadas
                                </EmptyTitle>
                                <EmptyDescription>
                                    Mientras no exista una política, no se
                                    reciben datos de ningún Equipo.
                                </EmptyDescription>
                            </EmptyHeader>
                        </Empty>
                    ) : (
                        <div className="rounded-lg border">
                            <Table>
                                <TableHeader className="bg-muted/50">
                                    <TableRow>
                                        <TableHead className="px-4">
                                            Versión
                                        </TableHead>
                                        <TableHead className="px-4">
                                            Título
                                        </TableHead>
                                        <TableHead className="px-4">
                                            Publicada
                                        </TableHead>
                                        <TableHead className="px-4 text-right">
                                            Aceptaciones
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {versions.map((version, position) => (
                                        <TableRow key={version.id}>
                                            <TableCell className="px-4 py-3">
                                                <span className="tabular-nums">
                                                    {version.version}
                                                </span>{' '}
                                                {position === 0 && (
                                                    <Badge variant="secondary">
                                                        Vigente
                                                    </Badge>
                                                )}
                                            </TableCell>
                                            <TableCell className="px-4 py-3 break-words whitespace-normal">
                                                {version.title}
                                            </TableCell>
                                            <TableCell className="px-4 py-3">
                                                {dateFormat.format(
                                                    new Date(
                                                        version.published_at,
                                                    ),
                                                )}
                                                {version.publisher && (
                                                    <div className="text-muted-foreground">
                                                        {version.publisher}
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell className="px-4 py-3 text-right tabular-nums">
                                                {version.acceptances_count}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    )}
                </section>
            </div>
        </>
    );
}

MonitoringPolicyAdmin.layout = {
    breadcrumbs: [{ title: 'Política de monitoreo', href: index() }],
};
