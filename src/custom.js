jQuery(document).ready(function($) {
  $('#datatableResdb').DataTable({
          "columns": [
            { targets: 0, visible: false, searchable: false, className: 'never' },
            { targets: 1, orderData: 0 },
            null
          ],
          "order": [[0, 'asc']],
          "pageLength": 5,
          "responsive": true,
          "paging":   true,
          "ordering": true,
          "searching": true,
          "info":     true
        });
});