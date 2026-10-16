import type { Column, RowData } from '@tanstack/react-table';
import { ArrowDown, ArrowUp, ChevronsUpDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { DataTableFeatures } from './features';

/**
 * Sortable header: a ghost button that cycles ascending/descending, with the
 * current direction announced to screen readers.
 */
export function DataTableColumnHeader<TData extends RowData, TValue>({
    column,
    title,
    className,
}: {
    column: Column<DataTableFeatures, TData, TValue>;
    title: string;
    className?: string;
}) {
    if (!column.getCanSort()) {
        return <span className={className}>{title}</span>;
    }

    const sorted = column.getIsSorted();
    const Icon =
        sorted === 'asc'
            ? ArrowUp
            : sorted === 'desc'
              ? ArrowDown
              : ChevronsUpDown;

    return (
        <Button
            variant="ghost"
            size="sm"
            className={cn('-ml-3 h-8 data-[state=open]:bg-accent', className)}
            onClick={() => column.toggleSorting(sorted === 'asc')}
            aria-label={
                sorted === 'asc'
                    ? `${title}: orden ascendente. Cambiar a descendente`
                    : `${title}: ordenar ascendente`
            }
        >
            {title}
            <Icon aria-hidden="true" className="size-3.5" />
        </Button>
    );
}
