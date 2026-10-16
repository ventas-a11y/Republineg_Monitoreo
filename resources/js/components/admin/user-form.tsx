import { Form } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import type { Role } from '@/types';
import type { RouteFormDefinition } from '@/wayfinder';

export type RoleOption = { value: Role; label: string };
export type AreaOption = { id: string; name: string };

export type UserFormValues = {
    name: string;
    email: string;
    role: Role;
    area_id: string | null;
    reviewed_area_ids: string[];
};

type Props = {
    action: RouteFormDefinition<'post'> | RouteFormDefinition<'put'>;
    roles: RoleOption[];
    areas: AreaOption[];
    defaults?: UserFormValues;
    submitLabel: string;
};

export default function UserForm({
    action,
    roles,
    areas,
    defaults,
    submitLabel,
}: Props) {
    const [role, setRole] = useState<Role>(defaults?.role ?? 'empleado');

    return (
        <Form {...action} className="max-w-xl space-y-6">
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-2">
                        <Label htmlFor="name">Nombre</Label>
                        <Input
                            id="name"
                            name="name"
                            defaultValue={defaults?.name}
                            required
                            autoComplete="off"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Correo</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            defaultValue={defaults?.email}
                            required
                            autoComplete="off"
                            spellCheck={false}
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="role">Rol</Label>
                        <Select
                            name="role"
                            value={role}
                            onValueChange={(value) => setRole(value as Role)}
                        >
                            <SelectTrigger id="role" className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {roles.map((option) => (
                                    <SelectItem
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.role} />
                    </div>

                    {role === 'empleado' && (
                        <div className="grid gap-2">
                            <Label htmlFor="area_id">Área</Label>
                            <Select
                                name="area_id"
                                defaultValue={defaults?.area_id ?? undefined}
                            >
                                <SelectTrigger id="area_id" className="w-full">
                                    <SelectValue placeholder="Elige un Área…" />
                                </SelectTrigger>
                                <SelectContent>
                                    {areas.map((area) => (
                                        <SelectItem
                                            key={area.id}
                                            value={area.id}
                                        >
                                            {area.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.area_id} />
                        </div>
                    )}

                    {role === 'revisor' && (
                        <fieldset className="grid gap-3">
                            <legend className="mb-2 text-sm font-medium">
                                Áreas que revisa
                            </legend>
                            {areas.map((area) => (
                                <div
                                    key={area.id}
                                    className="flex items-center gap-3"
                                >
                                    <Checkbox
                                        id={`reviewed-${area.id}`}
                                        name="reviewed_area_ids[]"
                                        value={area.id}
                                        defaultChecked={defaults?.reviewed_area_ids.includes(
                                            area.id,
                                        )}
                                    />
                                    <Label htmlFor={`reviewed-${area.id}`}>
                                        {area.name}
                                    </Label>
                                </div>
                            ))}
                            <InputError
                                message={
                                    errors.reviewed_area_ids ??
                                    errors['reviewed_area_ids.0']
                                }
                            />
                        </fieldset>
                    )}

                    {areas.length === 0 && role !== 'administrador' && (
                        <p className="text-sm text-muted-foreground">
                            Todavía no hay Áreas. Crea al menos una en
                            Configuración › Áreas.
                        </p>
                    )}

                    <Button type="submit" disabled={processing}>
                        {processing && <Spinner />}
                        {submitLabel}
                    </Button>
                </>
            )}
        </Form>
    );
}
