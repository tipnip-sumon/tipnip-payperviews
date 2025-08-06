(function () {
    "use strict"

    /* calendar */
    var calendarEl = document.getElementById('calendar1');
  
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev',
        center: 'title',
        right: 'next'
      },
      defaultView: 'month',
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
      selectMirror: true,
      droppable: true, // this allows things to be dropped onto the calendar
  
      select: function (arg) {
        var title = prompt('Event Title:');
        if (title) {
          calendar.addEvent({
            title: title,
            start: arg.start,
            end: arg.end,
            allDay: arg.allDay
          })
        }
        calendar.unselect()
      },
    //   eventClick: function (arg) {
    //     if (confirm('Are you sure you want to delete this event?')) {
    //       arg.event.remove()
    //     }
    //   },
  
      editable: true,
      dayMaxEvents: true, // allow "more" link when too many events
    //   eventSources: [sptCalendarEvents, sptBirthdayEvents, sptHolidayEvents, sptOtherEvents,],
  
    });
    calendar.render();
    
})();