import { Link, usePage } from '@inertiajs/react';
import {
    Activity,
    Bell,
    Building2,
    Camera,
    FileText,
    LayoutGrid,
    Monitor,
    ShieldCheck,
    Users,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { apps, timeline } from '@/routes/activity';
import { index as adminAreas } from '@/routes/admin/areas';
import { index as adminUsers } from '@/routes/admin/users';
import { index as alerts } from '@/routes/alerts';
import { index as captures } from '@/routes/captures';
import { index as devices } from '@/routes/devices';
import { index as reports } from '@/routes/reports';
import type { NavItem } from '@/types';

/**
 * Reminder shown to everyone, as in the mockup: monitoring is transparent.
 */
function TransparencyNotice() {
    return (
        <Card className="gap-1 py-3 shadow-none group-data-[collapsible=icon]:hidden">
            <CardHeader className="px-3">
                <CardTitle className="flex items-center gap-1.5 text-xs">
                    <ShieldCheck
                        aria-hidden="true"
                        className="size-3.5 text-success-fg"
                    />
                    Monitoreo transparente
                </CardTitle>
                <CardDescription className="text-xs leading-relaxed">
                    Cada persona ve su propia actividad y recibe un aviso antes
                    de cada Captura.
                </CardDescription>
            </CardHeader>
        </Card>
    );
}

export function AppSidebar() {
    const { auth } = usePage().props;

    const monitoringNavItems: NavItem[] = [
        { title: 'Resumen', href: dashboard(), icon: LayoutGrid },
        ...(auth.can.viewMonitoring
            ? [
                  { title: 'Equipos', href: devices(), icon: Monitor },
                  {
                      title: 'Actividad',
                      href: timeline(),
                      icon: Activity,
                      items: [
                          { title: 'Línea de tiempo', href: timeline() },
                          { title: 'Apps y sitios', href: apps() },
                      ],
                  },
                  { title: 'Capturas', href: captures(), icon: Camera },
                  { title: 'Alertas', href: alerts(), icon: Bell },
                  { title: 'Reportes', href: reports(), icon: FileText },
              ]
            : []),
    ];

    const configurationNavItems: NavItem[] = [
        ...(auth.can.manageAccounts
            ? [{ title: 'Cuentas', href: adminUsers(), icon: Users }]
            : []),
        ...(auth.can.manageAreas
            ? [{ title: 'Áreas', href: adminAreas(), icon: Building2 }]
            : []),
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={monitoringNavItems} label="Monitoreo" />
                {configurationNavItems.length > 0 && (
                    <NavMain
                        items={configurationNavItems}
                        label="Configuración"
                    />
                )}
            </SidebarContent>

            <SidebarFooter>
                <TransparencyNotice />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
