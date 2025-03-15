jQuery(document).ready(function($) {
  $('#datatableResdb').DataTable({
          "columns": [
            { targets: 0, visible: false, searchable: false, className: 'never' },
            { targets: 1, orderData: 0 },
            null
          ],
          "order": [[0, 'asc']],
          "pageLength": 5,
          "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
          "responsive": true,
          "paging":   true,
          "ordering": true,
          "searching": true,
          "info":     true
        });
});