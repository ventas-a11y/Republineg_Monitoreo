import { Head, Link } from '@inertiajs/react';
import { Plus, Users } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
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

function AccessStatus({ user }: { user: UserRow }) {
    if (user.invitation_expired) {
        return <Badge variant="destructive">Invitación vencida</Badge>;
    }

    if (user.invitation_pending) {
        return <Badge variant="outline">Invitación pendiente</Badge>;
    }

    if (!user.has_second_factor) {
        return <Badge variant="outline">Sin segundo factor</Badge>;
    }

    return <Badge variant="secondary">Activa</Badge>;
}

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

                {users.length === 0 ? (
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
                ) : (
                    <div className="rounded-lg border">
                        <Table>
                            <TableHeader className="bg-muted/50">
                                <TableRow>
                                    <TableHead className="px-4">
                                        Persona
                                    </TableHead>
                                    <TableHead className="px-4">Rol</TableHead>
                                    <TableHead className="px-4">
                                        Áreas
                                    </TableHead>
                                    <TableHead className="px-4">
                                        Acceso
                                    </TableHead>
                                    <TableHead className="px-4">
                                        <span className="sr-only">
                                            Acciones
                                        </span>
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.map((user) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="px-4 py-3 break-words whitespace-normal">
                                            <div className="font-medium">
                                                {user.name}
                                            </div>
                                            <div
                                                className="text-muted-foreground"
                                                translate="no"
                                            >
                                                {user.email}
                                            </div>
                                        </TableCell>
                                        <TableCell className="px-4 py-3">
                                            {user.role_label}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 whitespace-normal">
                                            {user.areas.length > 0
                                                ? user.areas.join(', ')
                                                : '—'}
                                        </TableCell>
                                        <TableCell className="px-4 py-3">
                                            <AccessStatus user={user} />
                                        </TableCell>
                                        <TableCell className="px-4 py-3 text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={edit(user.id)}
                                                    aria-label={`Editar la cuenta de ${user.name}`}
                                                >
                                                    Editar
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

UsersIndex.layout = {
    breadcrumbs: [{ title: 'Cuentas', href: index() }],
};
