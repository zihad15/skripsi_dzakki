<?php

namespace App\DataTables;

use App\Models\IpFailedLoginAttemptList;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class IpFailedLoginAttemptListDataTable extends DataTable
{
    protected $routeName;

    public function __construct($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'ipfailedloginattemptlist.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(IpFailedLoginAttemptList $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('ipfailedloginattemptlist-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->ajax([
                        'url' => route($this->routeName), // Dynamically set the AJAX URL
                        'type' => 'GET',
                    ])
                    //->dom('Bfrtip')
                    ->orderBy([[2, 'asc'], [3, 'asc']])
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('ip'),
            Column::make('status'),
            Column::make('failed_attempt')->title('Total Gagal Login'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'IpFailedLoginAttemptList_' . date('YmdHis');
    }
}
