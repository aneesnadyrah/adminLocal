"use strict";

// Class definition
let direction = "ltr";

// set moment locale to ms
moment.locale('ms');

// Get the current URL
let currentURL = window.location.href;

// Get the values of the parameters
let urlParams = currentURL.replace(apps + "/calendar", "").split("/");
// urlParams[0] is ignore as it handle the first / in the trimmed url
let sid = urlParams[1];
let rt = urlParams[2];
let systemId = sid;
let reportType = rt;

function waitForFunctionToExist(functionName, timeLimit, interval) {
  return new Promise((resolve, reject) => {
    const checkInterval = setInterval(() => {
      if (window[functionName]) {
        clearInterval(checkInterval);
        resolve(window[functionName]);
      } else {
        timeLimit -= interval;
        if (timeLimit <= 0) {
          clearInterval(checkInterval);
          reject(
            new Error(
              `Function ${functionName} did not exist within ${timeLimit} ms`
            )
          );
        }
      }
    }, interval);
  });
}

function calendarSetup(categoriesData, usersData, eventsData, currentUser) {
  // function to setup categories for filter and selection
  function categoryInitialSetup(categoriesData) {
    // Event filter element setup
    const filterElSetup = document.querySelector(".app-calendar-events-filter");
    if (filterElSetup) {
      // Render filter options passed
      categoriesData.forEach((element) => {
        const filterTemplate = `<div class="form-check form-check-${element.color_class} mb-2">
              <input
              class="form-check-input input-filter"
              type="checkbox"
              id="select-groupId-${element.id}"
              data-value="groupId-${element.id}"
              checked
              />
              <label class="form-check-label" for="select-personal">${element.category_name}</label>
          </div>`;
        filterElSetup.innerHTML += filterTemplate;
      });
    } else {
      // code to execute if filterElSetup does not exist
      console.log("filterElSetup does not exist");
    }
    // Event category selection element setup
    const eventCategorySetup = document.querySelector(
      '[name="calendar_group_id"]'
    );
    if (eventCategorySetup) {
      // Render category options passed
      eventCategorySetup.innerHTML = `<option></option>`;
      categoriesData.forEach((element) => {
        const eventCategoryTemplate = `<option data-label="${element.color_class}" value="${element.id}">${element.category_name}</option>`;
        eventCategorySetup.innerHTML += eventCategoryTemplate;
      });
    } else {
      // code to execute if eventCategorySetup does not exist
      console.log("eventCategorySetup does not exist");
    }
  }

  // function to setup categories for filter and selection
  function guestSelectListing(userList) {
    // Guest option element setup
    const eventGuestSetup = document.querySelector(
      '[name="calendar_event_guest"]'
    );
    if (eventGuestSetup) {
      // Render filter options passed
      eventGuestSetup.innerHTML = ``;
      userList.forEach((element) => {
        const eventGuestTemplate = `<option value="${element.id}">${element.username}</option>`;
        eventGuestSetup.innerHTML += eventGuestTemplate;
      });
    } else {
      // code to execute if filterElSetup does not exist
      console.log("eventGuestSetup does not exist");
    }
  }

  // set filter and category selection
  categoryInitialSetup(categoriesData);

  // set filter and category selection
  guestSelectListing(usersData);

  // funcition to retrieve category name by id
  function getCategoryNameById(categories, id) {
    // search for object with matching id
    const category = categories.find((c) => c.id === id);
    if (category) {
      // if object is found, return category_name
      return category.category_name;
    } else {
      // if object is not found, return null or an error message
      return null;
    }
  }

  // funcition to retrieve category color by id
  function getCategoryColorById(categories, id) {
    // search for object with matching id
    const category = categories.find((c) => c.id === id);
    if (category) {
      // if object is found, return category_class_color
      return category.color_class;
    } else {
      // if object is not found, return null or an error message
      return null;
    }
  }

  // begin::calendar setup
  // Define variables
  // calendar element
  const calendarEl = document.getElementById("kt_calendar_app");
  // left sidebar elemet (flatpickr calendar and filter)
  const appCalendarSidebar = document.querySelector(".app-calendar-sidebar");
  const appOverlay = document.querySelector(".app-overlay");
  const calendarsColor = categoriesData.reduce((acc, category) => {
    acc[category.id] = category.color_class;
    return acc;
  }, {});
  // add event offcanvas
  KTDrawer.createInstances();
  var ocEl = document.getElementById("kt_drawer_add_event");
  var oc = KTDrawer.getInstance(ocEl);
  // add event modal
  const addElement = document.getElementById("kt_drawer_add_event");
  var form = addElement.querySelector("#kt_modal_add_event_form");
  var eventName = form.querySelector('[name="calendar_event_name"]');
  // var eventCategory = form.querySelector('[name="calendar_group_id"]');
  // var eventGuest = form.querySelector('[name="calendar_event_guest"]');
  var eventCategory = $("#calendar_group_id"); // use jquery because it dependencies
  var eventGuest = $("#calendar_event_guest"); // use jquery because it dependencies
  var eventDescription = form.querySelector(
    '[name="calendar_event_description"]'
  );
  var eventLocation = form.querySelector('[name="calendar_event_location"]');
  var startDatepicker = form.querySelector(
    "#kt_calendar_datepicker_start_date"
  );
  var endDatepicker = form.querySelector("#kt_calendar_datepicker_end_date");
  var startTimepicker = form.querySelector(
    "#kt_calendar_datepicker_start_time"
  );
  var endTimepicker = form.querySelector("#kt_calendar_datepicker_end_time");
  const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
  var addButton = document.querySelector('[data-kt-calendar="add"]');
  var submitButton = form.querySelector("#kt_modal_add_event_submit");
  var cancelButton = form.querySelector("#kt_modal_add_event_cancel");
  var closeButton = addElement.querySelector("#kt_modal_add_event_close");
  var modalTitle = form.querySelector('[data-kt-calendar="title"]');

  // initialize modal
  // var modal = new bootstrap.Modal(addElement);
  var modal = oc;

  // View event modal
  const viewElement = document.getElementById("kt_modal_view_event");
  var viewModal = new bootstrap.Modal(viewElement);
  var viewEventName = viewElement.querySelector(
    '[data-kt-calendar="event_name"]'
  );
  var viewGroup = viewElement.querySelector(
    '[data-kt-calendar="event_view_group"]'
  );
  var viewAllDay = viewElement.querySelector('[data-kt-calendar="all_day"]');
  var viewEventDescription = viewElement.querySelector(
    '[data-kt-calendar="event_description"]'
  );
  var viewEventLocation = viewElement.querySelector(
    '[data-kt-calendar="event_location"]'
  );
  var viewStartDate = viewElement.querySelector(
    '[data-kt-calendar="event_start_date"]'
  );
  var viewEndDate = viewElement.querySelector(
    '[data-kt-calendar="event_end_date"]'
  );
  var viewEditButton = viewElement.querySelector("#kt_modal_view_event_edit");
  var viewDeleteButton = viewElement.querySelector(
    "#kt_modal_view_event_delete"
  );

  // filter events and flatpickr inline calendar
  const selectAll = document.querySelector(".select-all");
  const filterInput = [].slice.call(document.querySelectorAll(".input-filter"));
  const inlineCalendar = document.querySelector(".inline-calendar");

  // event data variables
  let eventToUpdate;
  let eventToView;
  let currentEvents = eventsData; // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events
  let isFormValid = false;
  let inlineCalInstance;
  let eventToAdd = {
    id: "",
    title: "",
    groupId: "",
    allDay: false,
    startStr: "",
    endStr: "",
    url: "",
    extendedProps: {
      creator: "",
      guests: "",
      description: "",
      location: "",
    },
  };

  // TODO: Add color dot on calendar categories selection
  // TODO: Decide how to generate guest list design

  // Inline sidebar calendar (flatpicker)
  if (inlineCalendar) {
    inlineCalInstance = inlineCalendar.flatpickr({
      monthSelectorType: "static",
      inline: true,
      locale: "ms",
      onReady: function () {
        // remove shadow
        inlineCalendar.nextElementSibling.classList.add("shadow-none");
      },
    });
  }

  // Initialize datepickers --- more info: https://flatpickr.js.org/
  var startFlatpickr;
  var endFlatpickr;
  var startTimeFlatpickr;
  var endTimeFlatpickr;
  const initDatepickers = () => {
    startFlatpickr = flatpickr(startDatepicker, {
      enableTime: false,
      dateFormat: "Y-m-d",
    });

    endFlatpickr = flatpickr(endDatepicker, {
      enableTime: false,
      dateFormat: "Y-m-d",
    });

    startTimeFlatpickr = flatpickr(startTimepicker, {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });

    endTimeFlatpickr = flatpickr(endTimepicker, {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });
  };

  // Event click function
  var handleViewEvent = (info) => {
    // event to show
    eventToView = info.event;
    eventToUpdate = eventToView;

    // console.log('eventToView',eventToView);

    // show viewModal
    viewModal.show();

    // Detect all day event
    var eventNameMod;
    var startDateMod;
    var endDateMod;

    // Generate labels
    if (eventToView.allDay) {
      eventNameMod = "All Day";
      console.log(eventToView.startStr, eventToView.endStr);
      startDateMod = moment(eventToView.startStr).format("Do MMM, YYYY");
      if (eventToView.endStr != "") {
        endDateMod = moment(eventToView.endStr).format("Do MMM, YYYY");
      } else {
        endDateMod = moment(eventToView.startStr).format("Do MMM, YYYY");
      }
    } else {
      eventNameMod = "";
      startDateMod = moment(eventToView.startStr).format(
        "Do MMM, YYYY - h:mm a"
      );
      endDateMod = moment(eventToView.endStr).format("Do MMM, YYYY - h:mm a");
    }

    // Populate view data
    viewEventName.innerText = eventToView.title;
    viewGroup.className = "";
    viewGroup.classList.add(
      "badge",
      "me-3",
      "badge-light-" + getCategoryColorById(categoriesData, eventToView.groupId)
    );
    viewGroup.innerText = getCategoryNameById(
      categoriesData,
      eventToView.groupId
    );
    viewAllDay.innerText = eventNameMod;
    viewEventDescription.innerText = eventToView.extendedProps.description
      ? eventToView.extendedProps.description
      : "--";
    viewEventLocation.innerText = eventToView.extendedProps.location
      ? eventToView.extendedProps.location
      : "--";
    viewStartDate.innerText = startDateMod;
    viewEndDate.innerText = endDateMod;
  };

  // Handle delete event
  const handleDeleteEvent = () => {
    viewDeleteButton.addEventListener("click", (e) => {
      e.preventDefault();

      Swal.fire({
        text: "Are you sure you would like to delete this event?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, return",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          removeEvent(eventToUpdate.id);

          viewModal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Your event was not deleted!.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Populate form
  const populateForm = (data) => {
    // console.log('populateData',data);
    // assign event title
    eventName.value = data.title ? data.title : "";
    // clear existing selected value
    eventCategory.val(null).trigger("change");
    // assign new selected value
    if (
      data.groupId != null ||
      data.groupId != "" ||
      data.groupId != undefined
    ) {
      eventCategory.val(data.groupId).trigger("change");
    } else {
      console.log("data.groupId value error");
    }
    // clear existing selected value
    eventGuest.val(null).trigger("change");
    // Check if the event contains guest data or not
    if (data.extendedProps && data.extendedProps.guests) {
      // assign new selected value if not null
      if (
        data.extendedProps.guests != null ||
        data.extendedProps.guests != "" ||
        data.extendedProps.guests != undefined
      ) {
        eventGuest.val(data.extendedProps.guests.split(",")).trigger("change");
      } else {
        console.log("data.extendedProps.guests value error");
      }
    } else {
      console.log("event do not have any guest data passed");
    }
    // assign event description
    eventDescription.value = data.extendedProps.description
      ? data.extendedProps.description
      : "";
    // assign event location
    eventLocation.value = data.extendedProps.location
      ? data.extendedProps.location
      : "";
    // console.log(data.startStr);
    if (data.startStr != "") {
      startFlatpickr.setDate(data.startStr, true, "Y-m-d");

      // Handle null end dates
      const endDate = data.endStr
        ? data.endStr
        : moment(data.startStr).format();
      endFlatpickr.setDate(endDate, true, "Y-m-d");
    }

    const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
    const datepickerWrappers = form.querySelectorAll(
      '[data-kt-calendar="datepicker"]'
    );
    if (data.allDay) {
      allDayToggle.checked = true;
      datepickerWrappers.forEach((dw) => {
        dw.classList.add("d-none");
      });
    } else {
      if (data.startStr != "" || data.endStr != "" || data.startStr != "") {
        startTimeFlatpickr.setDate(data.startStr, true, "Y-m-d H:i");
        endTimeFlatpickr.setDate(data.endStr, true, "Y-m-d H:i");
        endFlatpickr.setDate(data.startStr, true, "Y-m-d");
      }
      allDayToggle.checked = false;
      datepickerWrappers.forEach((dw) => {
        dw.classList.remove("d-none");
      });
    }
  };

  // Handle edit events
  var handleEditEvent = () => {
    // Update modal title
    modalTitle.innerText = "Edit an Event";
    // change Submit button
    submitButton.innerHTML = "Update";
    submitButton.classList.add("btn-update-event");
    submitButton.classList.remove("btn-add-event");

    modal.show();

    // Select datepicker wrapper elements
    const datepickerWrappers = form.querySelectorAll(
      '[data-kt-calendar="datepicker"]'
    );

    // Handle all day toggle
    const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
    allDayToggle.addEventListener("click", (e) => {
      if (e.target.checked) {
        datepickerWrappers.forEach((dw) => {
          dw.classList.add("d-none");
        });
      } else {
        endFlatpickr.setDate(eventToUpdate.startDate, true, "Y-m-d");
        datepickerWrappers.forEach((dw) => {
          dw.classList.remove("d-none");
        });
      }
    });

    populateForm(eventToUpdate);
  };

  // Handle edit button
  var handleEditButton = () => {
    viewEditButton.addEventListener("click", (e) => {
      e.preventDefault();

      viewModal.hide();
      handleEditEvent();
    });
  };

  // Handle cancel button
  const handleCancelButton = () => {
    // Edit event modal cancel button
    cancelButton.addEventListener("click", function (e) {
      e.preventDefault();

      Swal.fire({
        text: "Are you sure you would like to cancel?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, cancel it!",
        cancelButtonText: "No, return",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          form.reset(); // Reset form
          modal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Your form has not been cancelled!.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Handle close button
  const handleCloseButton = () => {
    // Edit event modal close button
    closeButton.addEventListener("click", function (e) {
      e.preventDefault();

      Swal.fire({
        text: "Are you sure you would like to cancel?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Yes, cancel it!",
        cancelButtonText: "No, return",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          form.reset(); // Reset form
          modal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Your form has not been cancelled!.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Modify sidebar toggler
  function modifyToggler() {
    const fcSidebarToggleButton = document.querySelector(
      ".fc-sidebarToggle-button"
    );
    fcSidebarToggleButton.classList.remove("fc-button-primary");
    fcSidebarToggleButton.classList.add("d-lg-none", "d-inline-block", "ps-0");
    while (fcSidebarToggleButton.firstChild) {
      fcSidebarToggleButton.firstChild.remove();
    }
    fcSidebarToggleButton.setAttribute("data-bs-toggle", "sidebar");
    fcSidebarToggleButton.setAttribute("data-overlay", "");
    fcSidebarToggleButton.setAttribute("data-target", "#app-calendar-sidebar");
    fcSidebarToggleButton.insertAdjacentHTML(
      "beforeend",
      '<i class="ti ti-menu-2 ti-sm"></i>'
    );
  }

  // Filter events by calender
  function selectedCalendars() {
    let selected = [],
      filterInputChecked = [].slice.call(
        document.querySelectorAll(".input-filter:checked")
      );
    // console.log('filterInputElement',filterInputChecked); //temporary commented

    filterInputChecked.forEach((item) => {
      selected.push(item.getAttribute("data-value"));
      // console.log(selected); //temporary commented
    });

    return selected;
  }

  // fetch calendar events
  function fetchEvents(info, successCallback) {
    // Do event fetch api to update currentEvents data
    api
      .get("calendar/event")
      .then((response) => {
        // console.log(response.data);
        // assign response data to currentEvents to be view
        currentEvents = response;

        let calendars = selectedCalendars(); // assign selected calendar from function

        // We are reading event object from app-calendar-events.js file directly by including that file above app-calendar file.
        // You should make an API call, look into above commented API call for reference
        let selectedEvents = currentEvents.filter(function (event) {
          return calendars.includes("groupId-" + event.groupId);
        });
        // if (selectedEvents.length > 0) {
        successCallback(selectedEvents);
        // }
      })
      .catch((error) => {
        console.log(error);
      });
  }

  // calendar init variables
  var todayDate = moment().startOf("day"); /** get today date */
  var TODAY = todayDate.format("YYYY-MM-DD");

  // Init calendar --- more info: https://fullcalendar.io/docs/initialize-globals
  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: "ms",
    firstDay: 1,
    initialView: "dayGridMonth",
    events: fetchEvents,
    editable: true,
    dragScroll: true,
    dayMaxEvents: true, // allow "more" link when too many events
    eventResizableFromStart: true,
    customButtons: {
      sidebarToggle: {
        text: "Sidebar",
      },
    },
    headerToolbar: {
      left: "sidebarToggle prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
    },
    direction: direction,
    initialDate: TODAY,
    navLinks: true, // can click day/week names to navigate views
    eventClassNames: function ({ event: calendarEvent }) {
      const colorName = calendarsColor[calendarEvent._def.groupId];
      //   const colorName = calendarsColor[calendarEvent._def.extendedProps.eventCategory];
      // Background Color
      return ["fc-event-" + colorName];
    },
    selectable: true,
    selectMirror: true,
    dateClick: function (info) {
      // console.log('dateClick value',info);
      let clickedDate = moment(info.date).format("YYYY-MM-DD");
      // alert('[fakhri] create new event here, passed clicked date value is '+clickedDate);
      eventToAdd.startStr = clickedDate;
      handleNewEvent();
    },

    // Click event --- more info: https://fullcalendar.io/docs/eventClick
    eventClick: function (info) {
      handleViewEvent(info);
    },

    // Handle changing calendar views --- more info: https://fullcalendar.io/docs/datesSet
    datesSet: function (info) {
      // do some stuff
      modifyToggler();
      // console.log('datesSet',info);
      // alert('[fakhri] datesSet trigger is done, do something here.');
    },
    viewDidMount: function (info) {
      // do some stuff
      modifyToggler();
      // console.log('viewDidMount',info);
      // alert('[fakhri] viewDidMount trigger is done, do something here.');
    },
    eventReceive: function (info) {
      console.log(info.event);
      alert("eventReceive trigger is done, do something here." + info.event);
    },
    eventAdd: function (info) {
      console.log(info.event);
      alert("eventAdd trigger is done, do something here." + info.event);
    },
  });
  // Render calendar
  calendar.render();
  // Modify sidebar toggler
  modifyToggler();

  // Init formvalidation
  const fv = FormValidation.formValidation(form, {
    fields: {
      calendar_event_name: {
        validators: {
          notEmpty: {
            message: "Event name is required",
          },
        },
      },
      calendar_group_id: {
        validators: {
          notEmpty: {
            message: "Event category is required",
          },
        },
      },
      calendar_event_start_date: {
        validators: {
          notEmpty: {
            message: "Start date is required",
          },
        },
      },
      calendar_event_end_date: {
        validators: {
          notEmpty: {
            message: "End date is required",
          },
        },
      },
    },

    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap: new FormValidation.plugins.Bootstrap5({
        rowSelector: ".fv-row",
        eleInvalidClass: "",
        eleValidClass: "",
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      // autoFocus: new FormValidation.plugins.AutoFocus()
    },
  })
    .on("core.form.valid", function () {
      // Jump to the next step when all fields in the current step are valid
      isFormValid = true;
      console.log("validation status", isFormValid);
    })
    .on("core.form.invalid", function () {
      // if fields are invalid
      isFormValid = false;
      console.log("validation status", isFormValid);
    });

  // Handle add button
  const handleAddButton = () => {
    addButton.addEventListener("click", (e) => {
      // Reset form data
      eventToAdd = {
        id: "",
        title: "",
        groupId: "",
        allDay: false,
        startStr: "",
        endStr: "",
        url: "",
        extendedProps: {
          creator: "",
          guests: "",
          description: "",
          location: "",
        },
      };
      handleNewEvent();
    });
  };

  // Handle add new event
  const handleNewEvent = () => {
    // Update modal title
    modalTitle.innerText = "Add a New Event";
    // Change button
    submitButton.innerHTML = "Add";
    submitButton.classList.add("btn-add-event");
    submitButton.classList.remove("btn-update-event");

    modal.show();

    // Select datepicker wrapper elements
    const datepickerWrappers = form.querySelectorAll(
      '[data-kt-calendar="datepicker"]'
    );

    // Handle all day toggle
    const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
    allDayToggle.addEventListener("click", (e) => {
      if (e.target.checked) {
        datepickerWrappers.forEach((dw) => {
          dw.classList.add("d-none");
        });
      } else {
        endFlatpickr.setDate(eventToAdd.startStr, true, "Y-m-d");
        datepickerWrappers.forEach((dw) => {
          dw.classList.remove("d-none");
        });
      }
    });

    populateForm(eventToAdd);
  };

  // Add new event
  // ------------------------------------------------
  const handleSubmitButton = () => {
    submitButton.addEventListener("click", (e) => {
      e.preventDefault();
      if (submitButton.classList.contains("btn-add-event")) {
        if (isFormValid) {
          console.log("form add valid");

          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable submit button whilst loading
          submitButton.disabled = true;

          // Simulate form submission
          setTimeout(function () {
            // Simulate form submission
            submitButton.removeAttribute("data-kt-indicator");

            // Show popup confirmation
            Swal.fire({
              text: "New event added to calendar!",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, got it!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
            }).then(function (result) {
              if (result.isConfirmed) {
                modal.hide();

                // Enable submit button after loading
                submitButton.disabled = false;

                // Detect if is all day event
                let allDayEvent = false;
                if (allDayToggle.checked) {
                  allDayEvent = true;
                }
                if (startTimeFlatpickr.selectedDates.length === 0) {
                  allDayEvent = true;
                }

                // Merge date & time
                var startDateTime = moment(
                  startFlatpickr.selectedDates[0]
                ).format();
                var endDateTime = moment(
                  endFlatpickr.selectedDates[
                  endFlatpickr.selectedDates.length - 1
                  ]
                ).format();
                if (!allDayEvent) {
                  const startDate = moment(
                    startFlatpickr.selectedDates[0]
                  ).format("YYYY-MM-DD");
                  const endDate = startDate;
                  const startTime = moment(
                    startTimeFlatpickr.selectedDates[0]
                  ).format("HH:mm:ss");
                  const endTime = moment(
                    endTimeFlatpickr.selectedDates[0]
                  ).format("HH:mm:ss");

                  startDateTime = startDate + "T" + startTime;
                  endDateTime = endDate + "T" + endTime;
                }

                let newEvent = {
                  id: "",
                  title: eventName.value,
                  groupId: eventCategory.val(),
                  allDay: allDayEvent,
                  start: startDateTime,
                  end: endDateTime,
                  url: "",
                  creator: currentUser,
                  guests: eventGuest.val().join(","),
                  description: eventDescription.value,
                  location: eventLocation.value,
                };

                // Add new event to calendar
                addEvent(newEvent);

                // Reset form for demo purposes only
                form.reset();
              }
            });

            //form.submit(); // Submit form
          }, 2000);
        } else {
          console.log("form add not valid");
          // Show popup warning
          Swal.fire({
            text: "Sorry, looks like there are some errors detected, please try again.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      } else {
        // Update event
        // ------------------------------------------------
        if (isFormValid) {
          console.log("form edit valid");

          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable submit button whilst loading
          submitButton.disabled = true;

          // Simulate form submission
          setTimeout(function () {
            // Simulate form submission
            submitButton.removeAttribute("data-kt-indicator");

            // Show popup confirmation
            Swal.fire({
              text: "New event added to calendar!",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, got it!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
            }).then(function (result) {
              if (result.isConfirmed) {
                modal.hide();

                // Enable submit button after loading
                submitButton.disabled = false;

                // Remove old event
                // calendar.getEventById(data.id).remove();

                // Detect if is all day event
                let allDayEvent = false;
                if (allDayToggle.checked) {
                  allDayEvent = true;
                }
                if (startTimeFlatpickr.selectedDates.length === 0) {
                  allDayEvent = true;
                }

                // Merge date & time
                var startDateTime = moment(
                  startFlatpickr.selectedDates[0]
                ).format();
                var endDateTime = moment(
                  endFlatpickr.selectedDates[
                  endFlatpickr.selectedDates.length - 1
                  ]
                ).format();
                if (!allDayEvent) {
                  const startDate = moment(
                    startFlatpickr.selectedDates[0]
                  ).format("YYYY-MM-DD");
                  const endDate = startDate;
                  const startTime = moment(
                    startTimeFlatpickr.selectedDates[0]
                  ).format("HH:mm:ss");
                  const endTime = moment(
                    endTimeFlatpickr.selectedDates[0]
                  ).format("HH:mm:ss");

                  startDateTime = startDate + "T" + startTime;
                  endDateTime = endDate + "T" + endTime;
                }

                // Update event in calendar

                let eventData = {
                  id: eventToUpdate.id,
                  title: eventName.value,
                  groupId: eventCategory.val(),
                  allDay: allDayEvent,
                  start: startDateTime,
                  end: endDateTime,
                  url: "",
                  creator: currentUser,
                  guests: eventGuest.val().join(","),
                  description: eventDescription.value,
                  location: eventLocation.value,
                };

                updateEvent(eventData);

                // Reset form for demo purposes only
                form.reset();
              }
            });

            //form.submit(); // Submit form
          }, 2000);
        } else {
          console.log("form edit not valid");

          // Show popup warning
          Swal.fire({
            text: "Sorry, looks like there are some errors detected, please try again.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      }
    });
  };

  // Add Event
  // ------------------------------------------------
  function addEvent(eventData) {
    // ? Add new event data to current events object and refetch it to display on calender
    // send new event data to addEvent api
    console.log({ addPayload: eventData });
    api
      .post("calendar", eventData)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To add event directly to calender (won't update currentEvents object)
    // calendar.addEvent(eventData);
  }

  // Update Event
  // ------------------------------------------------
  function updateEvent(eventData) {
    // ? Update existing event data to current events object and refetch it to display on calender
    // send new event data to updateEvent api
    // console.log('eventData to Update',eventData);
    api
      .put("calendar", eventData)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To update event directly to calender (won't update currentEvents object)
    // let propsToUpdate = ['id', 'title', 'url'];
    // let extendedPropsToUpdate = ['calendar', 'guests', 'location', 'description'];

    // updateEventInCalendar(eventData, propsToUpdate, extendedPropsToUpdate);
  }

  // Remove Event
  // ------------------------------------------------

  function removeEvent(eventId) {
    // ? Delete existing event data to current events object and refetch it to display on calender
    // send new event data to deleteEvent api
    // console.log('eventData to Update',eventData);

    api
      .delete("calendar/" + eventId)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To delete event directly to calender (won't update currentEvents object)
    // removeEventInCalendar(eventId);
  }

  // Calender filter functionality
  // ------------------------------------------------
  if (selectAll) {
    selectAll.addEventListener("click", (e) => {
      if (e.currentTarget.checked) {
        document
          .querySelectorAll(".input-filter")
          .forEach((c) => (c.checked = 1));
      } else {
        document
          .querySelectorAll(".input-filter")
          .forEach((c) => (c.checked = 0));
      }
      calendar.refetchEvents();
    });
  }

  if (filterInput) {
    filterInput.forEach((item) => {
      item.addEventListener("click", () => {
        document.querySelectorAll(".input-filter:checked").length <
          document.querySelectorAll(".input-filter").length
          ? (selectAll.checked = false)
          : (selectAll.checked = true);
        calendar.refetchEvents();
      });
    });
  }

  // Jump to date on sidebar(inline) calendar change
  inlineCalInstance.config.onChange.push(function (date) {
    calendar.changeView(
      calendar.view.type,
      moment(date[0]).format("YYYY-MM-DD")
    );
    modifyToggler();
    appCalendarSidebar.classList.remove("show");
    appOverlay.classList.remove("show");
  });

  // init all events
  initDatepickers();
  handleDeleteEvent();
  handleEditButton();
  handleCancelButton();
  handleCloseButton();
  handleAddButton();
  handleSubmitButton();
}

function svCalendarSetup(categoriesData, authorityData, usersData, eventsData, currentUser) {
  // get report type from parameter
  let reportType = rt,
    noRef;

  // get reference no from api// get suggested authority id
  api
    .get("calendar/reference/" + sid)
    .then((response) => {
      /* Assigning the value of the reference number to the variable noRef. */
      // console.log(response);
      if (response.data != "") {
        noRef = response.reference_no;
      } else {
        alert(
          "Maaf terdapat ralat pada list project yang dibuka. Sila hubungi unit teknikal untuk maklumat lanjut. Anda akan dihantar ke paparan sebelum ini."
        );
        window.history.back();
      }
    })
    .catch((error) => {
      console.log(error);
    });

  // function to get user id from username
  function getUserId(username, users) {
    for (let i = 0; i < users.length; i++) {
      if (users[i].username === username) {
        return users[i].id;
      }
    }
    return null;
  }

  // function to setup categories for filter and selection
  function categoryInitialSetup(categoriesData) {
    // Event filter element setup
    const filterElSetup = document.querySelector(".app-calendar-events-filter");
    if (filterElSetup) {
      // Render filter options passed
      categoriesData.forEach((element) => {
        const filterTemplate = `<div class="form-check form-check-${element.color_class} mb-2">
            <input
            class="form-check-input input-filter"
            type="checkbox"
            id="select-groupId-${element.id}"
            data-value="groupId-${element.id}"
            checked
            />
            <label class="form-check-label" for="select-personal">${element.category_name}</label>
        </div>`;
        filterElSetup.innerHTML += filterTemplate;
      });
    } else {
      // code to execute if filterElSetup does not exist
      console.log("filterElSetup does not exist");
    }
    // Event category selection element setup
    const eventCategorySetup = document.querySelector(
      '[name="calendar_group_id"]'
    );
    if (eventCategorySetup) {
      // Render category options passed
      eventCategorySetup.innerHTML = `<option></option>`;
      categoriesData.forEach((element) => {
        const eventCategoryTemplate = `<option data-label="${element.color_class}" value="${element.id}">${element.category_name}</option>`;
        eventCategorySetup.innerHTML += eventCategoryTemplate;
      });
    } else {
      // code to execute if eventCategorySetup does not exist
      console.log("eventCategorySetup does not exist");
    }
  }

  // function to setup categories for filter and selection
  function guestSelectListing(userList) {
    // Guest option element setup
    const eventGuestSetup = document.querySelector(
      '[name="calendar_event_guest"]'
    );
    if (eventGuestSetup) {
      // Render filter options passed
      eventGuestSetup.innerHTML = ``;
      userList.forEach((element) => {
        const eventGuestTemplate = `<option value="${element.id}">${element.username}</option>`;
        eventGuestSetup.innerHTML += eventGuestTemplate;
      });
    } else {
      // code to execute if filterElSetup does not exist
      console.log("eventGuestSetup does not exist");
    }
  }

  // set filter and category selection
  categoryInitialSetup(categoriesData);

  // set filter and category selection
  guestSelectListing(usersData);

  // funcition to retrieve category name by id
  function getCategoryNameById(categories, id) {
    // search for object with matching id
    const category = categories.find((c) => c.id == id);
    if (category) {
      // if object is found, return category_name
      return category.category_name;
    } else {
      // if object is not found, return null or an error message
      return null;
    }
  }

  // funcition to retrieve category color by id
  function getCategoryColorById(categories, id) {
    // search for object with matching id
    const category = categories.find((c) => c.id == id);
    if (category) {
      // if object is found, return category_class_color
      return category.color_class;
    } else {
      // if object is not found, return null or an error message
      return null;
    }
  }

  // begin::calendar setup
  // Define variables
  // calendar element
  const calendarEl = document.getElementById("kt_calendar_app");
  // left sidebar elemet (flatpickr calendar and filter)
  const appCalendarSidebar = document.querySelector(".app-calendar-sidebar");
  const appOverlay = document.querySelector(".app-overlay");
  const calendarsColor = categoriesData.reduce((acc, category) => {
    acc[category.id] = category.color_class;
    return acc;
  }, {});
  // add event offcanvas
  KTDrawer.createInstances();
  var ocEl = document.getElementById("kt_drawer_add_event");
  var oc = KTDrawer.getInstance(ocEl);
  // add event modal
  const addElement = document.getElementById("kt_drawer_add_event");
  var form = addElement.querySelector("#kt_modal_add_event_form");
  var eventName = form.querySelector('[name="calendar_event_name"]');
  // var eventCategory = form.querySelector('[name="calendar_group_id"]');
  // var eventGuest = form.querySelector('[name="calendar_event_guest"]');
  var eventCategory = $("#calendar_group_id"); // use jquery because it dependencies
  var eventGuest = $("#calendar_event_guest"); // use jquery because it dependencies
  var eventDescription = form.querySelector(
    '[name="calendar_event_description"]'
  );
  var eventLocation = form.querySelector('[name="calendar_event_location"]');
  var startDatepicker = form.querySelector(
    "#kt_calendar_datepicker_start_date"
  );
  var endDatepicker = form.querySelector("#kt_calendar_datepicker_end_date");
  var startTimepicker = form.querySelector(
    "#kt_calendar_datepicker_start_time"
  );
  var endTimepicker = form.querySelector("#kt_calendar_datepicker_end_time");
  const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
  var addButton = document.querySelector('[data-kt-calendar="add"]'); // addButton will be used as wizard finish button for sitevisit page
  // addButton textcontent has being changed to "Selesai" at the beginning of this js
  var submitButton = form.querySelector("#kt_modal_add_event_submit");
  var cancelButton = form.querySelector("#kt_modal_add_event_cancel");
  var closeButton = addElement.querySelector("#kt_modal_add_event_close");
  var modalTitle = form.querySelector('[data-kt-calendar="title"]');
  var tempEl = null;
  var categoryDiv = null;
  var guestDiv = null;
  var eventAuthority = null;

  // initialize modal
  // var modal = new bootstrap.Modal(addElement);
  var modal = oc;

  // View event modal
  const viewElement = document.getElementById("kt_modal_view_event");
  var viewModal = new bootstrap.Modal(viewElement);
  var viewEventName = viewElement.querySelector(
    '[data-kt-calendar="event_name"]'
  );
  var viewGroup = viewElement.querySelector(
    '[data-kt-calendar="event_view_group"]'
  );
  var viewAllDay = viewElement.querySelector('[data-kt-calendar="all_day"]');
  var viewEventDescription = viewElement.querySelector(
    '[data-kt-calendar="event_description"]'
  );
  var viewEventLocation = viewElement.querySelector(
    '[data-kt-calendar="event_location"]'
  );
  var viewStartDate = viewElement.querySelector(
    '[data-kt-calendar="event_start_date"]'
  );
  var viewEndDate = viewElement.querySelector(
    '[data-kt-calendar="event_end_date"]'
  );
  var viewEditButton = viewElement.querySelector("#kt_modal_view_event_edit");
  var viewDeleteButton = viewElement.querySelector(
    "#kt_modal_view_event_delete"
  );
  var viewAuthority = viewElement.querySelector('[data-kt-calendar="event_auth"]');
  var viewAuthLogo = viewElement.querySelector('[data-kt-calendar="event_auth_logo"]');
  var viewAuthName = viewElement.querySelector('[data-kt-calendar="event_auth_name"]');

  // filter events and flatpickr inline calendar
  const selectAll = document.querySelector(".select-all");
  const filterInput = [].slice.call(document.querySelectorAll(".input-filter"));
  const inlineCalendar = document.querySelector(".inline-calendar");

  // event data variables
  let eventToUpdate;
  let eventToView;
  let currentEvents = eventsData.filter(
    (event) =>
      event.groupId != 3 || (event.groupId == 3 && event.creator == currentUser)
  ); // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events // remove personal events groupId == 3 where the creator is others
  let isFormValid = false;
  let inlineCalInstance;
  let eventToAdd = {
    id: "",
    title: "",
    groupId: "",
    allDay: false,
    startStr: "",
    endStr: "",
    url: "",
    extendedProps: {
      creator: "",
      guests: "",
      description: "",
      location: "",
    },
  };

  // TODO: Add color dot on calendar categories selection
  // TODO: Decide how to generate guest list design

  // Inline sidebar calendar (flatpicker)
  if (inlineCalendar) {
    inlineCalInstance = inlineCalendar.flatpickr({
      monthSelectorType: "static",
      inline: true,
      locale: "ms",
      onReady: function () {
        // remove shadow
        inlineCalendar.nextElementSibling.classList.add("shadow-none");
      },
    });
  }

  // Initialize datepickers --- more info: https://flatpickr.js.org/
  var startFlatpickr;
  var endFlatpickr;
  var startTimeFlatpickr;
  var endTimeFlatpickr;
  const initDatepickers = () => {
    startFlatpickr = flatpickr(startDatepicker, {
      enableTime: false,
      dateFormat: "Y-m-d",
    });

    endFlatpickr = flatpickr(endDatepicker, {
      enableTime: false,
      dateFormat: "Y-m-d",
    });

    startTimeFlatpickr = flatpickr(startTimepicker, {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });

    endTimeFlatpickr = flatpickr(endTimepicker, {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });
  };

  // Event click function
  var handleViewEvent = (info) => {
    // console.log(info);
    // event to show
    eventToView = info.event;
    eventToUpdate = eventToView;

    // console.log('eventToView',eventToView);

    // show viewModal
    viewModal.show();

    // Detect all day event
    var eventNameMod;
    var startDateMod;
    var endDateMod;

    // Generate labels
    if (eventToView.allDay) {
      eventNameMod = "All Day";
      console.log(eventToView.startStr, eventToView.endStr);
      startDateMod = moment(eventToView.startStr).format("Do MMM, YYYY");
      if (eventToView.endStr != "") {
        endDateMod = moment(eventToView.endStr).format("Do MMM, YYYY");
      } else {
        endDateMod = moment(eventToView.startStr).format("Do MMM, YYYY");
      }
    } else {
      eventNameMod = "";
      startDateMod = moment(eventToView.startStr).format(
        "Do MMM, YYYY - h:mm a"
      );
      endDateMod = moment(eventToView.endStr).format("Do MMM, YYYY - h:mm a");
    }

    // Populate view data
    viewEventName.innerText = eventToView.title;
    viewGroup.className = "";
    viewGroup.classList.add(
      "badge",
      "my-2",
      "me-0",
      "badge-light-" + getCategoryColorById(categoriesData, eventToView.groupId)
    );
    viewGroup.innerText = getCategoryNameById(
      categoriesData,
      eventToView.groupId
    );
    viewAllDay.innerText = eventNameMod;
    viewEventDescription.innerText = eventToView.extendedProps.description
      ? eventToView.extendedProps.description
      : "--";
    viewEventLocation.innerText = eventToView.extendedProps.location
      ? eventToView.extendedProps.location
      : "--";
    viewStartDate.innerText = startDateMod;
    viewEndDate.innerText = endDateMod;

    // console.log({eventToView:eventToView._def});
    if (eventToView.groupId == 4) {
      viewAuthority.classList.remove("d-none");
      let authorityBinded = authorityData.find(
        (authObj) => authObj.id == eventToView.extendedProps.authority_id
      );
      viewAuthLogo.src = `assets/media/authorities/${authorityBinded.logo}.png`;
      viewAuthName.innerText = authorityBinded.name;
    }
  };

  viewElement.addEventListener("hidden.bs.modal", () => {
    viewAuthority.classList.add("d-none");
    viewAuthLogo.src = ``;
    viewAuthName.innerText = '';
  })

  // Handle delete event
  const handleDeleteEvent = () => {
    viewDeleteButton.addEventListener("click", (e) => {
      e.preventDefault();

      Swal.fire({
        text: "Anda pasti, anda ingin padam agenda ini?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, sila padam!",
        cancelButtonText: "Tidak, kembali",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          removeEvent(eventToUpdate.id);

          viewModal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Agenda anda tidak dipadam!.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, baik!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Populate form
  const populateForm = (data) => {
    console.log({ populateFrom: data.groupId });
    // check if current event is existing or new (temporarily use groupId since it is a required parameter for any event)
    if (data.groupId == '4') {

      // form setup
      var addSvEl = form.querySelector(".card-body");
      categoryDiv = addSvEl.querySelector('[name="form-event-category"]');
      guestDiv = addSvEl.querySelector('[name="form-event-guest"]');

      // hide category and guest
      categoryDiv.classList.add("d-none");
      guestDiv.classList.add("d-none");

      // Create a new div element
      var newDiv = document.createElement("div");

      // Set the class attribute
      newDiv.setAttribute("class", "fv-row mb-9");

      // Set the name attribute
      newDiv.setAttribute("name", "form-event-auth");

      // get suggested authority id
      api
        .get("calendar/suggestion/" + sid)
        .then((response) => {
          console.log({ authSuggestData: response });
          // Generate badge for authority listed by gis
          // Parse authID string into array of integers

          // Render span for authority passed
          var spanElement = "";
          response.authoritySuggest.forEach((element) => {
            if (element.calendar_id == null) {
              var spanTemplate = `<span class="badge bg-primary-subtle m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}
              <span class="badge bg-warning position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="width: 2px; height: 2px;">
              &nbsp;
            </span></span>`;
            } else if (element.calendar_id !== null) {
              var spanTemplate = `<span class="badge bg-primary-subtle m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}
              <span class="badge bg-success position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="width: 2px; height: 2px;">
                &nbsp;
              </span></span>`;
            } else {
              var spanTemplate = `<span class="badge bg-warning m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}</span>`;
              console.log("error in authoritySuggest data.");
            }
            spanElement += spanTemplate;
          });

          // Add some content to the new div
          newDiv.innerHTML = `<!--begin::Label-->
          <label class="fs-6 fw-semibold required mb-2">Pihak Berkuasa</label>
          <div class="mb-2">
          ${spanElement}
          </div>
          <!--end::Label-->
          <!--begin::Input-->
          <select class="form-select form-select-solid" id="calendar_auth_id" name="calendar_auth_id" required>
          <option></option>
          </select>
          <!--end::Input-->`;

          // Insert the new div before the existing element
          addSvEl.insertBefore(newDiv, guestDiv.nextSibling);

          // Assign newly added div as temporary element to be remove when modal is closed
          tempEl = guestDiv.nextSibling;

          // Init select2 instance
          eventAuthority = $("#calendar_auth_id"); // use jquery because it dependencies

          // Render category options passed
          var authOption = [];
          authorityData.forEach((element) => {
            const option = {
              id: element.id,
              text: element.sort_name,
            };
            authOption.push(option);
          });

          // init select2 instance
          eventAuthority.select2({
            data: authOption,
            placeholder: "Sila pilih pihak berkuasa terlibat",
          });

          // set select2 value
          eventAuthority.val(data.extendedProps.authority_id).trigger('change');

          // Add badge click event
          var authBadgeEl = addSvEl.querySelectorAll(
            '[name="event-auth-suggest"]'
          );
          authBadgeEl.forEach(function (badge) {
            badge.addEventListener("click", function () {
              if (
                this.dataset.cal ==
                "null" /* check for 'null' instead of null because of dataset passing constraint */
              ) {
                // assign selected value to the event authority select2
                var value = this.dataset.auth;
                eventAuthority.val(value).trigger("change");
              } else {
                let calId = this.dataset.cal;
                Swal.fire({
                  text: "Tetapan lawatan tapak bagi pihak berkuasa ini telah ditetapkan! Lihat maklumatnya?",
                  icon: "warning",
                  showCancelButton: true,
                  buttonsStyling: false,
                  confirmButtonText: "Ya! paparkan",
                  cancelButtonText: "Tidak, kembali",
                  customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light",
                  },
                }).then(function (result) {
                  if (result.value) {
                    // show date that had been set
                    // generate new variable to hold event data according to fullcalendar eventclick standard in order for it to be able to handle by the handleViewEvent function
                    let info = {};
                    info.event = calendar.getEventById(calId);
                    handleViewEvent(info);
                  } else if (result.dismiss === "cancel") {
                    Swal.fire({
                      text: "Anda boleh sambung penetapan agenda.",
                      icon: "info",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, baik!",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                    });
                  }
                });
              }
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });

      // assign response data to the eventName
      eventName.value = noRef ? "Lawatan Tapak : " + noRef : "";
      eventName.innerHTML = noRef ? "Lawatan Tapak : " + noRef : "";
      eventName.disabled = true;

      // [svOnly] disable category selection and supply fixed value
      // instead of clear existing selected value for general calendar
      // eventCategory.prop("disabled", true);
      eventCategory.val("4").trigger("change");
      // [svOnly] disable guest selection and supply fixed value
      // instead of clear existing selected value for general calendar
      // eventGuest.prop("disabled", true);
      eventGuest.val(getUserId(currentUser, usersData)).trigger("change");
      // Check if the event contains guest data or not
      if (data.extendedProps && data.extendedProps.guests) {
        // assign new selected value if not null
        if (
          data.extendedProps.guests != null ||
          data.extendedProps.guests != "" ||
          data.extendedProps.guests != undefined
        ) {
          eventGuest
            .val(data.extendedProps.guests.split(","))
            .trigger("change");
        } else {
          console.log("data.extendedProps.guests value error");
        }
      } else {
        console.log("event do not have any guest data passed");
      }
      // assign event description
      eventDescription.value = data.extendedProps.description
        ? data.extendedProps.description
        : "";
      // assign event location
      eventLocation.value = data.extendedProps.location
        ? data.extendedProps.location
        : "";
      // console.log(data.startStr);
      if (data.startStr != "") {
        startFlatpickr.setDate(data.startStr, true, "Y-m-d");

        // Handle null end dates
        const endDate = data.endStr
          ? data.endStr
          : moment(data.startStr).format();
        endFlatpickr.setDate(endDate, true, "Y-m-d");
      }

      const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
      const datepickerWrappers = form.querySelectorAll(
        '[data-kt-calendar="datepicker"]'
      );
      if (data.allDay) {
        allDayToggle.checked = true;
        datepickerWrappers.forEach((dw) => {
          dw.classList.add("d-none");
        });
      } else {
        if (data.startStr != "" || data.endStr != "" || data.startStr != "") {
          startTimeFlatpickr.setDate(data.startStr, true, "Y-m-d H:i");
          endTimeFlatpickr.setDate(data.endStr, true, "Y-m-d H:i");
          endFlatpickr.setDate(data.startStr, true, "Y-m-d");
        }
        allDayToggle.checked = false;
        datepickerWrappers.forEach((dw) => {
          dw.classList.remove("d-none");
        });
      }
    } else {
      // form setup
      var addSvEl = form.querySelector(".card-body");
      categoryDiv = addSvEl.querySelector('[name="form-event-category"]');
      guestDiv = addSvEl.querySelector('[name="form-event-guest"]');

      // hide category and guest
      categoryDiv.classList.add("d-none");
      guestDiv.classList.add("d-none");

      // Create a new div element
      var newDiv = document.createElement("div");

      // Set the class attribute
      newDiv.setAttribute("class", "fv-row mb-9");

      // Set the name attribute
      newDiv.setAttribute("name", "form-event-auth");

      // get suggested authority id
      api
        .get("calendar/suggestion/" + sid)
        .then((response) => {
          console.log({ authSuggestData: response });
          // Generate badge for authority listed by gis
          // Parse authID string into array of integers

          // Render span for authority passed
          var spanElement = "";
          response.authoritySuggest.forEach((element) => {
            if (element.calendar_id == null) {
              var spanTemplate = `<span class="badge bg-primary-subtle m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}
              <span class="badge bg-warning position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="width: 2px; height: 2px;">
              &nbsp;
            </span></span>`;
            } else if (element.calendar_id !== null) {
              var spanTemplate = `<span class="badge bg-primary-subtle m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}
              <span class="badge bg-success position-absolute top-0 start-100 translate-middle p-1 rounded-circle" style="width: 2px; height: 2px;">
                &nbsp;
              </span></span>`;
            } else {
              var spanTemplate = `<span class="badge bg-warning m-1 position-relative" name="event-auth-suggest" data-auth="${element.id}" data-cal="${element.calendar_id}" style="cursor: pointer;">${element.sort_name}</span>`;
              console.log("error in authoritySuggest data.");
            }
            spanElement += spanTemplate;
          });

          // Add some content to the new div
          newDiv.innerHTML = `<!--begin::Label-->
          <label class="fs-6 fw-semibold required mb-2">Pihak Berkuasa</label>
          <div class="mb-2">
          ${spanElement}
          </div>
          <!--end::Label-->
          <!--begin::Input-->
          <select class="form-select form-select-solid" id="calendar_auth_id" name="calendar_auth_id" required>
          <option></option>
          </select>
          <!--end::Input-->`;

          // Insert the new div before the existing element
          addSvEl.insertBefore(newDiv, guestDiv.nextSibling);

          // Assign newly added div as temporary element to be remove when modal is closed
          tempEl = guestDiv.nextSibling;

          // Init select2 instance
          eventAuthority = $("#calendar_auth_id"); // use jquery because it dependencies

          // Render category options passed
          var authOption = [];
          authorityData.forEach((element) => {
            const option = {
              id: element.id,
              text: element.sort_name,
            };
            authOption.push(option);
          });

          // init select2 instance
          eventAuthority.select2({
            data: authOption,
            placeholder: "Sila pilih pihak berkuasa terlibat",
          });

          // Add badge click event
          var authBadgeEl = addSvEl.querySelectorAll(
            '[name="event-auth-suggest"]'
          );
          authBadgeEl.forEach(function (badge) {
            badge.addEventListener("click", function () {
              if (
                this.dataset.cal ==
                "null" /* check for 'null' instead of null because of dataset passing constraint */
              ) {
                // assign selected value to the event authority select2
                var value = this.dataset.auth;
                eventAuthority.val(value).trigger("change");
              } else {
                let calId = this.dataset.cal;
                Swal.fire({
                  text: "Tetapan lawatan tapak bagi pihak berkuasa ini telah ditetapkan! Lihat maklumatnya?",
                  icon: "warning",
                  showCancelButton: true,
                  buttonsStyling: false,
                  confirmButtonText: "Ya! paparkan",
                  cancelButtonText: "Tidak, kembali",
                  customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light",
                  },
                }).then(function (result) {
                  if (result.value) {
                    // show date that had been set
                    // generate new variable to hold event data according to fullcalendar eventclick standard in order for it to be able to handle by the handleViewEvent function
                    let info = {};
                    info.event = calendar.getEventById(calId);
                    handleViewEvent(info);
                  } else if (result.dismiss === "cancel") {
                    Swal.fire({
                      text: "Anda boleh sambung penetapan agenda.",
                      icon: "info",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, baik!",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                    });
                  }
                });
              }
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });

      // assign response data to the eventName
      eventName.value = noRef ? "Lawatan Tapak : " + noRef : "";
      eventName.innerHTML = noRef ? "Lawatan Tapak : " + noRef : "";
      eventName.disabled = true;

      // [svOnly] disable category selection and supply fixed value
      // instead of clear existing selected value for general calendar
      // eventCategory.prop("disabled", true);
      eventCategory.val("4").trigger("change");
      // [svOnly] disable guest selection and supply fixed value
      // instead of clear existing selected value for general calendar
      // eventGuest.prop("disabled", true);
      eventGuest.val(getUserId(currentUser, usersData)).trigger("change");
      // Check if the event contains guest data or not
      if (data.extendedProps && data.extendedProps.guests) {
        // assign new selected value if not null
        if (
          data.extendedProps.guests != null ||
          data.extendedProps.guests != "" ||
          data.extendedProps.guests != undefined
        ) {
          eventGuest
            .val(data.extendedProps.guests.split(","))
            .trigger("change");
        } else {
          console.log("data.extendedProps.guests value error");
        }
      } else {
        console.log("event do not have any guest data passed");
      }
      // assign event description
      eventDescription.value = data.extendedProps.description
        ? data.extendedProps.description
        : "";
      // assign event location
      eventLocation.value = data.extendedProps.location
        ? data.extendedProps.location
        : "";
      // console.log(data.startStr);
      if (data.startStr != "") {
        startFlatpickr.setDate(data.startStr, true, "Y-m-d");

        // Handle null end dates
        const endDate = data.endStr
          ? data.endStr
          : moment(data.startStr).format();
        endFlatpickr.setDate(endDate, true, "Y-m-d");
      }

      const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
      const datepickerWrappers = form.querySelectorAll(
        '[data-kt-calendar="datepicker"]'
      );
      if (data.allDay) {
        allDayToggle.checked = true;
        datepickerWrappers.forEach((dw) => {
          dw.classList.add("d-none");
        });
      } else {
        if (data.startStr != "" || data.endStr != "" || data.startStr != "") {
          startTimeFlatpickr.setDate(data.startStr, true, "Y-m-d H:i");
          endTimeFlatpickr.setDate(data.endStr, true, "Y-m-d H:i");
          endFlatpickr.setDate(data.startStr, true, "Y-m-d");
        }
        allDayToggle.checked = false;
        datepickerWrappers.forEach((dw) => {
          dw.classList.remove("d-none");
        });
      }
    }
  };

  // Handle edit events
  var handleEditEvent = () => {
    // TODO: Check if the current event is for current sv
    console.log(eventToUpdate);
    if (eventToUpdate.groupId == 4) {
      // Update modal title
      modalTitle.innerText = "Kemaskini Tarikh Lawatan Tapak";
      // Change button
      submitButton.innerHTML = "Kemaskini";
      submitButton.classList.add("btn-update-event");
      submitButton.classList.remove("btn-add-event");

      modal.show();

      // Select datepicker wrapper elements
      const datepickerWrappers = form.querySelectorAll(
        '[data-kt-calendar="datepicker"]'
      );

      // Handle all day toggle
      const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
      allDayToggle.addEventListener("click", (e) => {
        if (e.target.checked) {
          datepickerWrappers.forEach((dw) => {
            dw.classList.add("d-none");
          });
        } else {
          endFlatpickr.setDate(eventToUpdate.startDate, true, "Y-m-d");
          datepickerWrappers.forEach((dw) => {
            dw.classList.remove("d-none");
          });
        }
      });

      populateForm(eventToUpdate);
    } else {
      // Update modal title
      modalTitle.innerText = "Edit an Event";
      // change Submit button
      submitButton.innerHTML = "Update";
      submitButton.classList.add("btn-update-event");
      submitButton.classList.remove("btn-add-event");

      modal.show();

      // Select datepicker wrapper elements
      const datepickerWrappers = form.querySelectorAll(
        '[data-kt-calendar="datepicker"]'
      );

      // Handle all day toggle
      const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
      allDayToggle.addEventListener("click", (e) => {
        if (e.target.checked) {
          datepickerWrappers.forEach((dw) => {
            dw.classList.add("d-none");
          });
        } else {
          endFlatpickr.setDate(eventToUpdate.startDate, true, "Y-m-d");
          datepickerWrappers.forEach((dw) => {
            dw.classList.remove("d-none");
          });
        }
      });

      populateForm(eventToUpdate);

      // Not allowed to update other events accept for this sv
      Swal.fire({
        text: "Anda tidak dibenarkan mengubah agenda ini melalui paparan tetapan lawatan tapak.",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Baik, Faham!",
        customClass: {
          confirmButton: "btn btn-primary",
        },
      }).then(function (result) {
        if (result.value) {
          form.reset(); // Reset form
          modal.hide(); // Hide modal
        }
      });
    }
  };

  // Handle edit button
  var handleEditButton = () => {
    viewEditButton.addEventListener("click", (e) => {
      e.preventDefault();

      viewModal.hide();
      handleEditEvent();
    });
  };

  // Handle cancel button
  const handleCancelButton = () => {
    // Edit event modal cancel button
    cancelButton.addEventListener("click", function (e) {
      e.preventDefault();

      Swal.fire({
        text: "Adakah anda pasti untuk batalkan?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, batalkan!",
        cancelButtonText: "Tidak, kembali",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          form.reset(); // Reset form
          modal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Maklumat agenda tidak dibatalkan.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, baik!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Handle close button
  const handleCloseButton = () => {
    // Edit event modal close button
    closeButton.addEventListener("click", function (e) {
      e.preventDefault();

      Swal.fire({
        text: "Adakah anda pasti untuk batalkan?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, batalkan!",
        cancelButtonText: "Tidak, kembali",
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: "btn btn-active-light",
        },
      }).then(function (result) {
        if (result.value) {
          form.reset(); // Reset form
          modal.hide(); // Hide modal
        } else if (result.dismiss === "cancel") {
          Swal.fire({
            text: "Maklumat agenda tidak dibatalkan.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, baik!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      });
    });
  };

  // Modify sidebar toggler
  function modifyToggler() {
    const fcSidebarToggleButton = document.querySelector(
      ".fc-sidebarToggle-button"
    );
    fcSidebarToggleButton.classList.remove("fc-button-primary");
    fcSidebarToggleButton.classList.add("d-lg-none", "d-inline-block", "ps-0");
    while (fcSidebarToggleButton.firstChild) {
      fcSidebarToggleButton.firstChild.remove();
    }
    fcSidebarToggleButton.setAttribute("data-bs-toggle", "sidebar");
    fcSidebarToggleButton.setAttribute("data-overlay", "");
    fcSidebarToggleButton.setAttribute("data-target", "#app-calendar-sidebar");
    fcSidebarToggleButton.insertAdjacentHTML(
      "beforeend",
      '<i class="ti ti-menu-2 ti-sm"></i>'
    );
  }

  // Filter events by calender
  function selectedCalendars() {
    let selected = [],
      filterInputChecked = [].slice.call(
        document.querySelectorAll(".input-filter:checked")
      );
    // console.log('filterInputElement',filterInputChecked); //temporary commented

    filterInputChecked.forEach((item) => {
      selected.push(item.getAttribute("data-value"));
      // console.log(selected); //temporary commented
    });

    return selected;
  }

  // fetch calendar events
  function fetchEvents(info, successCallback) {
    // Do event fetch api to update currentEvents data
    api
      .get("calendar/event")
      .then((response) => {
        // console.log(response.data);
        // assign response data to currentEvents to be view
        currentEvents = response.filter(
          (event) =>
            event.groupId != 3 ||
            (event.groupId == 3 && event.creator == currentUser)
        );

        let calendars = selectedCalendars(); // assign selected calendar from function

        // We are reading event object from app-calendar-events.js file directly by including that file above app-calendar file.
        // You should make an API call, look into above commented API call for reference
        let selectedEvents = currentEvents.filter(function (event) {
          return calendars.includes("groupId-" + event.groupId);
        });
        // if (selectedEvents.length > 0) {
        successCallback(selectedEvents);
        // }
      })
      .catch((error) => {
        console.log(error);
      });
  }

  // calendar init variables
  var todayDate = moment().startOf("day"); /** get today date */
  var TODAY = todayDate.format("YYYY-MM-DD");

  // Init calendar --- more info: https://fullcalendar.io/docs/initialize-globals
  var calendar = new FullCalendar.Calendar(calendarEl, {
    locale: "ms",
    firstDay: 1,
    initialView: "dayGridMonth",
    events: fetchEvents,
    editable: true,
    dragScroll: true,
    dayMaxEvents: true, // allow "more" link when too many events
    eventResizableFromStart: true,
    customButtons: {
      sidebarToggle: {
        text: "Sidebar",
      },
    },
    headerToolbar: {
      left: "sidebarToggle prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
    },
    direction: direction,
    initialDate: TODAY,
    navLinks: true, // can click day/week names to navigate views
    eventClassNames: function ({ event: calendarEvent }) {
      const colorName = calendarsColor[calendarEvent._def.groupId];
      const publicId = calendarEvent._def.publicId;
      //   const colorName = calendarsColor[calendarEvent._def.extendedProps.eventCategory];
      // Background Color
      return ["fc-event-" + colorName,"event-custom-id-" + publicId];
    },
    selectable: true,
    selectMirror: true,
    dateClick: function (info) {
      // console.log('dateClick value',info);
      let clickedDate = moment(info.date).format("YYYY-MM-DD");
      // create new event control here, passed clicked date value is '+clickedDate;
      eventToAdd.startStr = clickedDate;
      handleNewEvent();
    },
    eventClick: function (info) {
      handleViewEvent(info);
    },
    datesSet: function (info) {
      // do some stuff
      modifyToggler();
      // console.log('datesSet',info);
      // datesSet trigger is done, do something here.');
    },
    viewDidMount: function (info) {
      // do some stuff
      modifyToggler();
      // console.log('viewDidMount',info);
      // viewDidMount trigger is done, do something here.');
    },
    eventReceive: function (info) {
      // console.log(info.event);
      alert("eventReceive trigger is done, do something here." + info.event);
    },
    eventAdd: function (info) {
      // console.log(info.event);
      alert("eventAdd trigger is done, do something here." + info.event);
    }
  });
  // Render calendar
  calendar.render();
  // Modify sidebar toggler
  modifyToggler();

  // Init formvalidation
  const fv = FormValidation.formValidation(form, {
    fields: {
      calendar_event_name: {
        validators: {
          notEmpty: {
            message: "Event name is required",
          },
        },
      },
      calendar_group_id: {
        validators: {
          notEmpty: {
            message: "Event category is required",
          },
        },
      },
      calendar_event_start_date: {
        validators: {
          notEmpty: {
            message: "Start date is required",
          },
        },
      },
      calendar_event_end_date: {
        validators: {
          notEmpty: {
            message: "End date is required",
          },
        },
      },
    },

    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap: new FormValidation.plugins.Bootstrap5({
        rowSelector: ".fv-row",
        eleInvalidClass: "",
        eleValidClass: "",
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      // autoFocus: new FormValidation.plugins.AutoFocus()
    },
  })
    .on("core.form.valid", function () {
      // Jump to the next step when all fields in the current step are valid
      isFormValid = true;
      // console.log('validation status', isFormValid);
    })
    .on("core.form.invalid", function () {
      // if fields are invalid
      isFormValid = false;
      // console.log('validation status', isFormValid);
    });

  // Handle add button
  // addButton will be used as wizard finish button for sitevisit page // original script can be obtained from calendar.js
  const handleAddButton = () => {
    addButton.addEventListener("click", (e) => {
      // Check all sitevisit set by the user
      // get element body to insert new overview modal
      var parentBody = viewElement.parentNode;

      // set overview modal
      var overviewModalEl = document.createElement("div");
      overviewModalEl.setAttribute("class", "modal fade");
      overviewModalEl.setAttribute("id", "sv_calendar_overview");
      overviewModalEl.setAttribute("tabindex", "-1");
      overviewModalEl.setAttribute("aria-hidden", "true");
      overviewModalEl.innerHTML = `<!--begin::Modal dialog-->
          <div class="modal-dialog modal-dialog-centered mw-700px">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title">Rumusan Lawatan Tapak</h3>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-2"></i>
                </div>
                <!--end::Close-->
              </div>
              <div class="modal-body mx-5 mx-xl-10 pt-0 pb-5">
              <div class="position-relative ps-4 py-1 my-5">
                        <div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-primary">
                        </div>
                <div class=" my-1">
                  <div class="fw-semibold fs-5"><strong>${reportType == 1
          ? "Lawatan Tapak Awalan"
          : reportType == 2
            ? "Lawatan Tapak Bersama"
            : reportType == 3
              ? "Lawatan Tapak Berkala"
              : ""
        }</strong> : ${noRef}</div>
                </div>
                </div>

          <div class="accordion accordion-icon-collapse" id="kt_accordion_calendar">
                <div class="mh-350px scroll-y me-n7 pe-7" id="event_overview_container">
                </div>

          </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-sv-calendar="confirm">Selesai</button>
              </div>
            </div>
          </div>`;

      // add overview modal to body
      parentBody.appendChild(overviewModalEl);

      // get event-overview-container
      var overviewContainer = document.getElementById(
        "event_overview_container"
      );
      var eventToBeShow = [];

      currentEvents.forEach(function (element) {
        if (
          element.system_id == systemId &&
          element.report_type == reportType
        ) {
          // Detect all day event
          var eventNameMod;
          var startDateMod;
          var endDateMod;

          // Generate labels
          if (element.allDay) {
            eventNameMod = "All Day";
            startDateMod = moment(element.start).format("Do MMM, YYYY");
            endDateMod = moment(element.end).format("Do MMM, YYYY");
          } else {
            eventNameMod = "";
            startDateMod = moment(element.start).format(
              "Do MMM, YYYY - h:mm a"
            );
            endDateMod = moment(element.end).format("Do MMM, YYYY - h:mm a");
          }

          // get authority data related to this element
          var authorityBinded = authorityData.find(
            (authObj) => authObj.id == element.authority_id
          );

          // insert into overview modal
          eventToBeShow.push({
            element: `<!--begin::Accordion-->
            <!--begin::Item-->
            <div class="mb-3">
              <!--begin::Header-->
              <div class="d-flex flex-row justify-content-between">
                <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_${authorityBinded.id
              }">

                  <span class="accordion-icon">
                    <i class="ki-duotone ki-plus-square fs-3 accordion-icon-off"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <i class="ki-duotone ki-minus-square fs-3 accordion-icon-on"><span class="path1"></span><span class="path2"></span></i>


                  <div class="d-flex flex-row ms-5">
                    <div class="symbol symbol-35px d-flex align-items-center">
                      <img src="assets/media/authorities/${authorityBinded.logo
              }.png" alt="">
                    </div>
                    <div class="ms-5 mt-1 d-flex flex-column  align-items-start">
                        <span class="text-dark fw-bold text-hover-primary fs-5 me-4 mt-1 accordion-icon-on">${authorityBinded.name
              }</span>
                        <span class="text-dark fw-bold text-hover-primary fs-5 me-4 accordion-icon-off">${authorityBinded.name
              }</span>
                        <span class="fs-8 accordion-icon-off" data-kt-calendar="event_start_date">${startDateMod}</span>
                    </div>
                  </div>
                  </span>
                </div>
                <button id="btn_edit_${element.authority_id}" class="btn btn-sm btn-light-primary my-4">Kemaskini</button>
              </div>
              <!--end::Header-->

              <!--begin::Body-->
              <div id="kt_accordion_${authorityBinded.id
              }" class="collapse fs-6 ps-10" data-bs-parent="#kt_accordion_calendar">
                <div class="d-flex flex-column mt-3">



                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                    <i class="fad fa-calendar-clock text-muted fs-5 me-3"></i>
                    <div class="fs-6 me-3 fw-light">
                      <span data-kt-calendar="event_start_date">${startDateMod}</span>
                    </div>
                    <i class="fad fa-arrow-right text-muted fs-5 me-3"></i>
                    <div class="fs-6 fw-light">
                      <span data-kt-calendar="event_end_date">${endDateMod}</span>
                    </div>
                    </div>

                    <div class="d-flex align-items-start">
                      <span class="badge badge-light-success" data-kt-calendar="all_day">${eventNameMod}</span>
                    </div>

                  </div>

                  <div class="d-flex align-items-center mb-3">
                    <i class="fad fa-location-dot text-muted fs-5 me-5"></i>
                    <div class="fs-6 fw-light" data-kt-calendar="event_location">${element.location ? element.location : "--"
              }</div>
                  </div>

                  <div class="d-flex align-items-start">
                    <i class="far fa-calendar-lines-pen text-muted fs-5 me-3 mt-1"></i>
                    <div class="mb-9 flex-grow-1">
                        <div class="fs-6 fw-light" data-kt-calendar="event_description">${element.description ? element.description : "--"
              }</div>
                    </div>



                  </div>


                </div>
              </div>
              <!--end::Body-->
            </div>
            <!--end::Item-->
          <!--end::Accordion-->
          `, data: element
          });
        }
      });

      // init modal
      var overviewModal = new bootstrap.Modal(overviewModalEl);
      var overviewConfirm = document.querySelector(
        '[data-sv-calendar="confirm"]'
      );

      eventToBeShow.forEach((event) => {
        overviewContainer.innerHTML += event.element;
      });

      // check currentEvents to get events under the same systemId and reportType

      // Now that all buttons are in the DOM, let's add event listeners
      eventToBeShow.forEach((event) => {
        let editButton = overviewContainer.querySelector(`#btn_edit_${event.data.authority_id}`);
        let eventToBeClick = document.querySelector(`.event-custom-id-${event.data.id}`);

        editButton.addEventListener("click", (e) => {
          overviewModal.hide(); // Hide the modal
          eventToBeClick.click();
        });
      });

      overviewModal.show();

      overviewModalEl.addEventListener("hide.bs.modal", function (event) {
        // Removing the modal element from the DOM.
        overviewModalEl.remove();
      });

      overviewConfirm.addEventListener("click", (e) => {
        Swal.fire({
          text: "Anda pasti, untuk teruskan? Amaran, Mengubah Senarai Pihak Berkuasa Terlibat Pada Kalendar Turut Mengubah Senarai Laporan Lawatan Tapak.",
          icon: "warning",
          showCancelButton: true,
          buttonsStyling: false,
          confirmButtonText: "Ya, saya pasti!",
          cancelButtonText: "Tidak, kembali",
          customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-active-light",
          },
        }).then(function (result) {
          if (result.value) {
            addButton.setAttribute("data-kt-indicator", "on");
            addButton.disabled = true;
            // if user clicked yes
            overviewModal.hide(); // Hide the modal

            // api request to proceed action
            let payload = {
              item: "action-010",
              sid: systemId,
              rt: reportType,
            };
            api
              .post(`tasks/010/submit`, payload)
              .then((response) => {
                // check if task update succeeded
                console.log(response);

                addButton.removeAttribute("data-kt-indicator");
                addButton.disabled = false;

                if (response.message == "success") {
                  // setup toaster
                  toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toastr-bottom-right",
                    preventDuplicates: true,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "2000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                  };

                  toastr.success("Tahniah, Tetapan lawatan tapak berjaya! 🎉");

                  // send to calendar page
                  setTimeout(function () {
                    location.href = "operation/general/tasks/new";
                  }, 2500);
                } else {
                  console.log(
                    "Error, there are error in status update.",
                    response
                  );
                  Swal.fire({
                    text: "Terdapat ralat dalam proses kemaskini maklumat tugasan! Kembali ke paparan tugasan?.",
                    icon: "error",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, kembali!",
                    cancelButtonText: "Tidak, kekalkan!",
                    customClass: {
                      confirmButton: "btn btn-primary",
                      cancelButton: "btn btn-active-light",
                    },
                  }).then(function (result) {
                    if (result.value) {
                      // if user clicked yes
                      location.href = "/tasks/new";
                    } else if (result.dismiss === "cancel") {
                    }
                  });
                }
              })
              .catch((error) => {
                addButton.removeAttribute("data-kt-indicator");
                addButton.disabled = false;
                console.log(error);
              });
          } else if (result.dismiss === "cancel") {
            Swal.fire({
              text: "Anda boleh sambung tetapan lawatan tapak!.",
              icon: "info",
              buttonsStyling: false,
              confirmButtonText: "Ok, baik!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
            }).then(function (result) {
              if (result.value) {
                // if user clicked yes
                overviewModal.hide(); // Hide the modal
              } else if (result.dismiss === "cancel") {
              }
            });
          }
        });
      });
    });
  };

  // Handle add new event
  const handleNewEvent = () => {
    // Update modal title
    modalTitle.innerText = "Set Tarikh Lawatan Tapak";
    // Change button
    submitButton.innerHTML = "Tambah";
    submitButton.classList.add("btn-add-event");
    submitButton.classList.remove("btn-update-event");

    modal.show();

    // Select datepicker wrapper elements
    const datepickerWrappers = form.querySelectorAll(
      '[data-kt-calendar="datepicker"]'
    );

    // Handle all day toggle
    const allDayToggle = form.querySelector("#kt_calendar_datepicker_allday");
    allDayToggle.addEventListener("click", (e) => {
      if (e.target.checked) {
        datepickerWrappers.forEach((dw) => {
          dw.classList.add("d-none");
        });
      } else {
        endFlatpickr.setDate(eventToAdd.startStr, true, "Y-m-d");
        datepickerWrappers.forEach((dw) => {
          dw.classList.remove("d-none");
        });
      }
    });

    populateForm(eventToAdd);
  };

  // Add new event
  // ------------------------------------------------
  const handleSubmitButton = () => {
    submitButton.addEventListener("click", (e) => {
      e.preventDefault();
      if (submitButton.classList.contains("btn-add-event")) {
        if (isFormValid) {
          console.log("form add valid");

          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable submit button whilst loading
          submitButton.disabled = true;

          // check if form contains tempEl which is created for site visit calendar only
          if (tempEl !== null) {
            if (tempEl.getAttribute("name") === "form-event-auth") {
              // Detect if is all day event
              let allDayEvent = false;
              if (allDayToggle.checked) {
                allDayEvent = true;
              }
              if (startTimeFlatpickr.selectedDates.length === 0) {
                allDayEvent = true;
              }

              // Merge date & time
              var startDateTime = moment(
                startFlatpickr.selectedDates[0]
              ).format();
              var endDateTime = moment(
                endFlatpickr.selectedDates[
                endFlatpickr.selectedDates.length - 1
                ]
              ).format();
              if (!allDayEvent) {
                const startDate = moment(
                  startFlatpickr.selectedDates[0]
                ).format("YYYY-MM-DD");
                const endDate = startDate;
                const startTime = moment(
                  startTimeFlatpickr.selectedDates[0]
                ).format("HH:mm:ss");
                const endTime = moment(
                  endTimeFlatpickr.selectedDates[0]
                ).format("HH:mm:ss");

                startDateTime = startDate + "T" + startTime;
                endDateTime = endDate + "T" + endTime;
              }

              let newEvent = {
                id: "",
                title: eventName.value,
                groupId: eventCategory.val(),
                allDay: allDayEvent,
                start: startDateTime,
                end: endDateTime,
                url: "",
                creator: currentUser,
                guests: eventGuest.val().join(","),
                description: eventDescription.value,
                location: eventLocation.value,
                system_id: systemId,
                authority_id: eventAuthority.val(),
                report_type: reportType,
              };

              // Add new event to calendar
              addEvent(newEvent);
            }
          } else {
            // Detect if is all day event
            let allDayEvent = false;
            if (allDayToggle.checked) {
              allDayEvent = true;
            }
            if (startTimeFlatpickr.selectedDates.length === 0) {
              allDayEvent = true;
            }

            // Merge date & time
            var startDateTime = moment(
              startFlatpickr.selectedDates[0]
            ).format();
            var endDateTime = moment(
              endFlatpickr.selectedDates[endFlatpickr.selectedDates.length - 1]
            ).format();
            if (!allDayEvent) {
              const startDate = moment(startFlatpickr.selectedDates[0]).format(
                "YYYY-MM-DD"
              );
              const endDate = startDate;
              const startTime = moment(
                startTimeFlatpickr.selectedDates[0]
              ).format("HH:mm:ss");
              const endTime = moment(endTimeFlatpickr.selectedDates[0]).format(
                "HH:mm:ss"
              );

              startDateTime = startDate + "T" + startTime;
              endDateTime = endDate + "T" + endTime;
            }

            let newEvent = {
              id: "",
              title: eventName.value,
              groupId: eventCategory.val(),
              allDay: allDayEvent,
              start: startDateTime,
              end: endDateTime,
              url: "",
              creator: currentUser,
              guests: eventGuest.val().join(","),
              description: eventDescription.value,
              location: eventLocation.value,
            };

            // Add new event to calendar
            addEvent(newEvent);
          }

          // Simulate form submission
          setTimeout(function () {
            // Simulate form submission
            submitButton.removeAttribute("data-kt-indicator");

            // Show popup confirmation
            Swal.fire({
              text: "Agenda baru ditambah ke kalendar!",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, baik!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
            }).then(function (result) {
              if (result.isConfirmed) {
                modal.hide();

                // Enable submit button after loading
                submitButton.disabled = false;

                // Reset form for demo purposes only
                form.reset();
              }
            });

            //form.submit(); // Submit form
          }, 2000);
        } else {
          console.log("form add not valid");
          // Show popup warning
          Swal.fire({
            text: "Maaf, terdapat ralat dikesan, sila cuba lagi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, baik!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      } else {
        // Update event
        // ------------------------------------------------
        if (isFormValid) {
          console.log("form edit valid");

          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable submit button whilst loading
          submitButton.disabled = true;

          // check if form contains tempEl which is created for site visit calendar only
          if (tempEl !== null) {
            if (tempEl.getAttribute("name") === "form-event-auth") {
              // Detect if is all day event
              let allDayEvent = false;
              if (allDayToggle.checked) {
                allDayEvent = true;
              }
              if (startTimeFlatpickr.selectedDates.length === 0) {
                allDayEvent = true;
              }

              // Merge date & time
              var startDateTime = moment(
                startFlatpickr.selectedDates[0]
              ).format();
              var endDateTime = moment(
                endFlatpickr.selectedDates[
                endFlatpickr.selectedDates.length - 1
                ]
              ).format();
              if (!allDayEvent) {
                const startDate = moment(
                  startFlatpickr.selectedDates[0]
                ).format("YYYY-MM-DD");
                const endDate = startDate;
                const startTime = moment(
                  startTimeFlatpickr.selectedDates[0]
                ).format("HH:mm:ss");
                const endTime = moment(
                  endTimeFlatpickr.selectedDates[0]
                ).format("HH:mm:ss");

                startDateTime = startDate + "T" + startTime;
                endDateTime = endDate + "T" + endTime;
              }

              let eventData = {
                id: eventToUpdate.id,
                title: eventName.value,
                groupId: eventCategory.val(),
                allDay: allDayEvent,
                start: startDateTime,
                end: endDateTime,
                url: "",
                creator: currentUser,
                guests: eventGuest.val().join(","),
                description: eventDescription.value,
                location: eventLocation.value,
                system_id: systemId,
                authority_id: eventAuthority.val(),
                report_type: reportType,
              };

              // Add new event to calendar
              updateEvent(eventData);
            }
          } else {
            // Detect if is all day event
            let allDayEvent = false;
            if (allDayToggle.checked) {
              allDayEvent = true;
            }
            if (startTimeFlatpickr.selectedDates.length === 0) {
              allDayEvent = true;
            }

            // Merge date & time
            var startDateTime = moment(
              startFlatpickr.selectedDates[0]
            ).format();
            var endDateTime = moment(
              endFlatpickr.selectedDates[
              endFlatpickr.selectedDates.length - 1
              ]
            ).format();
            if (!allDayEvent) {
              const startDate = moment(
                startFlatpickr.selectedDates[0]
              ).format("YYYY-MM-DD");
              const endDate = startDate;
              const startTime = moment(
                startTimeFlatpickr.selectedDates[0]
              ).format("HH:mm:ss");
              const endTime = moment(
                endTimeFlatpickr.selectedDates[0]
              ).format("HH:mm:ss");

              startDateTime = startDate + "T" + startTime;
              endDateTime = endDate + "T" + endTime;
            }

            // Update event in calendar

            let eventData = {
              id: eventToUpdate.id,
              title: eventName.value,
              groupId: eventCategory.val(),
              allDay: allDayEvent,
              start: startDateTime,
              end: endDateTime,
              url: "",
              creator: currentUser,
              guests: eventGuest.val().join(","),
              description: eventDescription.value,
              location: eventLocation.value,
            };

            updateEvent(eventData);
          }

          // Simulate form submission
          setTimeout(function () {
            // Simulate form submission
            submitButton.removeAttribute("data-kt-indicator");

            // Show popup confirmation
            Swal.fire({
              text: "Agenda baru ditambah ke kalendar!",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok, baik!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
            }).then(function (result) {
              if (result.isConfirmed) {
                modal.hide();

                // Enable submit button after loading
                submitButton.disabled = false;

                // Remove old event
                // calendar.getEventById(data.id).remove();

                // Reset form for demo purposes only
                form.reset();
              }
            });

            //form.submit(); // Submit form
          }, 2000);
        } else {
          console.log("form edit not valid");

          // Show popup warning
          Swal.fire({
            text: "Maaf, terdapat ralat dikesan, sila cuba lagi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, baik!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
          });
        }
      }
    });
  };

  // handle drawer hide events
  const handleDrawerHide = () => {
    modal.on("kt.drawer.hide", function () {
      // remove temporary element if it meet condition
      // console.log(tempEl);
      if (tempEl !== null) {
        if (tempEl.getAttribute("name") === "form-event-auth") {
          tempEl.remove();
          tempEl = null;
        }
      }

      // redisplay category and guest
      if (categoryDiv !== null) {
        categoryDiv.classList.remove("d-none");
        categoryDiv = null;
      }

      if (guestDiv !== null) {
        guestDiv.classList.remove("d-none");
        guestDiv = null;
      }

      // reenable event title
      eventName.disabled = false;
    });
  };

  // Add Event
  // ------------------------------------------------
  function addEvent(eventData) {
    // ? Add new event data to current events object and refetch it to display on calender
    // send new event data to addEvent api
    api
      .post("calendar/" + sid, eventData)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To add event directly to calender (won't update currentEvents object)
    // calendar.addEvent(eventData);
  }

  // Update Event
  // ------------------------------------------------
  function updateEvent(eventData) {
    // ? Update existing event data to current events object and refetch it to display on calender
    // send new event data to updateEvent api
    // console.log('eventData to Update',eventData);
    api
      .put("calendar", eventData)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To update event directly to calender (won't update currentEvents object)
    // let propsToUpdate = ['id', 'title', 'url'];
    // let extendedPropsToUpdate = ['calendar', 'guests', 'location', 'description'];

    // updateEventInCalendar(eventData, propsToUpdate, extendedPropsToUpdate);
  }

  // Remove Event
  // ------------------------------------------------

  function removeEvent(eventId) {
    // ? Delete existing event data to current events object and refetch it to display on calender
    api
      .delete("calendar/" + eventId)
      .then((response) => {
        // console.log(response.data);
        calendar.refetchEvents();
      })
      .catch((error) => {
        console.log(error);
      });

    // ? To delete event directly to calender (won't update currentEvents object)
    // removeEventInCalendar(eventId);
  }

  // Calender filter functionality
  // ------------------------------------------------
  if (selectAll) {
    selectAll.addEventListener("click", (e) => {
      if (e.currentTarget.checked) {
        document
          .querySelectorAll(".input-filter")
          .forEach((c) => (c.checked = 1));
      } else {
        document
          .querySelectorAll(".input-filter")
          .forEach((c) => (c.checked = 0));
      }
      calendar.refetchEvents();
    });
  }

  if (filterInput) {
    filterInput.forEach((item) => {
      item.addEventListener("click", () => {
        document.querySelectorAll(".input-filter:checked").length <
          document.querySelectorAll(".input-filter").length
          ? (selectAll.checked = false)
          : (selectAll.checked = true);
        calendar.refetchEvents();
      });
    });
  }

  // Jump to date on sidebar(inline) calendar change
  inlineCalInstance.config.onChange.push(function (date) {
    calendar.changeView(
      calendar.view.type,
      moment(date[0]).format("YYYY-MM-DD")
    );
    modifyToggler();
    appCalendarSidebar.classList.remove("show");
    appOverlay.classList.remove("show");
  });

  // init all events
  initDatepickers();
  handleDeleteEvent();
  handleEditButton();
  handleCancelButton();
  handleCloseButton();
  handleAddButton();
  handleSubmitButton();
  handleDrawerHide();
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
  if (sid && rt) {
    // addButton will be used as wizard finish button for sitevisit page
    var finishButton = document.querySelector('[data-kt-calendar="button"]');
    finishButton.textContent = "Selesai";
  }
  // Get the init event data
  api
    .get("calendar")
    .then((response) => {
      // Do something with the response data
      console.log({ initResponse: response });
      if (sid && rt) {
        // init the site visit calendar
        console.log("parameters");
        svCalendarSetup(
          response.calendar_category,
          response.calendar_authority,
          response.calendar_user,
          response.calendar_event,
          response.current_user
        );
      } else {
        // init the general calendar
        console.log("no parameter");
        calendarSetup(
          response.calendar_category,
          response.calendar_user,
          response.calendar_event,
          response.current_user
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
