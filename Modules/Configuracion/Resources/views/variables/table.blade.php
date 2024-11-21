<div class="table-responsive">
    <table-component
        id="variables-table"
        :data="advancedSearchFilterPaginate()"
        sort-by="variables"
        sort-order="asc"
        table-class="table table-hover m-b-0"
        :show-filter="false"
        :pagination="dataPaginator"
        :show-caption="false"
        filter-placeholder="@lang('Quick filter')"
        filter-no-results="@lang('There are no matching rows')"
        filter-input-class="form-control col-md-4"
        :cache-lifetime="0"
        >
        <table-column show="name" label="@lang('Name')"></table-column>
            <table-column show="value" label="Valor"></table-column>
            <table-column show="type" label="@lang('Type')"></table-column>
            <table-column show="description" label="@lang('Description')"></table-column>
        <table-column label="@lang('crud.action')" :sortable="false" :filterable="false">
            <template slot-scope="row">

                <button @click="edit(row)" data-backdrop="static" data-target="#modal-form-variables" data-toggle="modal" class="btn btn-white btn-icon btn-md" data-toggle="tooltip" data-placement="top" title="@lang('crud.edit')">
                    <i class="fas fa-pencil-alt"></i>
                </button>

                <button @click="show(row)" data-target="#modal-view-variables" data-toggle="modal" class="btn btn-white btn-icon btn-md" data-toggle="tooltip" data-placement="top" title="@lang('see_details')">
                    <i class="fa fa-search"></i>
                </button>

    
                
            </template>
        </table-column>
    </table-component>
</div>