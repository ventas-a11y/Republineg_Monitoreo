import { useTable } from '@tanstack/react-table';
import type { ColumnDef, RowData, SortingState } from '@tanstack/react-table';
import { ChevronLeft, ChevronRight, Search } from 'lucide-react';
import { useState } from 'react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { features } from './features';
import type { DataTableFeatures } from './features';

type Props<TData extends RowData> = {
    columns: ColumnDef<DataTableFeatures, TData>[];
    data: TData[];
    /** Placeholder of the global search box. */
    searchPlaceholder: string;
    /** Shown when there are no rows at all (before any search). */
    empty: ReactNode;
    pageSize?: number;
};

/**
 * shadcn Data Table (TanStack Table v9): global search, sorting by column
 * header and pagination. Columns are built with createColumnHelper.
 */
export function DataTable<TData extends RowData>({
    columns,
    data,
    searchPlaceholder,
    empty,
    pageSize = 20,
}: Props<TData>) {
    const [sorting, setSorting] = useState<SortingState>([]);
    const [globalFilter, setGlobalFilter] = useState('');
    const [pagination, setPagination] = useState({ pageIndex: 0, pageSize });

    const table = useTable({
        features,
        data,
        columns,
        globalFilterFn: 'includesString',
        onSortingChange: setSorting,
        onGlobalFilterChange: setGlobalFilter,
        onPaginationChange: setPagination,
        state: { sorting, globalFilter, pagination },
    });

    if (data.length === 0) {
        return <>{empty}</>;
    }

    const rows = table.getRowModel().rows;
    const filteredCount = table.getFilteredRowModel().rows.length;

    return (
        <div className="space-y-3">
            <div className="relative max-w-sm">
                <Search
                    aria-hidden="true"
                    className="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    type="search"
                    value={globalFilter}
                    onChange={(event) => {
                        setGlobalFilter(event.target.value);
                        table.setPageIndex(0);
                    }}
                    placeholder={searchPlaceholder}
                    aria-label={searchPlaceholder}
                    autoComplete="off"
                    spellCheck={false}
                    className="pl-8"
                />
            </div>

            <div className="rounded-lg border">
                <Table>
                    <TableHeader className="bg-muted/50">
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => (
                                    <TableHead key={header.id} className="px-4">
                                        {header.isPlaceholder ? null : (
                                            <table.FlexRender header={header} />
                                        )}
                                    </TableHead>
                                ))}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {rows.length > 0 ? (
                            rows.map((row) => (
                                <TableRow key={row.id}>
                                    {row.getAllCells().map((cell) => (
                                        <TableCell
                                            key={cell.id}
                                            className="px-4 py-3"
                                        >
                                            <table.FlexRender cell={cell} />
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center text-muted-foreground"
                                >
                                    Ningún resultado coincide con la búsqueda.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>

            <div className="flex flex-wrap items-center justify-between gap-3 text-sm text-muted-foreground">
                <p aria-live="polite" className="tabular-nums">
                    {filteredCount} de {data.length}
                </p>
                {table.getPageCount() > 1 && (
                    <div className="flex items-center gap-2">
                        <span className="tabular-nums">
                            Página {pagination.pageIndex + 1} de{' '}
                            {table.getPageCount()}
                        </span>
                        <Button
                            variant="outline"
                            size="icon"
                            className="size-8"
                            onClick={() => table.previousPage()}
                            disabled={!table.getCanPreviousPage()}
                            aria-label="Página anterior"
                        >
                            <ChevronLeft aria-hidden="true" />
                        </Button>
                        <Button
                            variant="outline"
                            size="icon"
                            className="size-8"
                            onClick={() => table.nextPage()}
                            disabled={!table.getCanNextPage()}
                            aria-label="Página siguiente"
                        >
                            <ChevronRight aria-hidden="true" />
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
}
