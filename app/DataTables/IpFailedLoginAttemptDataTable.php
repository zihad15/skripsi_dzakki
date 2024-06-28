<?php

namespace App\DataTables;

use App\Models\IpFailedLoginAttempt;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Log;

class IpFailedLoginAttemptDataTable extends DataTable
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
        Log::info('datatable');
        return datatables()->eloquent($query)
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('Y-m-d H:i:s'); // Custom format
            })
            ->toJson();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(IpFailedLoginAttempt $model): QueryBuilder
    {
        Log::info('query');
        return $model->newQuery()
            ->where('failed_attempt', '>', 3);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        Log::info('html');
        return $this->builder()
                    ->setTableId('ipfailedloginattempt-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->ajax([
                        'url' => route($this->routeName), // Dynamically set the AJAX URL
                        'type' => 'GET',
                    ])
                    //->dom('Bfrtip')
                    ->orderBy(3, 'desc')
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
        Log::info('getcolumns');
        return [
            Column::make('input_username'),
            Column::make('ip'),
            Column::make('failed_attempt'),
            Column::make('created_at'),
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(60)
            //       ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'IpFailedLoginAttempt_' . date('YmdHis');
    }
}
