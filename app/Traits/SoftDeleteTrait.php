<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait SoftDeleteTrait
{

    public function delete()
    {
        $this->saveHistory();
        return parent::delete();
    }

    protected function saveHistory()
    {
        $tableHistory = $this->table . '_deleted';
        $primary = $this->table . '_id';
        $columns = Schema::getColumnListing($this->table);
        array_push($columns, $primary);

        try {
            DB::beginTransaction();
            if (!Schema::hasTable($tableHistory)) {
                // Create table dynamic
                $this->createTable($tableHistory, $columns);
                Log::debug('create ' . $tableHistory);

            } elseif (!Schema::hasColumns($tableHistory, $columns)) {
                // update table dynamic
                $this->updateTable($tableHistory, $columns);
                Log::debug('update ' . $tableHistory);
            }

            //insert table
            Log::debug('insert ' . $tableHistory);
            $columnsHistory = array_keys(Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($tableHistory));
            $columnsData = Arr::only($this->getAttributes(), $columnsHistory);
            if ($this->getKeyName()) {
                $columnsData[$primary] = $columnsData[$this->getKeyName()];
                unset($columnsData[$this->getKeyName()]);
            }
            if ($this->getCreatedAtColumn()) {
                $columnsData[$this->getCreatedAtColumn()] = Carbon::now()->toDateTimeString();
            }
            if ($this->getUpdatedAtColumn()) {
                $columnsData[$this->getUpdatedAtColumn()] = Carbon::now()->toDateTimeString();
            }

            DB::table($tableHistory)->insert($columnsData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
        }
    }

    protected function createTable($table, $columns)
    {
        Schema::create($table, function (Blueprint $table) use ($columns) {
            $table->integer('id', true);
            $this->getColumnsType($table, $columns);
        });
    }

    protected function getColumnsType(Blueprint $table, $columns, $create = true)
    {
        $primary = $this->table . '_id';
        $columns = array_map(function ($item) {
            return mb_strtolower($item);
        }, $columns);

        $columns = Arr::only(Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($this->table),
            $columns);
        foreach ($columns as $key => $column) {
            if ($key == $this->getKeyName()) {
                $table->integer($primary);
            } else {
                $type = $column->getType()->getName();
                $table->{$type}($column->getName())
                    ->nullable()
                    ->comment($column->getComment())
                    ->length($column->getLength());
            }
        }
        return $table;
    }

    protected function updateTable($table, $columns)
    {
        $columnsNotExists = array_diff($columns,
            array_keys(Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($table)));
        Schema::table($table, function (Blueprint $table) use ($columnsNotExists) {
            $this->getColumnsType($table, $columnsNotExists, false);
        });
    }

}
