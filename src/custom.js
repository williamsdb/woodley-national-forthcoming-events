jQuery(document).ready(function ($) {
  // Initialize DataTable with responsive design
  $("#datatableResdb").DataTable({
    columns: [
      { targets: 0, visible: false, searchable: false, className: "never" },
      { targets: 1, orderData: 0 },
      null,
    ],
    order: [[0, "asc"]],
    pageLength: 5,
    lengthMenu: [
      [5, 10, 25, 50, -1],
      [5, 10, 25, 50, "All"],
    ],
    responsive: true,
    paging: true,
    ordering: true,
    searching: true,
    info: true,
  });

  var table = $("#datatableResdb").DataTable();

  // Add click event to toggle the calendar menu
  table.on("page.dt", function () {
    $("html, body").animate(
      {
        scrollTop: $("#datatableResdb").offset().top,
      },
      300
    ); // Adjust speed as needed
  });

  // Toggle calendar menu on click
  document.addEventListener("click", function (e) {
    var containers = document.querySelectorAll(".add-to-calendar-container");
    containers.forEach(function (container) {
      if (!container.contains(e.target)) {
        var menu = container.querySelector(".add-to-calendar-menu");
        if (menu) menu.style.display = "none";
      }
    });
  });
});

// Function to handle generate and download .ics file
function addToCalendar(title, description, date) {
  // Convert date string like "31st July 2025 2pm" to "20250731T140000Z"
  function parseDateToICS(dateStr) {
    // Remove ordinal suffixes (st, nd, rd, th)
    dateStr = dateStr.replace(/(\d+)(st|nd|rd|th)/, "$1");
    // Parse using Date object
    const months = {
      January: 0,
      February: 1,
      March: 2,
      April: 3,
      May: 4,
      June: 5,
      July: 6,
      August: 7,
      September: 8,
      October: 9,
      November: 10,
      December: 11,
    };
    const regex = /(\d+)\s+([A-Za-z]+)\s+(\d{4})\s+(\d{1,2})(am|pm)/i;
    const match = dateStr.match(regex);
    if (!match) return "";
    let [_, day, month, year, hour, meridian] = match;
    day = parseInt(day, 10);
    month = months[month];
    year = parseInt(year, 10);
    hour = parseInt(hour, 10);
    if (meridian.toLowerCase() === "pm" && hour < 12) hour += 12;
    if (meridian.toLowerCase() === "am" && hour === 12) hour = 0;
    // Create UTC date
    const dateObj = new Date(Date.UTC(year, month, day, hour, 0, 0));
    // Format as YYYYMMDDTHHMMSSZ
    const pad = (n) => n.toString().padStart(2, "0");
    return (
      dateObj.getUTCFullYear().toString() +
      pad(dateObj.getUTCMonth() + 1) +
      pad(dateObj.getUTCDate()) +
      "T" +
      pad(dateObj.getUTCHours()) +
      pad(dateObj.getUTCMinutes()) +
      pad(dateObj.getUTCSeconds())
    );
  }

  var fdate = parseDateToICS(date);

  // Add one hour to the start date for DTEND
  var endDateObj = new Date(
    Date.UTC(
      parseInt(fdate.slice(0, 4), 10), // year
      parseInt(fdate.slice(4, 6), 10) - 1, // month (0-based)
      parseInt(fdate.slice(6, 8), 10), // day
      parseInt(fdate.slice(9, 11), 10) + 1, // hour + 1
      parseInt(fdate.slice(11, 13), 10), // minute
      parseInt(fdate.slice(13, 15), 10) // second
    )
  );
  const pad = (n) => n.toString().padStart(2, "0");
  var fdateEnd =
    endDateObj.getUTCFullYear().toString() +
    pad(endDateObj.getUTCMonth() + 1) +
    pad(endDateObj.getUTCDate()) +
    "T" +
    pad(endDateObj.getUTCHours()) +
    pad(endDateObj.getUTCMinutes()) +
    pad(endDateObj.getUTCSeconds());

  const icsContent = [
    "BEGIN:VCALENDAR",
    "VERSION:2.0",
    "BEGIN:VEVENT",
    "DTSTART:" + fdate,
    "DTEND:" + fdateEnd,
    "SUMMARY:" + title,
    "DESCRIPTION:" + description,
    "LOCATION:Online",
    "END:VEVENT",
    "END:VCALENDAR",
  ].join("\n");

  const blob = new Blob([icsContent], { type: "text/calendar" });
  const url = URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.href = url;
  a.download = "event.ics";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
