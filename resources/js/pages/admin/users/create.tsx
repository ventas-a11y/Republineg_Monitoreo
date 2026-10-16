import { Head } from '@inertiajs/react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import UserForm from '@/components/admin/user-form';
import type { AreaOption, RoleOption } from '@/components/admin/user-form';
import Heading from '@/components/heading';
import { create, index } from '@/routes/admin/users';

type Props = {
    roles: RoleOption[];
    areas: AreaOption[];
};

export default function CreateUser({ roles, areas }: Props) {
    return (
        <>
            <Head title="Nueva cuenta" />

            <div className="p-4">
                <Heading
                    title="Nueva cuenta"
                    description="La persona recibirá por correo una contraseña temporal que vence en 72 horas."
                />

                <UserForm
                    action={UserController.store.form()}
                    roles={roles}
                    areas={areas}
                    submitLabel="Crear cuenta y enviar invitación"
                />
            </div>
        </>
    );
}

CreateUser.layout = {
    breadcrumbs: [
        { title: 'Cuentas', href: index() },
        { title: 'Nueva cuenta', href: create() },
    ],
};
