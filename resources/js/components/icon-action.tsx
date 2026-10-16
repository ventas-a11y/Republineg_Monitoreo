import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

/**
 * Icon-only row action with a tooltip. The label is both the tooltip text
 * and the accessible name, so keyboard and screen-reader users get the same
 * information as mouse users. Navigation uses a Link; actions use onClick.
 */
export function IconAction({
    label,
    icon: Icon,
    href,
    onClick,
}: {
    label: string;
    icon: LucideIcon;
    href?: NonNullable<InertiaLinkProps['href']>;
    onClick?: () => void;
}) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    className="size-8"
                    asChild={href !== undefined}
                    onClick={href === undefined ? onClick : undefined}
                    aria-label={label}
                >
                    {href !== undefined ? (
                        <Link href={href} prefetch>
                            <Icon aria-hidden="true" />
                        </Link>
                    ) : (
                        <Icon aria-hidden="true" />
                    )}
                </Button>
            </TooltipTrigger>
            <TooltipContent>{label}</TooltipContent>
        </Tooltip>
    );
}
