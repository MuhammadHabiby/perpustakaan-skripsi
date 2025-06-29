<script>
    $(document).ready(function() {
        var tableId = '#{{ $table_id ?? 'dataTable' }}';
        var table = $(tableId);
        var defaultOrder = {!! isset($order_option) ? json_encode($order_option) : '[[0, "asc"]]' !!};

        if (table.length) {
            var tableBody = table.find('tbody');
            var hasDataRows = tableBody.find('tr').length > 0 && tableBody.find('tr:first td[colspan]')
                .length === 0;

            if (hasDataRows) {
                table.DataTable({
                    paging: true,
                    searching: true,
                    lengthChange: true,
                    info: true,
                    responsive: true,
                    order: defaultOrder,
                    language: {
                        url: '{{ asset('assets/admin/vendor/id.json') }}',
                        emptyTable: "Tidak ada data yang tersedia di tabel ini"
                    },
                    columnDefs: [{
                        targets: 'no-sort',
                        orderable: false,
                        searchable: false
                    }, {
                        targets: 'text-center',
                        className: 'text-center'
                    }, {
                        targets: 'action-column',
                        className: 'action-column',
                        orderable: false,
                        searchable: false
                    }]

                });
            } else {
                console.log('DataTables tidak diinisialisasi untuk ' + tableId + ' karena tabel kosong.');
            }
        } else {
            console.warn('Elemen tabel dengan ID "' + tableId + '" tidak ditemukan.');
        }
    });
</script>
