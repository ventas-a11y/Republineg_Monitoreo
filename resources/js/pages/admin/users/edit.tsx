import { Form, Head } from '@inertiajs/react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import UserForm from '@/components/admin/user-form';
import type {
    AreaOption,
    RoleOption,
    UserFormValues,
} from '@/components/admin/user-form';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { edit, index } from '@/routes/admin/users';

type Props = {
    user: UserFormValues & { id: string; invitation_pending: boolean };
    roles: RoleOption[];
    areas: AreaOption[];
};

export default function EditUser({ user, roles, areas }: Props) {
    return (
        <>
            <Head title={`Editar ${user.name}`} />

            <div className="space-y-10 p-4">
                <div>
                    <Heading title={user.name} description={user.email} />

                    <UserForm
                        action={UserController.update.form(user.id)}
                        roles={roles}
                        areas={areas}
                        defaults={user}
                        submitLabel="Guardar cambios"
                    />
                </div>

                <section className="max-w-xl space-y-3 border-t pt-6">
                    <Heading
                        variant="small"
                        title="Invitación"
                        description={
                            user.invitation_pending
                                ? 'Esta persona todavía no cambió su contraseña temporal.'
                                : 'Enviar una invitación nueva reemplaza la contraseña actual por una temporal.'
                        }
                    />
                    <Dialog>
                        <DialogTrigger asChild>
                            <Button variant="outline">
                                Enviar nueva invitación
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>
                                    ¿Enviar una nueva invitación a {user.name}?
                                </DialogTitle>
                                <DialogDescription>
                                    Su contraseña actual dejará de funcionar.
                                    Recibirá en {user.email} una contraseña
                                    temporal que vence en 72 horas y tendrá que
                                    cambiarla al entrar.
                                </DialogDescription>
                            </DialogHeader>
                            <Form
                                {...UserController.resendInvitation.form(
                                    user.id,
                                )}
                                options={{ preserveScroll: true }}
                            >
                                {({ processing }) => (
                                    <DialogFooter>
                                        <DialogClose asChild>
                                            <Button
                                                type="button"
                                                variant="outline"
                                            >
                                                Cancelar
                                            </Button>
                                        </DialogClose>
                                        <Button
                                            type="submit"
                                            variant="destructive"
                                            disabled={processing}
                                        >
                                            {processing && <Spinner />}
                                            Sí, reemplazar contraseña
                                        </Button>
                                    </DialogFooter>
                                )}
                            </Form>
                        </DialogContent>
                    </Dialog>
                </section>
            </div>
        </>
    );
}

EditUser.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Cuentas', href: index() },
        { title: props.user.name, href: edit(props.user.id) },
    ],
});
