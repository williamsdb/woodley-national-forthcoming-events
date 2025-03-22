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

  var table = $('#datatableResdb').DataTable();

  table.on('page.dt', function() {
          $('html, body').animate({
              scrollTop: $('#datatableResdb').offset().top
          }, 300); // Adjust speed as needed
  });

});